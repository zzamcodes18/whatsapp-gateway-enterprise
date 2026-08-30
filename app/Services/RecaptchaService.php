<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Cek apakah reCAPTCHA v3 aktif dan terkonfigurasi.
     */
    public static function isEnabled(): bool
    {
        return SystemSetting::get('enable_recaptcha', 'false') === 'true'
            && ! empty(SystemSetting::get('recaptcha_site_key'))
            && ! empty(SystemSetting::get('recaptcha_secret_key'));
    }

    /**
     * Verifikasi token reCAPTCHA v3 dari request ke Google.
     * Mengembalikan true jika skor >= threshold (default 0.5).
     */
    public static function verify(?string $token): bool
    {
        if (! self::isEnabled()) {
            return true; // reCAPTCHA nonaktif — lewati verifikasi
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => SystemSetting::get('recaptcha_secret_key'),
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful() || $response->json('success') !== true) {
                return false;
            }

            $score = (float) $response->json('score', 0);
            $threshold = (float) SystemSetting::get('recaptcha_min_score', '0.5');

            return $score >= $threshold;
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification failed: ' . $e->getMessage());

            // Fail-open agar user tidak terblokir saat Google down (opsional: fail-closed untuk keamanan maksimal)
            return false;
        }
    }
}
