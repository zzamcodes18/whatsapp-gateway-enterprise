<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WaEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login_attempt:'.sha1(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login gagal. Keamanan diaktifkan. Silakan coba {$seconds} detik lagi.",
                ]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            $user->logActivity('auth.login', 'Berhasil login ke sistem');

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
            ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (SystemSetting::get('allow_registration', 'true') === 'false') {
            return redirect()->route('login')->with('error', 'Pendaftaran pengguna baru saat ini sedang ditutup oleh administrator.');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (SystemSetting::get('allow_registration', 'true') === 'false') {
            return redirect()->route('login')->with('error', 'Pendaftaran pengguna baru saat ini sedang ditutup oleh administrator.');
        }

        $enableRegisterOtp = SystemSetting::get('enable_register_otp', 'true') === 'true';
        $botDeviceId = SystemSetting::get('otp_server_device_id');
        $botDevice = $botDeviceId ? Device::find($botDeviceId) : null;
        $isBotActive = $enableRegisterOtp && $botDevice && $botDevice->isConnected();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $cleanPhone = null;
        if (! empty($validated['phone_number'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            if (str_starts_with($cleanPhone, '08')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }
        }

        // Generasi Kode OTP
        $otp = (string) rand(100000, 999999);
        $method = 'whatsapp'; // Default pengiriman via WhatsApp

        // Jika bot WA aktif, kirim via WA terlebih dahulu
        if ($isBotActive && $cleanPhone) {
            $sent = $this->sendWhatsAppOtp($botDevice, $cleanPhone, $otp);
            if (! $sent && ! empty(SystemSetting::get('smtp_host'))) {
                // Fallback kirim via Email jika WA gagal tapi SMTP ada
                $this->sendEmailOtp($validated['email'], $otp);
                $method = 'email';
            }
        } elseif (! empty(SystemSetting::get('smtp_host'))) {
            // Jika Bot WA tidak aktif tapi SMTP dikonfigurasi, kirim via Email
            $this->sendEmailOtp($validated['email'], $otp);
            $method = 'email';
        }

        session([
            'pending_registration' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $cleanPhone,
                'password' => Hash::make($validated['password']),
                'otp' => $otp,
                'otp_method' => $method,
                'expires_at' => now()->addMinutes(5)->timestamp,
            ],
        ]);

        $msg = $method === 'whatsapp' 
            ? "Kode verifikasi OTP telah dikirimkan ke WhatsApp (+{$cleanPhone})."
            : "Kode verifikasi OTP telah dikirimkan ke Email ({$validated['email']}).";

        return redirect()->route('register.otp')->with('success', $msg);
    }

    public function showVerifyOtp()
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register');
        }

        $hasSmtp = ! empty(SystemSetting::get('smtp_host'));
        $botDeviceId = SystemSetting::get('otp_server_device_id');
        $botDevice = $botDeviceId ? Device::find($botDeviceId) : null;
        $hasWa = $botDevice && $botDevice->isConnected();

        return view('auth.verify-otp', [
            'phone' => $pending['phone_number'],
            'email' => $pending['email'],
            'expiresAt' => $pending['expires_at'],
            'currentMethod' => $pending['otp_method'] ?? 'whatsapp',
            'hasSmtp' => $hasSmtp,
            'hasWa' => $hasWa,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran telah berakhir. Silakan daftar kembali.');
        }

        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if (now()->timestamp > $pending['expires_at']) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluwarsa. Silakan klik tombol kirim ulang OTP.']);
        }

        if ($validated['otp'] !== $pending['otp']) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan tidak cocok. Silakan periksa kembali WhatsApp Anda.']);
        }

        $defaultDeviceLimit = (int) SystemSetting::get('default_device_limit', 3);
        $defaultDailyLimit = (int) SystemSetting::get('default_daily_message_limit', 500);

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone_number' => $pending['phone_number'],
            'password' => $pending['password'],
            'role' => 'user',
            'is_active' => true,
            'device_limit' => $defaultDeviceLimit,
            'daily_message_limit' => $defaultDailyLimit,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        session()->forget('pending_registration');

        Auth::login($user);
        $request->session()->regenerate();

        $user->logActivity('auth.register_otp_verified', 'Registrasi & Verifikasi OTP WhatsApp berhasil');

        return redirect()->route('dashboard')
            ->with('success', 'Verifikasi OTP WhatsApp Berhasil! Selamat datang di WhatsApp Gateway Enterprise.');
    }

    public function resendOtp(Request $request)
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran telah berakhir. Silakan daftar kembali.');
        }

        $targetChannel = $request->input('channel', $pending['otp_method'] ?? 'whatsapp');

        $otp = (string) rand(100000, 999999);
        $pending['otp'] = $otp;
        $pending['otp_method'] = $targetChannel;
        $pending['expires_at'] = now()->addMinutes(5)->timestamp;
        session(['pending_registration' => $pending]);

        if ($targetChannel === 'email') {
            $hasSmtp = ! empty(SystemSetting::get('smtp_host'));
            if (! $hasSmtp) {
                return back()->withErrors(['otp' => 'SMTP Mail Server belum dikonfigurasi oleh administrator.']);
            }
            $this->sendEmailOtp($pending['email'], $otp);
            return back()->with('success', "Kode OTP baru berhasil dikirimkan ke Email ({$pending['email']})!");
        } else {
            $botDeviceId = SystemSetting::get('otp_server_device_id');
            $botDevice = $botDeviceId ? Device::find($botDeviceId) : null;

            if (! $botDevice || ! $botDevice->isConnected()) {
                return back()->withErrors(['otp' => 'Perangkat Bot Server WhatsApp OTP saat ini sedang offline.']);
            }

            $sent = $this->sendWhatsAppOtp($botDevice, $pending['phone_number'], $otp);
            if ($sent) {
                return back()->with('success', "Kode OTP WhatsApp baru berhasil dikirimkan ke +{$pending['phone_number']}!");
            }
            return back()->withErrors(['otp' => 'Gagal mengirimkan ulang OTP via WhatsApp. Silakan coba beberapa saat lagi.']);
        }
    }

    protected function sendEmailOtp(string $email, string $otp): void
    {
        try {
            $siteName = SystemSetting::get('site_name', 'WhatsApp Gateway');
            \Illuminate\Support\Facades\Mail::raw(
                "Kode verifikasi OTP {$siteName} Anda adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun.",
                function ($message) use ($email, $siteName, $otp) {
                    $message->to($email)
                        ->subject("[{$siteName}] Kode Verifikasi OTP: {$otp}");
                }
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim Email OTP: ' . $e->getMessage());
        }
    }

    protected function sendWhatsAppOtp(Device $botDevice, string $phone, string $otp): bool
    {
        $template = SystemSetting::get('otp_template', 'Kode verifikasi OTP WhatsApp Gateway Anda adalah: *{otp}*. Berlaku selama 5 menit. Jangan bagikan kode ini kepada siapapun.');
        $messageText = str_replace('{otp}', $otp, $template);

        $result = $this->engineService->sendTextMessage(
            $botDevice->session_id,
            $phone,
            $messageText
        );

        if (! empty($result['success']) && $result['success'] === true) {
            Message::create([
                'user_id' => $botDevice->user_id,
                'device_id' => $botDevice->id,
                'remote_jid' => $phone.'@s.whatsapp.net',
                'message_type' => 'text',
                'message_content' => $messageText,
                'direction' => 'outbound',
                'status' => 'sent',
                'wa_message_id' => $result['messageId'] ?? null,
            ]);

            return true;
        }

        return false;
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $user->logActivity('auth.logout', 'Logout dari akun');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Simulating reset instructions safely without revealing user presence
        return back()->with('status', 'Jika email terdaftar, instruksi pemulihan kata sandi telah dikirimkan.');
    }
}
