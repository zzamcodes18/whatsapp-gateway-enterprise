@extends('layouts.app')

@section('title', 'Dokumentasi REST API & Webhooks v1.0')

@section('content')
<div x-data="{ 
    codeLang: 'curl',
    activeSection: 'auth',
    userKey: '{{ $userApiKey }}',
    baseUrl: '{{ $baseUrl }}'
}" class="max-w-7xl mx-auto space-y-6 pb-16">

    <!-- ================= TOP HERO BANNER & HEADER ================= -->
    <div class="app-card p-6 sm:p-8 bg-slate-950 text-white relative overflow-hidden shadow-2xl border-slate-800">
        <!-- Glow accents -->
        <div class="absolute -top-16 -right-16 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-80 h-80 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-extrabold text-[10px] uppercase tracking-wider border border-blue-500/30">
                        REST API v1.0 ENTERPRISE
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[10px] font-bold border border-emerald-500/30">
                        LATENCY &lt; 800ms
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Dokumentasi REST API & Webhooks</h1>
                <p class="text-xs sm:text-sm text-slate-400 max-w-2xl font-medium leading-relaxed">
                    Panduan integrasi pengiriman pesan WhatsApp otomatis, OTP, notifikasi tagihan, media gambar/dokumen, serta penerimaan callback webhook realtime.
                </p>
            </div>

            <!-- Base URL Pill Box -->
            <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-2xl space-y-1.5 flex-shrink-0">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">API Base Endpoint</span>
                <div class="flex items-center gap-2">
                    <code class="font-mono text-xs font-bold text-emerald-400">{{ $baseUrl }}/api/v1</code>
                    <button type="button" onclick="copyToClipboard('{{ $baseUrl }}/api/v1', 'Base URL berhasil disalin!')" class="p-1 text-slate-400 hover:text-white rounded-lg transition-colors cursor-pointer" title="Salin Base URL">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Language Code Switcher Bar -->
        <div class="mt-6 pt-5 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 mr-1">Pilih Bahasa Pemrograman:</span>
                
                <button @click="codeLang = 'curl'" :class="codeLang === 'curl' ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-3 py-1.5 rounded-xl text-xs font-mono transition-all cursor-pointer">
                    cURL
                </button>
                <button @click="codeLang = 'php'" :class="codeLang === 'php' ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-3 py-1.5 rounded-xl text-xs font-mono transition-all cursor-pointer">
                    PHP (Guzzle)
                </button>
                <button @click="codeLang = 'js'" :class="codeLang === 'js' ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-3 py-1.5 rounded-xl text-xs font-mono transition-all cursor-pointer">
                    Node.js (Axios)
                </button>
                <button @click="codeLang = 'python'" :class="codeLang === 'python' ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-3 py-1.5 rounded-xl text-xs font-mono transition-all cursor-pointer">
                    Python (Requests)
                </button>
            </div>

            <div class="text-[11px] text-slate-400 font-mono">
                API Key Anda: <strong class="text-blue-400">{{ $userApiKey }}</strong>
            </div>
        </div>
    </div>

    <!-- ================= MAIN DOCS CONTENT GRID ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Sticky Sidebar Navigation (3 cols) -->
        <aside class="lg:col-span-3 space-y-4">
            <div class="app-card p-4 bg-white dark:bg-[#111A2E] sticky top-4 space-y-3 font-medium text-xs">
                <div class="font-bold text-slate-900 dark:text-white uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    Daftar Isi Dokumentasi
                </div>

                <nav class="space-y-1">
                    <button @click="activeSection = 'all'" :class="activeSection === 'all' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                        <span>Semua Dokumentasi</span>
                    </button>

                    <button @click="activeSection = 'auth'" :class="activeSection === 'auth' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Autentikasi & Header</span>
                    </button>

                    <button @click="activeSection = 'ratelimit'" :class="activeSection === 'ratelimit' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                        <span>Batas Limit (Rate Limit)</span>
                    </button>

                    <div class="pt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">
                        REST Endpoints
                    </div>

                    <button @click="activeSection = 'send-text'" :class="activeSection === 'send-text' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">POST</span>
                        <span class="truncate">Kirim Pesan Teks</span>
                    </button>

                    <button @click="activeSection = 'send-template'" :class="activeSection === 'send-template' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400">POST</span>
                        <span class="truncate">Kirim Template Pesan</span>
                    </button>

                    <button @click="activeSection = 'send-button'" :class="activeSection === 'send-button' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-purple-100 dark:bg-purple-500/15 text-purple-700 dark:text-purple-400">POST</span>
                        <span class="truncate">Kirim Button Message</span>
                    </button>

                    <button @click="activeSection = 'send-media'" :class="activeSection === 'send-media' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">POST</span>
                        <span class="truncate">Kirim Gambar / File</span>
                    </button>

                    <button @click="activeSection = 'device-status'" :class="activeSection === 'device-status' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-400">GET</span>
                        <span class="truncate">Status Device WA</span>
                    </button>

                    <div class="pt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">
                        Callbacks & Errors
                    </div>

                    <button @click="activeSection = 'webhooks'" :class="activeSection === 'webhooks' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                        <span>Webhook Signature</span>
                    </button>

                    <button @click="activeSection = 'status-codes'" :class="activeSection === 'status-codes' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        <span>Status Kode & Errors</span>
                    </button>

                    <div class="pt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">
                        Integrasi
                    </div>

                    <button @click="activeSection = 'whmcs'" :class="activeSection === 'whmcs' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-bold border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg transition-all text-left cursor-pointer">
                        <i data-lucide="plug" class="w-4 h-4"></i>
                        <span>Module WHMCS</span>
                    </button>
                </nav>
            </div>
        </aside>

        <!-- Main Documentation Body (9 cols) -->
        <main class="lg:col-span-9 space-y-8">

            <!-- 1. AUTENTIKASI & HEADER -->
            <section id="section-auth" x-show="activeSection === 'all' || activeSection === 'auth'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">1. Autentikasi API Key</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Setiap HTTP Request ke REST API wajib menyertakan kunci API yang valid.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs text-slate-600 dark:text-slate-400 font-medium">
                    <p>
                        Anda dapat menyertakan API Key dalam request header menggunakan salah satu metode di bawah ini:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 font-mono">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase block">Metode 1: Standard Bearer Token</span>
                            <code class="text-blue-700 dark:text-blue-400 font-bold">Authorization: Bearer {{ $userApiKey }}</code>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase block">Metode 2: Custom Header</span>
                            <code class="text-blue-700 dark:text-blue-400 font-bold">X-API-KEY: {{ $userApiKey }}</code>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. RATE LIMITING -->
            <section id="section-ratelimit" x-show="activeSection === 'all' || activeSection === 'ratelimit'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="w-8 h-8 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">2. Batas Request (Rate Limiting)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Melindungi stabilitas server dari lonjakan trafik tak terduga.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                    Setiap kunci API memiliki kuota default **60 request per menit**. Setiap response API menyertakan header telemetry berikut:
                </p>

                <div class="p-3 bg-slate-950 text-white rounded-xl font-mono text-xs space-y-1">
                    <div class="text-slate-300">X-RateLimit-Limit: 60</div>
                    <div class="text-emerald-400">X-RateLimit-Remaining: 58</div>
                    <div class="text-slate-400">X-RateLimit-Reset: 1724930400</div>
                </div>
            </section>

            <!-- 3. ENDPOINT: KIRIM PESAN TEKS / OTP -->
            <section id="endpoint-send-text" x-show="activeSection === 'all' || activeSection === 'send-text'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900 dark:text-white">/api/v1/messages/send-text</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">KIRIM OTOMATIS TEKS & OTP</span>
                </div>

                <!-- Parameters Table -->
                <div class="space-y-2">
                    <h3 class="font-bold text-xs text-slate-700">Request JSON Parameters:</h3>
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs app-table">
                            <thead>
                                <tr>
                                    <th class="p-2.5">Parameter</th>
                                    <th class="p-2.5">Tipe</th>
                                    <th class="p-2.5">Wajib</th>
                                    <th class="p-2.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono text-[11px]">
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">device_id</td>
                                    <td class="p-2.5">integer</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">ID perangkat WhatsApp terhubung (Contoh: <code>1</code>).</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">phone</td>
                                    <td class="p-2.5">string</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">Nomor penerima WhatsApp format internasional (Contoh: <code>6281234567890</code>).</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">message</td>
                                    <td class="p-2.5">string</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">Isi pesan WhatsApp yang akan dikirim (Mendukung emote & ganti baris <code>\n</code>).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Interactive Code Snippets Container -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700">Contoh Kode Integrasi:</span>
                        <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 uppercase" x-text="codeLang">cURL</span>
                    </div>

                    <!-- cURL Snippet -->
                    <div x-show="codeLang === 'curl'" class="relative">
                        <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/send-text" \
  -H "Authorization: Bearer {{ $userApiKey }}" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP keamanan Anda: 893-102. Berlaku 5 menit. JANGAN BERIKAN KODE INI KEPADA SIAPAPUN."
  }'</code></pre>
                    </div>

                    <!-- PHP Snippet -->
                    <div x-show="codeLang === 'php'" style="display: none;" class="relative">
                        <pre class="bg-slate-950 text-amber-300 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>use Illuminate\Support\Facades\Http;

