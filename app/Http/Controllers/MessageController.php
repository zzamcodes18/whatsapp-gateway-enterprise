<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\WaEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $devices = $user->devices()->where('status', 'connected')->get();

        $query = $user->messages()->with('device')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->query('device_id'));
        }
        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('remote_jid', 'like', $search)
                    ->orWhere('message_content', 'like', $search);
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        return view('messages.index', [
            'devices' => $devices,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'phone' => ['required', 'string', 'min:8', 'max:25'],
            'message_type' => ['required', 'in:text,image,document'],
            'message' => ['nullable', 'string'],
            'media_url' => ['nullable', 'url'],
        ]);

        $user = Auth::user();

        if (! $user->canSendMessage()) {
            $reason = $user->sendMessageBlockReason() ?? 'Batas pengiriman pesan Anda telah tercapai.';
            return back()->withInput()->withErrors([
                'phone' => $reason . ' Kuota harian direset pukul 00:05 WIB, kuota bulanan direset awal bulan.',
            ]);
        }

        $device = $user->devices()->findOrFail($validated['device_id']);

        if (! $device->isConnected()) {
            return back()->withInput()->withErrors([
                'device_id' => 'Device yang dipilih belum terhubung ke WhatsApp.',
            ]);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $remoteJid = $cleanPhone.'@s.whatsapp.net';

        // Record message in DB
        $messageRecord = $user->messages()->create([
            'device_id' => $device->id,
            'remote_jid' => $remoteJid,
            'message_type' => $validated['message_type'],
            'message_content' => $validated['message'] ?? '',
            'media_url' => $validated['media_url'] ?? null,
            'direction' => 'outbound',
            'status' => 'pending',
        ]);

        // Send via WaEngineService
        if ($validated['message_type'] === 'text') {
            $result = $this->engineService->sendTextMessage(
                $device->session_id,
                $cleanPhone,
                $validated['message']
            );
        } else {
            $result = $this->engineService->sendMediaMessage(
                $device->session_id,
                $cleanPhone,
                $validated['media_url'],
                $validated['message_type'],
                $validated['message'] ?? '',
                'file_'.time().'.pdf'
            );
        }

        if (! empty($result['success']) && $result['success'] === true) {
            $messageRecord->update([
                'status' => 'sent',
                'wa_message_id' => $result['messageId'] ?? null,
            ]);

            $user->incrementMessageCount();

            $user->logActivity('message.send', "Mengirim pesan ke {$cleanPhone}", [
                'message_id' => $messageRecord->id,
                'device_id' => $device->id,
            ]);

            return back()->with('success', "Pesan berhasil dikirim ke +{$cleanPhone}!");
        }

        $errorMessage = $result['message'] ?? 'Gagal mengirim pesan via WhatsApp Engine';
        $messageRecord->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);

        return back()->withInput()->withErrors([
            'phone' => "Gagal mengirim: {$errorMessage}",
        ]);
    }
}
