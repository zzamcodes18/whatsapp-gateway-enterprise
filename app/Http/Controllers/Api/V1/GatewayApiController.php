<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\WaEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayApiController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function getDevices(Request $request): JsonResponse
    {
        $user = $request->user();
        $devices = $user->devices()->select('id', 'name', 'phone_number', 'connection_type', 'status', 'connected_at', 'created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    public function sendTextMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'min:8'],
            'message' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user->canSendMessage()) {
            return response()->json([
                'success' => false,
                'message' => "Daily message quota reached ({$user->daily_message_limit} msg/day). Resets at 00:05 WIB.",
            ], 429);
        }

        $device = $user->devices()->where('id', $validated['device_id'])->first();

        if (! $device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        if (! $device->isConnected()) {
            return response()->json(['success' => false, 'message' => 'Device is not connected to WhatsApp'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $result = $this->engineService->sendTextMessage($device->session_id, $cleanPhone, $validated['message']);

        if (! empty($result['success']) && $result['success'] === true) {
            $user->incrementMessageCount();
        }

        $messageRecord = Message::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'remote_jid' => $cleanPhone.'@s.whatsapp.net',
            'message_type' => 'text',
            'message_content' => $validated['message'],
            'direction' => 'outbound',
            'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            'wa_message_id' => $result['messageId'] ?? null,
            'error_message' => $result['message'] ?? null,
        ]);

        return response()->json([
            'success' => $result['success'] ?? false,
            'message_id' => $messageRecord->id,
            'wa_id' => $result['messageId'] ?? null,
            'status' => $messageRecord->status,
        ], ($result['success'] ?? false) ? 200 : 500);
    }

    public function sendMediaMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'min:8'],
            'media_url' => ['required', 'url'],
            'media_type' => ['required', 'in:image,document,video,audio'],
            'caption' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $device = $user->devices()->where('id', $validated['device_id'])->first();

        if (! $device || ! $device->isConnected()) {
            return response()->json(['success' => false, 'message' => 'Device not found or not connected'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $result = $this->engineService->sendMediaMessage(
            $device->session_id,
            $cleanPhone,
            $validated['media_url'],
            $validated['media_type'],
            $validated['caption'] ?? ''
        );

        $messageRecord = Message::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'remote_jid' => $cleanPhone.'@s.whatsapp.net',
            'message_type' => $validated['media_type'],
            'message_content' => $validated['caption'] ?? '',
            'media_url' => $validated['media_url'],
            'direction' => 'outbound',
            'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            'wa_message_id' => $result['messageId'] ?? null,
            'error_message' => $result['message'] ?? null,
        ]);

        return response()->json([
            'success' => $result['success'] ?? false,
            'message_id' => $messageRecord->id,
            'wa_id' => $result['messageId'] ?? null,
            'status' => $messageRecord->status,
        ], ($result['success'] ?? false) ? 200 : 500);
    }
}