$response = Http::withToken('{{ $userApiKey }}')
    ->post('{{ $baseUrl }}/api/v1/messages/send-text', [
        'device_id' => 1,
        'phone' => '6281234567890',
        'message' => "Kode OTP keamanan Anda: 893-102. Berlaku 5 menit.",
    ]);

$result = $response->json();</code></pre>
                    </div>

                    <!-- JS Snippet -->
                    <div x-show="codeLang === 'js'" style="display: none;" class="relative">
                        <pre class="bg-slate-950 text-sky-300 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>const axios = require('axios');

const res = await axios.post('{{ $baseUrl }}/api/v1/messages/send-text', {
    device_id: 1,
    phone: "6281234567890",
    message: "Kode OTP keamanan Anda: 893-102. Berlaku 5 menit."
}, {
    headers: {
        'Authorization': `Bearer {{ $userApiKey }}`,
        'Content-Type': 'application/json'
    }
});

console.log(res.data);</code></pre>
                    </div>

                    <!-- Python Snippet -->
                    <div x-show="codeLang === 'python'" style="display: none;" class="relative">
                        <pre class="bg-slate-950 text-purple-300 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>import requests

url = "{{ $baseUrl }}/api/v1/messages/send-text"
headers = {
    "Authorization": "Bearer {{ $userApiKey }}",
    "Content-Type": "application/json"
}
payload = {
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP keamanan Anda: 893-102."
}

