<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Message;
use App\Models\SystemSetting;
use App\Services\WaEngineService;
use Illuminate\Http\Request;

class AdminBotServerController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index()
    {
        $connectedDevices = Device::with('user')->where('status', 'connected')->get();
        $allDevices = Device::with('user')->latest()->get();

        $selectedDeviceId = SystemSetting::get('otp_server_device_id');
        $botDevice = $selectedDeviceId ? Device::with('user')->find($selectedDeviceId) : null;
        $otpTemplate = SystemSetting::get('otp_template', 'Kode verifikasi OTP WhatsApp Gateway Anda adalah: {otp}. Berlaku selama 5 menit. Jangan berikan kepada siapapun.');
        $enableRegisterOtp = SystemSetting::get('enable_register_otp', 'true') === 'true';

        return view('admin.bot-server.index', [
            'connectedDevices' => $connectedDevices,
            'allDevices' => $allDevices,
            'botDevice' => $botDevice,
            'otpTemplate' => $otpTemplate,
            'enableRegisterOtp' => $enableRegisterOtp,
        ]);
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'device_id' => ['nullable', 'exists:devices,id'],
            'otp_template' => ['required', 'string', 'max:500'],
            'enable_register_otp' => ['nullable'],
        ]);

        // Reset previous bot devices
        Device::where('is_system_bot', true)->update(['is_system_bot' => false]);

        if (! empty($validated['device_id'])) {
            $device = Device::findOrFail($validated['device_id']);
            $device->update(['is_system_bot' => true]);
            SystemSetting::set('otp_server_device_id', (string) $device->id);
        } else {
            SystemSetting::set('otp_server_device_id', null);
        }

        SystemSetting::set('otp_template', $validated['otp_template']);
        SystemSetting::set('enable_register_otp', $request->has('enable_register_otp') ? 'true' : 'false');

        auth()->user()->logActivity('admin.bot_server_update', 'Admin memperbarui konfigurasi WhatsApp Bot Server OTP');

        return back()->with('success', 'Konfigurasi Bot Server OTP berhasil disimpan!');
    }

    public function testSendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:8', 'max:25'],
            'custom_code' => ['nullable', 'string', 'max:10'],
        ]);

        $selectedDeviceId = SystemSetting::get('otp_server_device_id');
        if (! $selectedDeviceId) {
            return back()->withErrors(['phone' => 'Belum ada perangkat yang diatur sebagai Bot Server OTP.']);
        }

        $botDevice = Device::find($selectedDeviceId);
        if (! $botDevice || ! $botDevice->isConnected()) {
            return back()->withErrors(['phone' => 'Perangkat Bot Server sedang offline atau tidak terhubung ke WhatsApp.']);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $otpCode = $validated['custom_code'] ?? rand(100000, 999999);
        $template = SystemSetting::get('otp_template', 'Kode verifikasi OTP WhatsApp Gateway Anda adalah: {otp}. Berlaku selama 5 menit.');
        $messageText = str_replace('{otp}', $otpCode, $template);

        $result = $this->engineService->sendTextMessage(
            $botDevice->session_id,
            $cleanPhone,
            $messageText
        );

        if (! empty($result['success']) && $result['success'] === true) {
            Message::create([
                'user_id' => auth()->id(),
                'device_id' => $botDevice->id,
                'remote_jid' => $cleanPhone.'@s.whatsapp.net',
                'message_type' => 'text',
                'message_content' => $messageText,
                'direction' => 'outbound',
                'status' => 'sent',
                'wa_message_id' => $result['messageId'] ?? null,
            ]);

            auth()->user()->logActivity('admin.bot_test_otp', "Test OTP terkirim ke {$cleanPhone}");

            return back()->with('success', "Test OTP [{$otpCode}] berhasil dikirim ke +{$cleanPhone} via {$botDevice->name}!");
        }

        $errorMsg = $result['message'] ?? 'Gagal mengirim pesan melalui engine.';

        return back()->withErrors(['phone' => "Gagal kirim: {$errorMsg}"]);
    }
}
