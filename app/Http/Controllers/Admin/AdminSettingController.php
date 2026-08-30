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
            'default_device_limit' => ['required', 'integer', 'min:0'],
            'default_daily_message_limit' => ['required', 'integer', 'min:0'],
            'allow_registration' => ['required', 'in:true,false'],
            'enable_google_login' => ['nullable', 'in:true,false'],
            'google_client_id' => ['nullable', 'string', 'max:255'],
            'google_client_secret' => ['nullable', 'string', 'max:255'],
            'enable_github_login' => ['nullable', 'in:true,false'],
            'github_client_id' => ['nullable', 'string', 'max:255'],
            'github_client_secret' => ['nullable', 'string', 'max:255'],
            'wa_engine_url' => ['nullable', 'url', 'max:255'],
            'wa_engine_secret' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,ico,webp', 'max:1024'],
        ]);

        $keysToUpdate = [
            'site_name',
            'site_description',
            'site_keywords',
            'support_email',
            'support_whatsapp',
            'default_device_limit',
            'default_daily_message_limit',
            'allow_registration',
            'enable_google_login',
            'google_client_id',
            'google_client_secret',
            'enable_github_login',
            'github_client_id',
            'github_client_secret',
            'wa_engine_url',
            'wa_engine_secret',
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
}