res = requests.post(url, json=payload, headers=headers)
print(res.json())</code></pre>
                    </div>
                </div>

                <!-- Sample JSON Response 200 OK -->
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh Respon Berhasil (HTTP 200 OK):</span>
                    <pre class="bg-slate-900 text-slate-300 p-3.5 rounded-xl font-mono text-xs border border-slate-800 overflow-x-auto">
{
  <span class="text-blue-400">"status"</span>: <span class="text-emerald-400">true</span>,
  <span class="text-blue-400">"message"</span>: <span class="text-emerald-300">"Pesan berhasil terkirim ke antrean WhatsApp"</span>,
  <span class="text-blue-400">"data"</span>: {
    <span class="text-blue-400">"message_id"</span>: <span class="text-amber-300">89021</span>,
    <span class="text-blue-400">"target"</span>: <span class="text-emerald-300">"6281234567890@s.whatsapp.net"</span>,
    <span class="text-blue-400">"status"</span>: <span class="text-emerald-300">"sent"</span>,
    <span class="text-blue-400">"created_at"</span>: <span class="text-emerald-300">"{{ now()->toIso8601String() }}"</span>
  }
}</pre>
                </div>
            </section>

            <!-- 4. ENDPOINT: KIRIM TEMPLATE PESAN (DYNAMIC PARAMETERS) -->
            <section id="endpoint-send-template" x-show="activeSection === 'all' || activeSection === 'send-template'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900 dark:text-white">/api/v1/messages/send-template</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">TEMPLATE SYSTEM</span>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Kirim pesan berdasarkan <strong>Template ID</strong> yang telah Anda buat di menu <em>Template Pesan</em>. Variabel seperti <code>{otp}</code>, <code>{name}</code>, <code>{code}</code> akan diisi secara otomatis dari object <code>variables</code>.
                </p>

                <div class="space-y-2">
                    <h3 class="font-bold text-xs text-slate-700">Request JSON Parameters:</h3>
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs app-table">
                            <thead>
                                <tr>
                                    <th class="p-2.5">Parameter</th>
                                    <th class="p-2.5">Tipe</th>
                                    <th class="p-2.5">Wajib</th>
                                    <th class="p-2.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono text-[11px]">
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">device_id</td>
                                    <td class="p-2.5">integer</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">ID perangkat WhatsApp terhubung.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">phone</td>
                                    <td class="p-2.5">string</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">Nomor WhatsApp penerima (misal: <code>081234567890</code>).</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">template_id</td>
                                    <td class="p-2.5">integer</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">ID unik template pesan milik akun Anda (Lihat badge <code>ID: #...</code> di menu Template).</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">variables</td>
                                    <td class="p-2.5">object / array</td>
                                    <td class="p-2.5 text-slate-400">Opsional</td>
                                    <td class="p-2.5 font-sans">Pasangan Key-Value untuk menggantikan placeholder <code>{key}</code> pada template (contoh: <code>{"otp": "884920", "name": "Budi"}</code>).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh Request cURL Template Message:</span>
                    <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/send-template" \
  -H "Authorization: Bearer {{ $userApiKey }}" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "template_id": 1,
    "variables": {
      "otp": "992014",
      "name": "Budi Santoso"
    }
  }'</code></pre>
                </div>
            </section>

            <!-- 5. ENDPOINT: KIRIM BUTTON & INTERACTIVE MESSAGE -->
            <section id="endpoint-send-button" x-show="activeSection === 'all' || activeSection === 'send-button'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-purple-600 text-white font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900 dark:text-white">/api/v1/messages/send-button</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">INTERACTIVE BUTTONS</span>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh Request cURL Button Message:</span>
                    <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/send-button" \
  -H "Authorization: Bearer {{ $userApiKey }}" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "title": "KONFIRMASI PESANAN",
    "body": "Halo Kak, mohon konfirmasi pesanan #INV-9901 Anda.",
    "footer": "LapakOTP Gateway System",
    "buttons": [
      { "type": "copy", "text": "Salin Kode Inv", "code": "INV-9901" },
      { "type": "url", "text": "Bayar Sekarang", "url": "https://lapakotp.com/pay/INV-9901" }
    ]
  }'</code></pre>
                </div>
            </section>

            <!-- 4. ENDPOINT: KIRIM MEDIA / GAMBAR / PDF -->
            <section id="endpoint-send-media" x-show="activeSection === 'all' || activeSection === 'send-media'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900 dark:text-white">/api/v1/messages/send-media</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">KIRIM GAMBAR / DOKUMEN PDF</span>
                </div>

                <div class="space-y-2">
                    <h3 class="font-bold text-xs text-slate-700">Request JSON Parameters:</h3>
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs app-table">
                            <thead>
                                <tr>
                                    <th class="p-2.5">Parameter</th>
                                    <th class="p-2.5">Tipe</th>
                                    <th class="p-2.5">Wajib</th>
                                    <th class="p-2.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono text-[11px]">
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">device_id</td>
                                    <td class="p-2.5">integer</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">ID perangkat terhubung.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">phone</td>
                                    <td class="p-2.5">string</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">Nomor tujuan (Contoh: <code>6281234567890</code>).</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">media_url</td>
                                    <td class="p-2.5">string (URL)</td>
                                    <td class="p-2.5 text-emerald-600 font-bold">Ya</td>
                                    <td class="p-2.5 font-sans">URL langsung (direct HTTPS link) file gambar PNG/JPG atau PDF.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-blue-600">caption</td>
                                    <td class="p-2.5">string</td>
                                    <td class="p-2.5 text-slate-400">Opsional</td>
                                    <td class="p-2.5 font-sans">Keterangan/caption teks di bawah media.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh cURL Kirim Media:</span>
                    <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/send-media" \
  -H "Authorization: Bearer {{ $userApiKey }}" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "media_url": "https://domain-anda.com/invoices/INV-1092.pdf",
    "caption": "Lampiran Tagihan Resmi Bulan Ini #INV-1092"
  }'</code></pre>
                </div>
            </section>

            <!-- 5. ENDPOINT: STATUS DEVICE -->
            <section id="endpoint-device-status" x-show="activeSection === 'all' || activeSection === 'device-status'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-extrabold rounded-lg font-mono text-xs">GET</span>
                        <h2 class="text-base font-bold font-mono text-slate-900 dark:text-white">/api/v1/devices</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">CEK DAFTAR DEVICE</span>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh cURL GET Devices:</span>
                    <pre class="bg-slate-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>curl -X GET "{{ $baseUrl }}/api/v1/devices" \
  -H "Authorization: Bearer {{ $userApiKey }}"</code></pre>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Respon JSON Status Perangkat:</span>
                    <pre class="bg-slate-900 text-slate-300 p-3.5 rounded-xl font-mono text-xs border border-slate-800 overflow-x-auto">
{
  <span class="text-blue-400">"status"</span>: <span class="text-emerald-400">true</span>,
  <span class="text-blue-400">"data"</span>: [
    {
      <span class="text-blue-400">"id"</span>: <span class="text-amber-300">1</span>,
      <span class="text-blue-400">"name"</span>: <span class="text-emerald-300">"WhatsApp CS Utama"</span>,
      <span class="text-blue-400">"phone_number"</span>: <span class="text-emerald-300">"6281234567890"</span>,
      <span class="text-blue-400">"status"</span>: <span class="text-emerald-300">"connected"</span>
    }
  ]
}</pre>
                </div>
            </section>

            <!-- 6. WEBHOOK HMAC SIGNATURE VERIFICATION -->
            <section id="section-webhooks" x-show="activeSection === 'all' || activeSection === 'webhooks'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">4. Verifikasi Keaslian Webhook Signature</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Header <code>X-WAGateway-Secret</code> mengamankan webhook dari manipulasi pihak ketiga.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                    Setiap request webhook dari server kami menyertakan header <code>X-WAGateway-Secret</code> yang berisi kunci rahasia Anda (<code>{{ $userWebhookSecret }}</code>).
                </p>

                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700">Contoh Kode Verifikasi di Server PHP Anda:</span>
                    <pre class="bg-slate-950 text-amber-300 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800"><code>$incomingSecret = $_SERVER['HTTP_X_WAGATEWAY_SECRET'] ?? '';
