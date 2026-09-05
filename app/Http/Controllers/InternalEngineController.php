<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\Webhook;
use App\Services\UrlGuardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternalEngineController extends Controller
{
    public function handleEvent(Request $request)
    {
        $secret = $request->header('X-Engine-Secret');
        $expected = (string) config('services.wa_engine.secret');

        // Secret wajib dikonfigurasi & dibandingkan timing-safe
        if ($expected === '' || ! is_string($secret) || ! hash_equals($expected, $secret)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Engine Callback'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        Log::info("Engine event received: {$event}", ['data' => $data]);

        switch ($event) {
            case 'session.qr':
                if (! empty($data['sessionId']) && ! empty($data['qrCode'])) {
                    Device::where('session_id', $data['sessionId'])->update([
                        'qr_code' => $data['qrCode'],
                        'status' => 'qr_ready',
                    ]);
                }
                break;

            case 'session.pairing_code':
                if (! empty($data['sessionId']) && ! empty($data['pairingCode'])) {
                    Device::where('session_id', $data['sessionId'])->update([
                        'pairing_code' => $data['pairingCode'],
                        'status' => 'pairing_ready',
                    ]);
                }
                break;

            case 'session.connected':
                if (! empty($data['sessionId'])) {
                    $device = Device::where('session_id', $data['sessionId'])->first();
                    if ($device) {
                        $device->update([
                            'status' => 'connected',
                            'qr_code' => null,
                            'pairing_code' => null,
                            'phone_number' => $data['user']['phone'] ?? $device->phone_number,
                            'metadata' => $data['user'] ?? null,
                            'connected_at' => now(),
                        ]);

                        $device->user->logActivity('device.connected', "Device '{$device->name}' berhasil terhubung!");
                        $this->dispatchUserWebhook($device->user_id, 'device.connected', $data);
                    }
                }
                break;

            case 'session.disconnected':
                if (! empty($data['sessionId'])) {
                    $device = Device::where('session_id', $data['sessionId'])->first();
                    if ($device) {
                        $device->update([
                            'status' => 'disconnected',
                            'qr_code' => null,
                            'pairing_code' => null,
                        ]);

                        $device->user->logActivity('device.disconnected', "Device '{$device->name}' terputus.");
                        $this->dispatchUserWebhook($device->user_id, 'device.disconnected', $data);
                    }
                }
                break;

            case 'message.incoming':
                if (! empty($data['sessionId'])) {
                    $device = Device::where('session_id', $data['sessionId'])->first();
                    if ($device) {
                        $msg = Message::create([
                            'user_id' => $device->user_id,
                            'device_id' => $device->id,
                            'remote_jid' => $data['remoteJid'] ?? '',
                            'message_type' => $data['messageType'] ?? 'text',
                            'message_content' => $data['content'] ?? '',
                            'direction' => 'inbound',
                            'status' => 'delivered',
                            'wa_message_id' => $data['messageId'] ?? null,
                        ]);

                        $this->dispatchUserWebhook($device->user_id, 'message.received', [
                            'message_id' => $msg->id,
                            'device_id' => $device->id,
                            'remote_jid' => $data['remoteJid'] ?? '',
                            'content' => $data['content'] ?? '',
                            'push_name' => $data['pushName'] ?? '',
                            'timestamp' => $data['timestamp'] ?? time(),
                        ]);
                    }
                }
                break;
        }

        return response()->json(['success' => true]);
    }

    protected function dispatchUserWebhook(int $userId, string $event, array $payload): void
    {
        $webhook = Webhook::where('user_id', $userId)->where('is_active', true)->first();
        if (! $webhook || empty($webhook->target_url)) {
            return;
        }

        // Check if event is subscribed
        if (! empty($webhook->events) && ! in_array($event, $webhook->events) && ! in_array('*', $webhook->events)) {
            return;
        }

        // Guard SSRF: jangan kirim webhook ke URL internal/private
        try {
            UrlGuardService::assertSafeUrl($webhook->target_url);
        } catch (\Throwable $e) {
            Log::warning('Blocked SSRF-suspect webhook dispatch', ['url' => $webhook->target_url]);

            return;
        }

        try {
            Http::async()->withHeaders([
                'X-WAGateway-Event' => $event,
                'X-WAGateway-Secret' => $webhook->secret_key,
                'Content-Type' => 'application/json',
            ])->timeout(3)->post($webhook->target_url, [
                'event' => $event,
                'data' => $payload,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to async dispatch user webhook: '.$e->getMessage());
        }
    }
}
