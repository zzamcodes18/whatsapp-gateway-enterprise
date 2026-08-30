<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Services\HcaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apiKeys = $user->apiKeys()->latest()->get();
        $webhook = $user->webhooks()->first();

        return view('api-keys.index', [
            'apiKeys' => $apiKeys,
            'webhook' => $webhook,
        ]);
    }

    public function store(Request $request)
    {
        if (HcaptchaService::isEnabled()) {
            $hcaptchaToken = $request->input('h-captcha-response') ?? $request->input('g-recaptcha-response');
            if (! HcaptchaService::verify($hcaptchaToken)) {
                return back()->withErrors(['captcha' => 'Verifikasi hCaptcha gagal atau belum diselesaikan. Silakan coba lagi.'])->withInput();
            }
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'rate_limit' => ['nullable', 'integer', 'min:10', 'max:1000'],
        ]);

        $user = Auth::user();

        $generated = ApiKey::generate(
            $user,
            $validated['name'],
            ['send_message', 'read_devices', 'read_logs'],
            $validated['rate_limit'] ?? 60
        );

        $user->logActivity('api_key.create', "Membuat API Key baru: '{$validated['name']}'");

        return back()->with([
            'success' => 'API Key berhasil dibuat!',
            'plain_text_token' => $generated['plain_text_token'],
            'key_name' => $validated['name'],
        ]);
    }

    public function destroy(Request $request, ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        if (HcaptchaService::isEnabled()) {
            $hcaptchaToken = $request->input('h-captcha-response') ?? $request->input('g-recaptcha-response');
            if (! HcaptchaService::verify($hcaptchaToken)) {
                return back()->withErrors(['captcha' => 'Verifikasi hCaptcha gagal atau belum diselesaikan. Silakan coba lagi.']);
            }
        }

        $name = $apiKey->name;
        $apiKey->delete();

        Auth::user()->logActivity('api_key.delete', "Menghapus API Key: '{$name}'");

        return back()->with('success', "API Key '{$name}' berhasil dihapus.");
    }
}
