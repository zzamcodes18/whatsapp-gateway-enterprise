<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apiKey = $user ? $user->apiKeys()->first() : null;
        $webhook = $user ? $user->webhooks()->first() : null;

        return view('docs.index', [
            'user' => $user,
            'userApiKey' => $apiKey ? $apiKey->key_prefix . '...' : 'YOUR_API_KEY',
            'userWebhookSecret' => $webhook ? $webhook->secret_key : 'YOUR_WEBHOOK_SECRET',
            'baseUrl' => config('app.url'),
        ]);
    }
}
