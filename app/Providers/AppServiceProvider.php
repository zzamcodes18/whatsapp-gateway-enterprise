<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
        \Carbon\Carbon::setLocale('id');

        try {
            $smtpHost = SystemSetting::get('smtp_host');
            if ($smtpHost) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $smtpHost,
                    'mail.mailers.smtp.port' => (int) SystemSetting::get('smtp_port', 587),
                    'mail.mailers.smtp.encryption' => SystemSetting::get('smtp_encryption') === 'null' ? null : SystemSetting::get('smtp_encryption', 'tls'),
                    'mail.mailers.smtp.username' => SystemSetting::get('smtp_username'),
                    'mail.mailers.smtp.password' => SystemSetting::get('smtp_password'),
                    'mail.from.address' => SystemSetting::get('smtp_from_address', env('MAIL_FROM_ADDRESS', 'noreply@example.com')),
                    'mail.from.name' => SystemSetting::get('smtp_from_name', SystemSetting::get('site_name', 'WhatsApp Gateway')),
                ]);
            }
        } catch (\Throwable $e) {
            // Abaikan jika DB belum siap
        }

        View::composer('*', function ($view) {
            try {
                $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $siteName = $settings['site_name'] ?? 'Whatsapp Gateway Enterprise';
            $siteDescription = $settings['site_description'] ?? 'Console manajemen gateway WhatsApp terpadu. Pantau nomor terhubung, kirim pesan REST API, dan kelola API Key.';
            $siteKeywords = $settings['site_keywords'] ?? 'whatsapp gateway, wa gateway api, whatsapp otp, whatsapp bot api, rest api whatsapp';
            
            $rawLogo = $settings['site_logo'] ?? null;
            if ($rawLogo) {
                $siteLogo = (str_starts_with($rawLogo, 'data:') || str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) 
                    ? $rawLogo 
                    : asset(ltrim($rawLogo, '/'));
            } else {
                $siteLogo = null;
            }

            $rawFavicon = $settings['site_favicon'] ?? null;
            if ($rawFavicon) {
                $siteFavicon = (str_starts_with($rawFavicon, 'data:') || str_starts_with($rawFavicon, 'http://') || str_starts_with($rawFavicon, 'https://')) 
                    ? $rawFavicon 
                    : asset(ltrim($rawFavicon, '/'));
            } else {
                $siteFavicon = null;
            }

            $supportWhatsapp = $settings['support_whatsapp'] ?? '';
            $supportEmail = $settings['support_email'] ?? '';

            $view->with('siteName', $siteName)
                 ->with('siteDescription', $siteDescription)
                 ->with('siteKeywords', $siteKeywords)
                 ->with('siteLogo', $siteLogo)
                 ->with('siteFavicon', $siteFavicon)
                 ->with('supportWhatsapp', $supportWhatsapp)
                 ->with('supportEmail', $supportEmail)
                 ->with('globalSettings', $settings);
        });
    }
}
