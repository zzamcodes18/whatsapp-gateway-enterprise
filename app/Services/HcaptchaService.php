<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HcaptchaService
{
    /**
     * Cek apakah hCaptcha aktif dan terkonfigurasi.
     */
    public static function isEnabled(): bool
    {
        return SystemSetting::get('enable_hcaptcha', 'false') === 'true'
            && ! empty(SystemSetting::get('hcaptcha_site_key'))
            && ! empty(SystemSetting::get('hcaptcha_secret_key'));
    }

    /**
     * Render container widget hCaptcha (jika aktif).
     */
    public static function renderWidget(): string
    {
        if (! self::isEnabled()) {
            return '';
        }

        $siteKey = SystemSetting::get('hcaptcha_site_key');

        return '<div class="h-captcha flex justify-center my-2" data-sitekey="'.e($siteKey).'"></div>';
    }

    /**
     * Render tag script hCaptcha (jika aktif).
     */
    public static function renderScript(): string
    {
        if (! self::isEnabled()) {
            return '';
        }

        return '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
    }

    /**
     * Verifikasi token hCaptcha dari request ke api.hcaptcha.com.
     */
    public static function verify(?string $token): bool
    {
        if (! self::isEnabled()) {
            return true; // hCaptcha nonaktif — lewati verifikasi
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://api.hcaptcha.com/siteverify', [
                    'secret' => SystemSetting::get('hcaptcha_secret_key'),
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable $e) {
            Log::warning('hCaptcha verification failed: ' . $e->getMessage());

            return false;
        }
    }
}
