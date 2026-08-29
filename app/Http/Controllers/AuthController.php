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
            'phone_number' => [$isBotActive ? 'required' : 'nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $cleanPhone = null;
        if (! empty($validated['phone_number'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            if (str_starts_with($cleanPhone, '08')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }
        }

        // Jika Bot Server OTP Aktif & Terhubung, kirimkan Kode OTP via WhatsApp
        if ($isBotActive && $cleanPhone) {
            $otp = (string) rand(100000, 999999);

            session([
                'pending_registration' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone_number' => $cleanPhone,
                    'password' => Hash::make($validated['password']),
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(5)->timestamp,
                ],
            ]);

            $sent = $this->sendWhatsAppOtp($botDevice, $cleanPhone, $otp);

            if ($sent) {
                return redirect()->route('register.otp')
                    ->with('success', "Kode verifikasi OTP telah dikirimkan ke WhatsApp (+{$cleanPhone}). Silakan masukkan 6 digit kode OTP Anda.");
            } else {
                return back()->withInput()->withErrors(['phone_number' => 'Gagal mengirim kode OTP ke nomor WhatsApp Anda. Pastikan nomor terhubung dengan WhatsApp.']);
            }
        }

        // Fallback pendaftaran langsung jika Bot Server tidak aktif / offline
        $defaultDeviceLimit = (int) SystemSetting::get('default_device_limit', 3);
        $defaultDailyLimit = (int) SystemSetting::get('default_daily_message_limit', 500);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $cleanPhone,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'is_active' => true,
            'device_limit' => $defaultDeviceLimit,
            'daily_message_limit' => $defaultDailyLimit,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $user->logActivity('auth.register', 'Pendaftaran akun baru');

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di WhatsApp Gateway Enterprise.');
    }

    public function showVerifyOtp()
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', [
            'phone' => $pending['phone_number'],
            'email' => $pending['email'],
            'expiresAt' => $pending['expires_at'],
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

        $botDeviceId = SystemSetting::get('otp_server_device_id');
        $botDevice = $botDeviceId ? Device::find($botDeviceId) : null;

        if (! $botDevice || ! $botDevice->isConnected()) {
            return back()->withErrors(['otp' => 'Perangkat Bot Server OTP saat ini sedang offline.']);
        }

        $otp = (string) rand(100000, 999999);
        $pending['otp'] = $otp;
        $pending['expires_at'] = now()->addMinutes(5)->timestamp;
        session(['pending_registration' => $pending]);

        $sent = $this->sendWhatsAppOtp($botDevice, $pending['phone_number'], $otp);

        if ($sent) {
            return back()->with('success', "Kode OTP WhatsApp baru berhasil dikirimkan ke +{$pending['phone_number']}!");
        }

        return back()->withErrors(['otp' => 'Gagal mengirimkan ulang OTP via WhatsApp. Silakan coba beberapa saat lagi.']);
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
