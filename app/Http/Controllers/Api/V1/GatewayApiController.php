<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageTemplate;
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
            $reason = $user->sendMessageBlockReason() ?? 'Message quota reached.';
            return response()->json([
                'success' => false,
                'message' => $reason,
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

    public function sendButtonMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'min:8'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'footer' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'url'],
            'video' => ['nullable', 'string', 'url'],
            'document' => ['nullable', 'string', 'url'],
            'buttons' => ['required', 'array', 'min:1'],
        ]);

        $user = $request->user();

        if (! $user->canSendMessage()) {
            $reason = $user->sendMessageBlockReason() ?? 'Message quota reached.';
            return response()->json([
                'success' => false,
                'message' => $reason,
            ], 429);
        }

        $device = $user->devices()->where('id', $validated['device_id'])->first();

        if (! $device || ! $device->isConnected()) {
            return response()->json(['success' => false, 'message' => 'Device not found or not connected'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $result = $this->engineService->sendInteractiveMessage(
            $device->session_id,
            $cleanPhone,
            [
                'title' => $validated['title'] ?? '',
                'subtitle' => $validated['subtitle'] ?? '',
                'body' => $validated['body'] ?? '',
                'footer' => $validated['footer'] ?? '',
                'image' => $validated['image'] ?? null,
                'video' => $validated['video'] ?? null,
                'document' => $validated['document'] ?? null,
                'buttons' => $validated['buttons'],
            ]
        );

        if (! empty($result['success']) && $result['success'] === true) {
            $user->incrementMessageCount();
        }

        $messageRecord = Message::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'remote_jid' => $cleanPhone.'@s.whatsapp.net',
            'message_type' => 'button',
            'message_content' => json_encode(['body' => $validated['body'] ?? '', 'buttons' => $validated['buttons']]),
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

    public function sendTemplateMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'min:8'],
            'template_id' => ['required', 'integer'],
            'variables' => ['nullable', 'array'],
        ]);

        $user = $request->user();

        if (! $user->canSendMessage()) {
            $reason = $user->sendMessageBlockReason() ?? 'Message quota reached.';
            return response()->json([
                'success' => false,
                'message' => $reason,
            ], 429);
        }

        $template = $user->messageTemplates()->where('id', $validated['template_id'])->where('is_active', true)->first();

        if (! $template) {
            return response()->json(['success' => false, 'message' => 'Template message not found or inactive'], 404);
        }

        $device = $user->devices()->where('id', $validated['device_id'])->first();

        if (! $device || ! $device->isConnected()) {
            return response()->json(['success' => false, 'message' => 'Device not found or not connected to WhatsApp'], 400);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $vars = $validated['variables'] ?? [];

        // Render template variables
        $body = MessageTemplate::renderPlaceholders($template->content, $vars);
        $title = MessageTemplate::renderPlaceholders($template->title, $vars);
        $footer = MessageTemplate::renderPlaceholders($template->footer, $vars);

        $buttons = [];
        if (! empty($template->buttons)) {
            foreach ($template->buttons as $btn) {
                $item = $btn;
                $item['text'] = MessageTemplate::renderPlaceholders($btn['text'] ?? '', $vars);
                if (! empty($btn['url'])) {
                    $item['url'] = MessageTemplate::renderPlaceholders($btn['url'], $vars);
                }
                if (! empty($btn['code'])) {
                    $item['code'] = MessageTemplate::renderPlaceholders($btn['code'], $vars);
                }
                $buttons[] = $item;
            }
        }

        if (! empty($buttons)) {
            $result = $this->engineService->sendInteractiveMessage(
                $device->session_id,
                $cleanPhone,
                [
                    'title' => $title ?? '',
                    'body' => $body,
                    'footer' => $footer ?? '',
                    'buttons' => $buttons,
                ]
            );
        } else {
            $result = $this->engineService->sendTextMessage(
                $device->session_id,
                $cleanPhone,
                $body
            );
        }

        if (! empty($result['success']) && $result['success'] === true) {
            $user->incrementMessageCount();
        }

        $messageRecord = Message::create([
            'user_id' => $user->id,
            'device_id' => $device->id,
            'remote_jid' => $cleanPhone.'@s.whatsapp.net',
            'message_type' => ! empty($buttons) ? 'button' : 'text',
            'message_content' => $body,
            'direction' => 'outbound',
            'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
            'wa_message_id' => $result['messageId'] ?? null,
            'error_message' => $result['message'] ?? null,
        ]);

        return response()->json([
            'success' => $result['success'] ?? false,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
            ],
            'rendered_message' => $body,
            'message_id' => $messageRecord->id,
            'wa_id' => $result['messageId'] ?? null,
            'status' => $messageRecord->status,
        ], ($result['success'] ?? false) ? 200 : 500);
    }
}
