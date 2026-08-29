<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $webhook = $user->webhooks()->first();

        return view('webhooks.index', [
            'webhook' => $webhook,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_url' => ['required', 'url', 'max:500'],
            'events' => ['required', 'array'],
            'events.*' => ['string'],
            'secret_key' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();

        $webhook = $user->webhooks()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'target_url' => $validated['target_url'],
                'secret_key' => $validated['secret_key'] ?? Str::random(32),
                'events' => $validated['events'],
                'is_active' => true,
            ]
        );

        $user->logActivity('webhook.update', 'Memperbarui konfigurasi Webhook');

        return back()->with('success', 'Konfigurasi Webhook berhasil disimpan!');
    }

    public function test(Request $request)
    {
        $user = Auth::user();
        $webhook = $user->webhooks()->first();

        if (! $webhook) {
            return back()->withErrors(['target_url' => 'Harap konfigurasikan URL webhook terlebih dahulu.']);
        }

        try {
            $response = Http::withHeaders([
                'X-LapakOTP-Secret' => $webhook->secret_key,
                'Content-Type' => 'application/json',
                'User-Agent' => 'LapakOTP-Webhook-Dispatcher/1.0',
            ])->timeout(5)->post($webhook->target_url, [
                'event' => 'webhook.test',
                'message' => 'LapakOTP Webhook Test Ping Successful',
                'timestamp' => now()->toIso8601String(),
                'user_id' => $user->id,
            ]);

            if ($response->successful()) {
                return back()->with('success', "Test Webhook Sukses! Respon HTTP: {$response->status()}");
            }

            return back()->withErrors(['target_url' => "Target mengembalikan HTTP {$response->status()}: ".$response->body()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['target_url' => 'Gagal mengirim test webhook: '.$e->getMessage()]);
        }
    }
}
