<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Cek apakah Turnstile aktif dan terkonfigurasi.
     */
    public static function isEnabled(): bool
    {
        return SystemSetting::get('enable_turnstile', 'false') === 'true'
            && ! empty(SystemSetting::get('turnstile_site_key'))
            && ! empty(SystemSetting::get('turnstile_secret_key'));
    }

    /**
     * Render widget Turnstile (jika aktif).
     * Mengembalikan HTML widget + script, atau string kosong jika nonaktif.
     */
    public static function render(): string
    {
        if (! self::isEnabled()) {
            return '';
        }

        $siteKey = SystemSetting::get('turnstile_site_key');

        return '<div class="cf-turnstile" data-sitekey="'.e($siteKey).'" data-theme="auto" data-language="id"></div>'
            .'<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    }

    /**
     * Verifikasi token Turnstile dari request ke Cloudflare.
     */
    public static function verify(?string $token): bool
    {
        if (! self::isEnabled()) {
            return true; // Turnstile nonaktif — lewati verifikasi
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => SystemSetting::get('turnstile_secret_key'),
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable $e) {
            \Log::warning('Turnstile verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
