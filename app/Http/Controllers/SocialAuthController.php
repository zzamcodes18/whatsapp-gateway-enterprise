<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $enabled = SystemSetting::get('enable_google_login', 'false') === 'true';
        $clientId = SystemSetting::get('google_client_id');

        if (! $enabled || empty($clientId)) {
            return redirect()->route('login')->with('error', 'Login / Register via Google belum diaktifkan oleh Administrator.');
        }

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => route('auth.google.callback'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Handle Google OAuth Callback.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $enabled = SystemSetting::get('enable_google_login', 'false') === 'true';
        $clientId = SystemSetting::get('google_client_id');
        $clientSecret = SystemSetting::get('google_client_secret');

        if (! $enabled || empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Konfigurasi Google OAuth belum lengkap.');
        }

        $code = $request->input('code');
        if (empty($code)) {
            return redirect()->route('login')->with('error', 'Gagal mendapatkan otorisasi dari Google.');
        }

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => route('auth.google.callback'),
                'grant_type' => 'authorization_code',
            ]);

            if (! $response->successful()) {
                return redirect()->route('login')->with('error', 'Token Google OAuth tidak valid: ' . ($response->json('error_description') ?? 'Token error'));
            }

            $accessToken = $response->json('access_token');
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (! $userResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal mengambil data profil Google.');
            }

            $googleUser = $userResponse->json();
            $googleId = $googleUser['sub'] ?? null;
            $email = $googleUser['email'] ?? null;
            $name = $googleUser['name'] ?? 'Google User';
            $picture = $googleUser['picture'] ?? null;

            if (empty($email) || empty($googleId)) {
                return redirect()->route('login')->with('error', 'Email dari akun Google tidak ditemukan.');
            }

            return $this->authenticateSocialUser($request, 'google', $googleId, $email, $name, $picture);

        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat login Google: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to GitHub OAuth.
     */
    public function redirectToGithub(): RedirectResponse
    {
        $enabled = SystemSetting::get('enable_github_login', 'false') === 'true';
        $clientId = SystemSetting::get('github_client_id');

        if (! $enabled || empty($clientId)) {
            return redirect()->route('login')->with('error', 'Login / Register via GitHub belum diaktifkan oleh Administrator.');
        }

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => route('auth.github.callback'),
            'scope' => 'user:email',
            'state' => $state,
        ]);

        return redirect('https://github.com/login/oauth/authorize?' . $query);
    }

    /**
     * Handle GitHub OAuth Callback.
     */
    public function handleGithubCallback(Request $request): RedirectResponse
    {
        $enabled = SystemSetting::get('enable_github_login', 'false') === 'true';
        $clientId = SystemSetting::get('github_client_id');
        $clientSecret = SystemSetting::get('github_client_secret');

        if (! $enabled || empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Konfigurasi GitHub OAuth belum lengkap.');
        }

        $code = $request->input('code');
        if (empty($code)) {
            return redirect()->route('login')->with('error', 'Gagal mendapatkan otorisasi dari GitHub.');
        }

        try {
            $response = Http::acceptJson()->asForm()->post('https://github.com/login/oauth/access_token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => route('auth.github.callback'),
            ]);

            if (! $response->successful() || empty($response->json('access_token'))) {
                return redirect()->route('login')->with('error', 'Token GitHub OAuth tidak valid: ' . ($response->json('error_description') ?? 'Token error'));
            }

            $accessToken = $response->json('access_token');
            $userResponse = Http::withToken($accessToken)->get('https://api.github.com/user');

            if (! $userResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal mengambil data profil GitHub.');
            }

            $githubUser = $userResponse->json();
            $githubId = (string) ($githubUser['id'] ?? '');
            $email = $githubUser['email'] ?? null;
            $name = $githubUser['name'] ?? $githubUser['login'] ?? 'GitHub User';
            $picture = $githubUser['avatar_url'] ?? null;

            if (empty($email)) {
                // Fetch email from GitHub emails endpoint if private
                $emailsResponse = Http::withToken($accessToken)->get('https://api.github.com/user/emails');
                if ($emailsResponse->successful() && is_array($emailsResponse->json())) {
                    foreach ($emailsResponse->json() as $em) {
                        if (! empty($em['primary']) && ! empty($em['verified'])) {
                            $email = $em['email'];
                            break;
                        }
                    }
                }
            }

            if (empty($email) || empty($githubId)) {
                return redirect()->route('login')->with('error', 'Email dari akun GitHub tidak ditemukan.');
            }

            return $this->authenticateSocialUser($request, 'github', $githubId, $email, $name, $picture);

        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat login GitHub: ' . $e->getMessage());
        }
    }

    /**
     * Common Authenticate Social User logic.
     */
    protected function authenticateSocialUser(Request $request, string $provider, string $providerId, string $email, string $name, ?string $picture): RedirectResponse
    {
        $column = $provider . '_id';

        // A. JIKA PENGGUNA SEDANG LOGIN (Menghubungkan akun sosial dari Profil)
        if (Auth::check()) {
            /** @var User $currentUser */
            $currentUser = Auth::user();

            // Pastikan providerId belum dipakai akun lain
            $existingUser = User::where($column, $providerId)->where('id', '!=', $currentUser->id)->first();
            if ($existingUser) {
                return redirect()->route('profile.edit')->with('error', 'Akun ' . ucfirst($provider) . ' ini sudah terhubung dengan pengguna lain.');
            }

            $currentUser->{$column} = $providerId;
            if (empty($currentUser->avatar) && $picture) {
                $currentUser->avatar = $picture;
            }
            $currentUser->save();

            $currentUser->logActivity('user.social_link', 'Menghubungkan akun ' . ucfirst($provider));

            return redirect()->route('profile.edit')->with('success', 'Akun ' . ucfirst($provider) . ' berhasil dihubungkan ke profil Anda!');
        }

        // B. JIKA PENGGUNA BELUM LOGIN (Login / Register via OAuth)
        // 1. Cari user berdasarkan provider_id
        $user = User::where($column, $providerId)->first();

        // 2. Jika belum ditemukan, cari berdasarkan email
        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        // 3. Jika user sudah ada
        if ($user) {
            if (! $user->is_active) {
                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh administrator.');
            }

            // Tautkan provider_id jika belum ada
            if (empty($user->{$column})) {
                $user->{$column} = $providerId;
            }

            // Update avatar jika belum memiliki avatar
            if (empty($user->avatar) && $picture) {
                $user->avatar = $picture;
            }

            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();

            Auth::login($user, true);
            $user->logActivity('auth.social_login', 'Login via ' . ucfirst($provider));

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // 4. Jika user belum terdaftar -> buat akun baru
        $allowRegister = SystemSetting::get('allow_registration', 'true') === 'true';
        if (! $allowRegister) {
            return redirect()->route('login')->with('error', 'Pendaftaran pengguna baru sedang ditutup oleh Administrator.');
        }

        $deviceLimit = (int) SystemSetting::get('default_device_limit', 1);
        $dailyMessageLimit = (int) SystemSetting::get('default_daily_message_limit', 100);

        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'role' => 'user',
            'is_active' => true,
            'avatar' => $picture,
            'device_limit' => $deviceLimit,
            'daily_message_limit' => $dailyMessageLimit,
            $column => $providerId,
        ]);

        $newUser->last_login_at = now();
        $newUser->last_login_ip = $request->ip();
        $newUser->save();

        Auth::login($newUser, true);
        $newUser->logActivity('auth.social_register', 'Pendaftaran akun baru via ' . ucfirst($provider));

        return redirect()->route('dashboard')->with('success', 'Selamat datang! Akun Anda berhasil dibuat melalui ' . ucfirst($provider) . '.');
    }
}
