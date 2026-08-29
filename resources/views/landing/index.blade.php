@extends('layouts.landing')

@section('title', 'Whatsapp Gateway Enterprise & Multi-Device REST API')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-16 pb-20 space-y-24">

    <!-- HERO SECTION -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        <!-- Left: Hero Text -->
        <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2">
                <span class="app-tag app-tag-blue">
                    <i data-lucide="cpu" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>BAILEYS V7.0 CORE</span>
                </span>
                <span class="app-tag app-tag-emerald">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>MULTI-DEVICE ACTIVE</span>
                </span>
            </div>

            <h1 class="font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight text-slate-900 leading-[1.15]">
                WhatsApp Gateway API <br class="hidden sm:block">
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 bg-clip-text text-transparent">
                    Cepat, Stabil & Terpadu
                </span>
            </h1>

            <p class="text-sm sm:text-base font-medium text-slate-600 leading-relaxed max-w-xl">
                Otomatisasi pengiriman pesan OTP, notifikasi transaksi, reminder tagihan, dan customer service melalui REST API berlatensi rendah. Hubungkan nomor secara instan dengan <strong>Scan QR Code</strong> atau <strong>Pairing Code 8-Digit</strong>.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('register') }}" class="app-btn app-btn-primary text-sm py-3 px-5 flex items-center gap-2">
                    <span>Mulai Integrasi Gratis</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="#koneksi" class="app-btn app-btn-secondary text-sm py-3 px-4 flex items-center gap-2">
                    <i data-lucide="scan" class="w-4 h-4 text-blue-600"></i>
                    <span>Pilihan Metode Koneksi</span>
                </a>
            </div>

            <!-- Highlights -->
            <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 font-mono text-xs">
                <div class="space-y-0.5">
                    <div class="font-extrabold text-2xl text-slate-900">99.9%</div>
                    <div class="text-slate-500 font-semibold">Tingkat Uptime</div>
                </div>
                <div class="space-y-0.5">
                    <div class="font-extrabold text-2xl text-blue-600">&lt; 1s</div>
                    <div class="text-slate-500 font-semibold">Latency Dispatch</div>
                </div>
                <div class="space-y-0.5">
                    <div class="font-extrabold text-2xl text-slate-900">UUIDv4</div>
                    <div class="text-slate-500 font-semibold">Session Isolation</div>
                </div>
            </div>
        </div>

        <!-- Right: Telemetry & Live Interactive Preview -->
        <div class="lg:col-span-5">
            <div class="app-card p-6 bg-white space-y-4 shadow-md border-blue-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="font-bold text-sm text-slate-900">Gateway Telemetry</span>
                    </div>
                    <span class="app-tag app-tag-emerald text-[10px]">OPERATIONAL</span>
                </div>

                <div class="space-y-2.5 font-mono text-xs">
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Engine Protocol</span>
                        <strong class="text-slate-900">@whiskeysockets/baileys</strong>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Versi Core</span>
                        <strong class="text-blue-600 font-bold">v7.0.0-rc.14</strong>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Metode Pairing</span>
                        <strong class="text-slate-900">QR Code + Pairing Code</strong>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Dispatcher Webhook</span>
                        <strong class="text-emerald-600 font-bold">Realtime HTTP POST</strong>
                    </div>
                </div>

                <div class="pt-1">
                    <a href="{{ route('login') }}" class="w-full app-btn app-btn-soft-blue text-xs py-2.5 text-center flex items-center justify-center gap-2">
                        <span>Buka Console Management</span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- METODE KONEKSI -->
    <section id="koneksi" class="space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="app-tag app-tag-blue text-[10px]">DUAL CONNECTION ARCHITECTURE</span>
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900">Fleksibilitas Menghubungkan Nomor</h2>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Pilih metode yang paling sesuai dengan kebutuhan server atau perangkat Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pairing Code Card -->
            <div class="app-card app-card-hover p-6 sm:p-7 bg-white space-y-4">
                <div class="w-11 h-11 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center font-bold text-blue-600 shadow-2xs">
                    <i data-lucide="key-round" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-900">1. Pairing Code 8-Karakter</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1.5 leading-relaxed">
                        Cukup masukkan nomor WhatsApp ponsel Anda. Gateway secara langsung melakukan handshake dengan WhatsApp server untuk meminta 8-digit kode pairing resmi.
                    </p>
                </div>
                <div class="space-y-2 text-xs font-semibold text-slate-700 pt-2 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Tanpa memerlukan kamera untuk scan QR</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Input kode langsung di menu Perangkat Tertaut WhatsApp HP</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Sangat ideal untuk deploy VPS jarak jauh tanpa monitor</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="app-card app-card-hover p-6 sm:p-7 bg-white space-y-4">
                <div class="w-11 h-11 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center font-bold text-emerald-600 shadow-2xs">
                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-900">2. Scan QR Code Resolusi Tinggi</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1.5 leading-relaxed">
                        Pilihan koneksi konvensional 1 langkah scan. Tampilkan QR Code live di layar console lalu scan menggunakan kamera WhatsApp ponsel.
                    </p>
                </div>
                <div class="space-y-2 text-xs font-semibold text-slate-700 pt-2 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Otentikasi instan satu langkah</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Auto refresh real-time ketika QR kedaluwarsa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Multi-session terisolasi per nomor telepon dengan UUIDv4</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR UTAMA -->
    <section id="fitur" class="space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="app-tag app-tag-blue text-[10px]">INFRASTRUCTURE</span>
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900">Performa Tinggi & Handal</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="app-card app-card-hover p-6 space-y-3">
                <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Multi-Device & Multi-Session</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Kelola beberapa nomor WhatsApp dalam satu akun dengan session storage mandiri berbasis UUIDv4.
                </p>
            </div>

            <div class="app-card app-card-hover p-6 space-y-3">
                <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                    <i data-lucide="webhook" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Webhook Realtime Event</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Terima notifikasi pesan masuk, status delivery, dan status koneksi secara real-time ke endpoint server Anda.
                </p>
            </div>

            <div class="app-card app-card-hover p-6 space-y-3">
                <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">API Key Rate Limiting</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Kontrol granular pengiriman pesan dengan rate limit per menit dan autentikasi token SHA-256 hashed.
                </p>
            </div>
        </div>
    </section>

    <!-- CODE PLAYGROUND SECTION -->
    <section id="playground" class="space-y-6" x-data="{ lang: 'curl' }">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="app-tag app-tag-blue text-[10px]">DEVELOPER READY</span>
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900">Integrasi API Dalam 3 Menit</h2>
        </div>

        <div class="app-card bg-slate-950 text-white p-5 sm:p-7 font-mono text-xs overflow-hidden shadow-xl border-slate-800">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <button @click="lang = 'curl'" :class="lang === 'curl' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1 rounded-lg text-xs border border-slate-700 cursor-pointer transition-colors">cURL</button>
                    <button @click="lang = 'node'" :class="lang === 'node' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1 rounded-lg text-xs border border-slate-700 cursor-pointer transition-colors">Node.js</button>
                    <button @click="lang = 'php'" :class="lang === 'php' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1 rounded-lg text-xs border border-slate-700 cursor-pointer transition-colors">PHP</button>
                </div>
                <span class="text-slate-400 text-[11px] font-sans">POST /api/v1/messages/send-text</span>
            </div>

            <div x-show="lang === 'curl'">
                <pre class="text-emerald-400 overflow-x-auto p-1 leading-relaxed"><code>curl -X POST "{{ url('/api/v1/messages/send-text') }}" \
  -H "X-API-Key: lpk_live_your_secret_key" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP Anda: 894-201. Berlaku selama 5 menit."
  }'</code></pre>
            </div>

            <div x-show="lang === 'node'" style="display: none;">
                <pre class="text-sky-300 overflow-x-auto p-1 leading-relaxed"><code>const axios = require('axios');

