<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaEngineService
{
    protected string $baseUrl;

    protected string $secret;

    public function __construct()
    {
        $this->baseUrl = \App\Models\SystemSetting::get('wa_engine_url', config('services.wa_engine.url', env('WA_ENGINE_URL', 'http://127.0.0.1:3000')));
        $this->secret = \App\Models\SystemSetting::get('wa_engine_secret', config('services.wa_engine.secret', env('WA_ENGINE_SECRET', 'wagateway_secret_key_2026')));
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl.'/engine')
            ->withHeaders([
                'X-Engine-Secret' => $this->secret,
                'Accept' => 'application/json',
            ])
            ->timeout(15);
    }

    public function checkHealth(): array
    {
        try {
            $res = Http::baseUrl($this->baseUrl.'/engine')->get('/health');

            return $res->json() ?? ['status' => 'offline'];
        } catch (\Throwable $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    public function startSession(string $sessionId, string $method = 'qr', ?string $phoneNumber = null, bool $forceRestart = false): array
    {
        try {
            $payload = [
                'sessionId' => $sessionId,
                'method' => $method,
                'phoneNumber' => $phoneNumber,
                'forceRestart' => $forceRestart,
            ];

            $response = $this->client()->post('/session/start', $payload);

            return $response->json() ?? ['success' => false, 'message' => 'No response from engine'];
        } catch (\Throwable $e) {
            Log::error('WaEngineService startSession error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getSessionStatus(string $sessionId): array
    {
        try {
            $response = $this->client()->get("/session/{$sessionId}/status");

            return $response->json() ?? ['success' => false, 'message' => 'No response'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function logoutSession(string $sessionId): array
    {
        try {
            $response = $this->client()->post("/session/{$sessionId}/logout");

            return $response->json() ?? ['success' => false];
        } catch (\Throwable $e) {
            Log::error('WaEngineService logoutSession error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendTextMessage(string $sessionId, string $phone, string $message): array
    {
        try {
            $response = $this->client()->post('/message/send-text', [
                'sessionId' => $sessionId,
                'phone' => $phone,
                'message' => $message,
            ]);

            return $response->json() ?? ['success' => false, 'message' => 'Failed to send text'];
        } catch (\Throwable $e) {
            Log::error('WaEngineService sendTextMessage error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendMediaMessage(string $sessionId, string $phone, string $mediaUrl, string $mediaType = 'image', string $caption = '', string $fileName = 'file.pdf'): array
    {
        try {
            $response = $this->client()->post('/message/send-media', [
                'sessionId' => $sessionId,
                'phone' => $phone,
                'mediaUrl' => $mediaUrl,
                'mediaType' => $mediaType,
                'caption' => $caption,
                'fileName' => $fileName,
            ]);

            return $response->json() ?? ['success' => false, 'message' => 'Failed to send media'];
        } catch (\Throwable $e) {
            Log::error('WaEngineService sendMediaMessage error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendInteractiveMessage(string $sessionId, string $phone, array $payload): array
    {
        try {
            $response = $this->client()->post('/message/send-interactive', array_merge([
                'sessionId' => $sessionId,
                'phone' => $phone,
            ], $payload));

            return $response->json() ?? ['success' => false, 'message' => 'Failed to send interactive button message'];
        } catch (\Throwable $e) {
            Log::error('WaEngineService sendInteractiveMessage error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
