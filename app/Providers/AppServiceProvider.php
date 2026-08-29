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
        View::composer('*', function ($view) {
            try {
                $settings = SystemSetting::all()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $settings = [];
            }

            $siteName = $settings['site_name'] ?? 'Whatsapp Gateway Enterprise';
            $siteDescription = $settings['site_description'] ?? 'Console manajemen gateway WhatsApp terpadu. Pantau nomor terhubung, kirim pesan REST API, dan kelola API Key.';
            $siteKeywords = $settings['site_keywords'] ?? 'whatsapp gateway, wa gateway api, whatsapp otp, baileys v1, whatsapp bot api, rest api whatsapp';
            $siteLogo = $settings['site_logo'] ?? null;
            $siteFavicon = $settings['site_favicon'] ?? null;
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
