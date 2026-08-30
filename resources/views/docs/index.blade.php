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
            <div class="app-card p-4 bg-white sticky top-4 space-y-3 font-medium text-xs">
                <div class="font-bold text-slate-900 uppercase text-[10px] tracking-wider border-b border-slate-100 pb-2">
                    Daftar Isi Dokumentasi
                </div>

                <nav class="space-y-1">
                    <a href="#section-auth" @click="activeSection = 'auth'" :class="activeSection === 'auth' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Autentikasi & Header</span>
                    </a>

                    <a href="#section-ratelimit" @click="activeSection = 'ratelimit'" :class="activeSection === 'ratelimit' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                        <span>Batas Limit (Rate Limit)</span>
                    </a>

                    <div class="pt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">
                        REST Endpoints
                    </div>

                    <a href="#endpoint-send-text" @click="activeSection = 'send-text'" :class="activeSection === 'send-text' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-700">POST</span>
                        <span class="truncate">Kirim Pesan Teks</span>
                    </a>

                    <a href="#endpoint-send-template" @click="activeSection = 'send-template'" :class="activeSection === 'send-template' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 text-blue-700">POST</span>
                        <span class="truncate">Kirim Template Pesan</span>
                    </a>

                    <a href="#endpoint-send-button" @click="activeSection = 'send-button'" :class="activeSection === 'send-button' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-purple-100 text-purple-700">POST</span>
                        <span class="truncate">Kirim Button Message</span>
                    </a>

                    <a href="#endpoint-send-media" @click="activeSection = 'send-media'" :class="activeSection === 'send-media' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-700">POST</span>
                        <span class="truncate">Kirim Gambar / File</span>
                    </a>

                    <a href="#endpoint-device-status" @click="activeSection = 'device-status'" :class="activeSection === 'device-status' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 text-blue-700">GET</span>
                        <span class="truncate">Status Device WA</span>
                    </a>

                    <div class="pt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3">
                        Callbacks & Errors
                    </div>

                    <a href="#section-webhooks" @click="activeSection = 'webhooks'" :class="activeSection === 'webhooks' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                        <span>Webhook Signature</span>
                    </a>

                    <a href="#section-status-codes" @click="activeSection = 'status-codes'" :class="activeSection === 'status-codes' ? 'bg-blue-50 text-blue-700 font-bold border-l-2 border-blue-600' : 'text-slate-600 hover:bg-slate-50'" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        <span>Status Kode & Errors</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Documentation Body (9 cols) -->
        <main class="lg:col-span-9 space-y-8">

            <!-- 1. AUTENTIKASI & HEADER -->
            <section id="section-auth" class="app-card p-6 bg-white space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">1. Autentikasi API Key</h2>
                        <p class="text-xs text-slate-500 font-medium">Setiap HTTP Request ke REST API wajib menyertakan kunci API yang valid.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs text-slate-600 font-medium">
                    <p>
                        Anda dapat menyertakan API Key dalam request header menggunakan salah satu metode di bawah ini:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 font-mono">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Metode 1: Standard Bearer Token</span>
                            <code class="text-blue-700 font-bold">Authorization: Bearer {{ $userApiKey }}</code>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Metode 2: Custom Header</span>
                            <code class="text-blue-700 font-bold">X-API-KEY: {{ $userApiKey }}</code>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. RATE LIMITING -->
            <section id="section-ratelimit" class="app-card p-6 bg-white space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">2. Batas Request (Rate Limiting)</h2>
                        <p class="text-xs text-slate-500 font-medium">Melindungi stabilitas server dari lonjakan trafik tak terduga.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-medium">
                    Setiap kunci API memiliki kuota default **60 request per menit**. Setiap response API menyertakan header telemetry berikut:
                </p>

                <div class="p-3 bg-slate-950 text-white rounded-xl font-mono text-xs space-y-1">
                    <div class="text-slate-300">X-RateLimit-Limit: 60</div>
                    <div class="text-emerald-400">X-RateLimit-Remaining: 58</div>
                    <div class="text-slate-400">X-RateLimit-Reset: 1724930400</div>
                </div>
            </section>

            <!-- 3. ENDPOINT: KIRIM PESAN TEKS / OTP -->
            <section id="endpoint-send-text" class="app-card p-6 bg-white space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900">/api/v1/messages/send-text</h2>
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
                        <span class="text-[10px] font-mono text-slate-400 uppercase" x-text="codeLang">cURL</span>
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
            <section id="endpoint-send-template" class="app-card p-6 bg-white space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900">/api/v1/messages/send-template</h2>
                    </div>
                    <span class="app-tag app-tag-blue text-[10px]">TEMPLATE SYSTEM</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
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
            <section id="endpoint-send-button" class="app-card p-6 bg-white space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-purple-600 text-white font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900">/api/v1/messages/send-button</h2>
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
            <section id="endpoint-send-media" class="app-card p-6 bg-white space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-extrabold rounded-lg font-mono text-xs">POST</span>
                        <h2 class="text-base font-bold font-mono text-slate-900">/api/v1/messages/send-media</h2>
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
            <section id="endpoint-device-status" class="app-card p-6 bg-white space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-extrabold rounded-lg font-mono text-xs">GET</span>
                        <h2 class="text-base font-bold font-mono text-slate-900">/api/v1/devices</h2>
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
            <section id="section-webhooks" class="app-card p-6 bg-white space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">4. Verifikasi Keaslian Webhook Signature</h2>
                        <p class="text-xs text-slate-500 font-medium">Header <code>X-WAGateway-Secret</code> mengamankan webhook dari manipulasi pihak ketiga.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-medium">
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
            <section id="section-status-codes" class="app-card p-6 bg-white space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">5. Daftar Kode Status Response HTTP</h2>
                        <p class="text-xs text-slate-500 font-medium">Penjelasan kode HTTP status yang dikembalikan oleh API.</p>
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
                                <td class="p-3 font-bold text-emerald-600">200 OK</td>
                                <td class="p-3 font-sans font-bold">Sukses</td>
                                <td class="p-3 font-sans text-slate-600">Request berhasil diproses & pesan masuk ke antrean pengiriman WhatsApp.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-amber-600">400 Bad Request</td>
                                <td class="p-3 font-sans font-bold">Device Disconnected</td>
                                <td class="p-3 font-sans text-slate-600">Perangkat WhatsApp belum terhubung/logout. Lakukan scan QR terlebih dahulu.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600">401 Unauthorized</td>
                                <td class="p-3 font-sans font-bold">API Key Invalid</td>
                                <td class="p-3 font-sans text-slate-600">API Key tidak ditemukan atau sudah di-revoke. Periksa kembali header request.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600">422 Unprocessable</td>
                                <td class="p-3 font-sans font-bold">Validasi Parameter Gagal</td>
                                <td class="p-3 font-sans text-slate-600">Parameter wajib kurang (seperti `phone` atau `message` tidak diisi).</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-rose-600">429 Too Many Requests</td>
                                <td class="p-3 font-sans font-bold">Batas Kuota Harian/Rate Limit Exceeded</td>
                                <td class="p-3 font-sans text-slate-600">Anda telah mencapai limit kuota harian atau rate limit per menit.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>

    </div>

</div>
@endsection