await axios.post('{{ url('/api/v1/messages/send-text') }}', {
  device_id: 1,
  phone: '6281234567890',
  message: 'Kode OTP Anda: 894-201. Berlaku selama 5 menit.'
}, {
  headers: { 'X-API-Key': 'lpk_live_your_secret_key' }
});</code></pre>
            </div>

            <div x-show="lang === 'php'" style="display: none;">
                <pre class="text-amber-300 overflow-x-auto p-1 leading-relaxed"><code>use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-API-Key' => 'lpk_live_your_secret_key',
])->post('{{ url('/api/v1/messages/send-text') }}', [
    'device_id' => 1,
    'phone' => '6281234567890',
    'message' => 'Kode OTP Anda: 894-201. Berlaku selama 5 menit.',
]);</code></pre>
            </div>
        </div>
    </section>

    <!-- BOTTOM CTA BANNER -->
    <section class="app-card bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white p-8 sm:p-12 text-center space-y-4 shadow-xl border-blue-500/30">
        <h2 class="font-extrabold text-2xl sm:text-4xl text-white">
            Mulai Kirim Pesan WhatsApp Hari Ini
        </h2>
        <p class="text-xs sm:text-sm text-blue-100 font-medium max-w-md mx-auto leading-relaxed">
            Daftar akun gratis sekarang, hubungkan nomor WhatsApp Anda, dan dapatkan API Key dalam hitungan menit.
        </p>
        <div class="pt-2">
            <a href="{{ route('register') }}" class="app-btn bg-white hover:bg-slate-50 text-blue-700 font-bold text-xs sm:text-sm py-3 px-6 inline-flex items-center gap-2 shadow-md">
                <span>Daftar Akun Gratis</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </section>

</div>
@endsection
