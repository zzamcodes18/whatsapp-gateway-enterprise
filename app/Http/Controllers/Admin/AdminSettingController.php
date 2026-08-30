<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:100'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_keywords' => ['nullable', 'string', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:100'],
            'support_whatsapp' => ['nullable', 'string', 'max:30'],
            'allow_registration' => ['nullable', 'string'],
            'allow_registration' => ['required', 'in:true,false'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer'],
            'smtp_encryption' => ['nullable', 'string', 'max:20'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_from_address' => ['nullable', 'email', 'max:255'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
            'enable_google_login' => ['nullable', 'in:true,false'],
            'google_client_id' => ['nullable', 'string', 'max:255'],
            'google_client_secret' => ['nullable', 'string', 'max:255'],
            'enable_github_login' => ['nullable', 'in:true,false'],
            'github_client_id' => ['nullable', 'string', 'max:255'],
            'github_client_secret' => ['nullable', 'string', 'max:255'],
            'wa_engine_url' => ['nullable', 'url', 'max:255'],
            'wa_engine_secret' => ['nullable', 'string', 'max:255'],
            'enable_turnstile' => ['nullable', 'in:true,false'],
            'turnstile_site_key' => ['nullable', 'string', 'max:255'],
            'turnstile_secret_key' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,ico,webp', 'max:1024'],
        ]);

        $keysToUpdate = [
            'site_name',
            'site_description',
            'site_keywords',
            'support_email',
            'support_whatsapp',
            'allow_registration',
            'allow_registration',
            'smtp_host',
            'smtp_port',
            'smtp_encryption',
            'smtp_username',
            'smtp_password',
            'smtp_from_address',
            'smtp_from_name',
            'enable_google_login',
            'google_client_id',
            'google_client_secret',
            'enable_github_login',
            'github_client_id',
            'github_client_secret',
            'wa_engine_url',
            'wa_engine_secret',
            'enable_turnstile',
            'turnstile_site_key',
            'turnstile_secret_key',
        ];

        foreach ($keysToUpdate as $key) {
            if ($request->has($key)) {
                SystemSetting::set($key, (string) $request->input($key));
            }
        }

        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $mime = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $dataUri = 'data:' . $mime . ';base64,' . $base64;
            SystemSetting::set('site_logo', $dataUri);
        }

        if ($request->hasFile('site_favicon')) {
            $file = $request->file('site_favicon');
            $mime = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $dataUri = 'data:' . $mime . ';base64,' . $base64;
            SystemSetting::set('site_favicon', $dataUri);
        }

        /** @var User $user */
        $user = auth()->user();
        $user->logActivity('admin.setting_updated', 'Memperbarui konfigurasi website');

        return redirect()->back()->with('success', 'Konfigurasi website berhasil disimpan!');
    }

    public function testSmtp(Request $request)
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $smtpHost = SystemSetting::get('smtp_host');
        if (empty($smtpHost)) {
            return back()->withErrors(['test_email' => 'SMTP Host belum dikonfigurasi. Harap simpan pengaturan SMTP terlebih dahulu.']);
        }

        try {
            $siteName = SystemSetting::get('site_name', 'WhatsApp Gateway');
            $testCode = (string) rand(100000, 999999);

            // Re-apply Mail Config secara runtime
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => (int) SystemSetting::get('smtp_port', 587),
                'mail.mailers.smtp.encryption' => SystemSetting::get('smtp_encryption') === 'null' ? null : SystemSetting::get('smtp_encryption', 'tls'),
                'mail.mailers.smtp.username' => SystemSetting::get('smtp_username'),
                'mail.mailers.smtp.password' => SystemSetting::get('smtp_password'),
                'mail.from.address' => SystemSetting::get('smtp_from_address', env('MAIL_FROM_ADDRESS', 'noreply@example.com')),
                'mail.from.name' => SystemSetting::get('smtp_from_name', $siteName),
            ]);

            \Illuminate\Support\Facades\Mail::raw(
                "Halo,\n\nIni adalah email uji coba (Test Connection) dari server SMTP {$siteName}.\nKode Uji OTP: {$testCode}\n\nJika Anda menerima email ini, konfigurasi SMTP Mail Server Anda telah BERHASIL terhubung!",
                function ($message) use ($validated, $siteName) {
                    $message->to($validated['test_email'])
                        ->subject("[{$siteName}] Uji Koneksi SMTP Mail Server - BERHASIL");
                }
            );

            /** @var User $user */
            $user = auth()->user();
            $user->logActivity('admin.smtp_test_sent', "Uji kirim email SMTP ke {$validated['test_email']}");

            return back()->with('success', "Email uji coba berhasil dikirim ke {$validated['test_email']}! Periksa kotak masuk/spam Anda.");
        } catch (\Throwable $e) {
            return back()->withErrors(['test_email' => 'Gagal terhubung ke SMTP Mail Server: ' . $e->getMessage()]);
        }
    }
}