$mySecret = "{{ $userWebhookSecret }}";

if ($incomingSecret !== $mySecret) {
    http_response_code(401);
    exit("Unauthorized Webhook Call");
}

$payload = json_decode(file_get_contents('php://input'), true);
// Proses payload webhook...</code></pre>
                </div>
            </section>

            <!-- 7. KODE STATUS & ERROR HANDLING -->
            <section id="section-status-codes" x-show="activeSection === 'all' || activeSection === 'status-codes'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="w-8 h-8 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">5. Daftar Kode Status Response HTTP</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Penjelasan kode HTTP status yang dikembalikan oleh API.</p>
                    </div>
                </div>

                <div class="app-table-wrapper">
                    <table class="w-full text-left text-xs app-table">
                        <thead>
                            <tr>
                                <th class="p-3">Kode Status</th>
                                <th class="p-3">Arti Status</th>
                                <th class="p-3">Penjelasan & Solusi</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono text-[11px]">
                            <tr>
                                <td class="p-3 font-bold text-emerald-600 dark:text-emerald-400">200 OK</td>
                                <td class="p-3 font-sans font-bold">Sukses</td>
                                <td class="p-3 font-sans text-slate-600 dark:text-slate-400">Request berhasil diproses & pesan masuk ke antrean pengiriman WhatsApp.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-amber-600 dark:text-amber-400">400 Bad Request</td>
                                <td class="p-3 font-sans font-bold">Device Disconnected</td>
                                <td class="p-3 font-sans text-slate-600 dark:text-slate-400">Perangkat WhatsApp belum terhubung/logout. Lakukan scan QR terlebih dahulu.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600 dark:text-rose-400">401 Unauthorized</td>
                                <td class="p-3 font-sans font-bold">API Key Invalid</td>
                                <td class="p-3 font-sans text-slate-600 dark:text-slate-400">API Key tidak ditemukan atau sudah di-revoked. Periksa kembali header request.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600 dark:text-rose-400">422 Unprocessable</td>
                                <td class="p-3 font-sans font-bold">Validasi Parameter Gagal</td>
                                <td class="p-3 font-sans text-slate-600 dark:text-slate-400">Parameter wajib kurang (seperti `phone` atau `message` tidak diisi).</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600 dark:text-rose-400">429 Too Many Requests</td>
                                <td class="p-3 font-sans font-bold">Batas Kuota Harian/Rate Limit Exceeded</td>
                                <td class="p-3 font-sans text-slate-600 dark:text-slate-400">Anda telah mencapai limit kuota harian atau rate limit per menit.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 8. MODULE WHMCS -->
            <section id="section-whmcs" x-show="activeSection === 'all' || activeSection === 'whmcs'" class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-6">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="plug" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">6. Module WHMCS — Notifikasi Otomatis untuk Bisnis Hosting</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Integrasikan WhatsApp Gateway ke WHMCS: notifikasi invoice, order, tiket, domain, dan lainnya.</p>
                    </div>
                </div>

                <!-- Download Card -->
                <div class="rounded-2xl bg-slate-950 text-white p-6 relative overflow-hidden border border-slate-800">
                    <div class="absolute -top-12 -right-12 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold text-[10px] uppercase tracking-wider border border-emerald-500/30">Addon Module WHMCS</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-mono text-[10px] font-bold border border-blue-500/30">v1.0.0</span>
                            </div>
                            <h3 class="text-lg font-extrabold text-white">WhatsApp Gateway for WHMCS</h3>
                            <p class="text-xs text-slate-400 max-w-lg font-medium leading-relaxed">
                                Notifikasi WhatsApp untuk 23 event WHMCS (invoice, order, tiket, domain, suspend), template pesan yang bisa dikustomisasi, dan tombol CTA "Bayar Sekarang".
                            </p>
                        </div>
                        <a href="{{ route('download.whmcs-module') }}" download
                           class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-400/40 hover:-translate-y-0.5">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download .ZIP
                        </a>
                    </div>
                </div>

                <!-- Fitur -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 space-y-1.5">
                        <i data-lucide="bell-ring" class="w-5 h-5 text-blue-500"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">23 Event Notifikasi</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Invoice dibuat/jatuh tempo/lunas, order baru, tiket, domain, suspend/unsuspend, dan lainnya.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 space-y-1.5">
                        <i data-lucide="mouse-pointer-click" class="w-5 h-5 text-emerald-500"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Tombol CTA Interaktif</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Notifikasi invoice dikirim dengan tombol "Bayar Sekarang" & "Client Area" langsung di WhatsApp.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 space-y-1.5">
                        <i data-lucide="pencil-ruler" class="w-5 h-5 text-amber-500"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Template Editor</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Edit pesan per event dari admin WHMCS dengan merge field <code class="text-emerald-500">{firstname}</code>, <code class="text-emerald-500">{total}</code>, dll.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 space-y-1.5">
                        <i data-lucide="scroll-text" class="w-5 h-5 text-purple-500"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Log Notifikasi</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Riwayat semua notifikasi (terkirim/gagal) tersimpan di database WHMCS untuk audit.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 space-y-1.5">
                        <i data-lucide="shield-check" class="w-5 h-5 text-rose-500"></i>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Aman & Ringan</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Komunikasi via HTTPS + API key, tanpa menyimpan kredensial WhatsApp di server WHMCS.</p>
                    </div>
                </div>

                <!-- Panduan Pemasangan -->
                <div class="space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4 text-blue-500"></i>
                        Panduan Pemasangan
                    </h3>

                    <div class="space-y-3">
                        <div class="flex gap-3 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">1</div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Siapkan Panel Gateway</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Login ke panel WhatsApp Gateway, pastikan device WhatsApp Anda berstatus <strong class="text-emerald-500">Connected</strong> di menu Devices, lalu buat API Key di menu API Keys dan salin token <code class="text-emerald-500 font-mono">lpk_...</code>.</p>
                            </div>
                        </div>

                        <div class="flex gap-3 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">2</div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Upload Module ke WHMCS</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Download ZIP di atas, ekstrak, lalu upload folder <code class="text-emerald-500 font-mono">wagateway</code> ke direktori <code class="text-emerald-500 font-mono">modules/addons/</code> instalasi WHMCS Anda. Struktur akhir: <code class="text-emerald-500 font-mono">modules/addons/wagateway/wagateway.php</code></p>
                            </div>
                        </div>

                        <div class="flex gap-3 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">3</div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Aktifkan & Konfigurasi</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Masuk WHMCS Admin → <strong>System Settings → Addon Modules</strong> → cari <strong>WhatsApp Gateway Enterprise</strong> → <em>Activate</em> → <em>Configure</em>. Isi API URL, API Key, Device ID, dan (opsional) nomor support WhatsApp untuk floating button.</p>
                            </div>
                        </div>

                        <div class="flex gap-3 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">4</div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Test & Selesai</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Buka module → tab <strong>Dashboard</strong> → klik <em>Test Koneksi</em> dan <em>Kirim Test Message</em>. Sesuaikan template pesan di tab <strong>Template Notifikasi</strong>. Semua notifikasi event WHMCS kini terkirim via WhatsApp! 🎉</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event yang didukung -->
                <div class="space-y-3">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                        Event WHMCS yang Didukung
                    </h3>
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs app-table">
                            <thead>
                                <tr>
                                    <th class="p-3">Kategori</th>
                                    <th class="p-3">Event</th>
                                </tr>
                            </thead>
                            <tbody class="text-[11px]">
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Invoice & Billing</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Invoice Dibuat, Reminder Pembayaran, Jatuh Tempo, Pembayaran Berhasil, Refund, Dibatalkan</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Order & Product</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Order Baru, Order Dibayar, Order Dibatalkan</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Klien</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Registrasi Klien Baru, Password Diubah, Login</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Tiket Support</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Tiket Baru, Balasan Klien, Tiket Ditutup</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Domain</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Registrasi, Perpanjangan, Kedaluwarsa</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">Layanan</td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400">Addon Diaktifkan, Suspend, Unsuspend, Terminasi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>

    </div>

</div>
@endsection
