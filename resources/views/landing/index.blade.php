@extends('layouts.landing')

@section('title', 'WhatsApp Gateway Enterprise REST API - Cepat, Ringan & Realtime')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-12 pb-20 space-y-20 sm:space-y-28">

    <!-- HERO SECTION WITH LIVE INTERACTIVE SIMULATOR -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center" x-data="landingSimulator()">
        <!-- Left: Hero Text & Realtime Badge -->
        <div class="lg:col-span-6 space-y-6 sm:space-y-7">
            
            <!-- Live Status Telemetry Pill -->
            <div class="inline-flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>GATEWAY ENGINE: ONLINE</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-medium bg-blue-50 text-blue-700 border border-blue-200">
                    <i data-lucide="zap" class="w-3 h-3 text-blue-600"></i>
                    <span x-text="'Ping: ' + ping + ' ms'">Ping: 18 ms</span>
                </span>
            </div>

            <h1 class="font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight text-slate-900 leading-[1.12]">
                WhatsApp Gateway API <br class="hidden sm:block">
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-600 bg-clip-text text-transparent">
                    Sangat Cepat, Handal & Realtime
                </span>
            </h1>

            <p class="text-sm sm:text-base font-medium text-slate-600 leading-relaxed max-w-xl">
                Infrastruktur REST API berlatensi rendah untuk otomatisasi pengiriman pesan OTP, notifikasi transaksi, reminder tagihan, dan CS. Hubungkan nomor WhatsApp secara instan dengan <strong>Pairing Code 8-Digit</strong> atau <strong>Scan QR Code</strong>.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <a href="{{ route('register') }}" class="app-btn app-btn-primary text-xs sm:text-sm py-3 px-6 flex items-center gap-2.5 shadow-md shadow-blue-500/20">
                    <span>Mulai Integrasi Gratis</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="#simulator" class="app-btn app-btn-secondary text-xs sm:text-sm py-3 px-5 flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-4 h-4 text-blue-600"></i>
                    <span>Coba Live Simulator</span>
                </a>
            </div>

            <!-- Key Metric Stats -->
            <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 font-mono text-xs">
                <div class="space-y-1">
                    <div class="font-extrabold text-2xl text-slate-900">99.98%</div>
                    <div class="text-slate-500 font-semibold text-[11px] font-sans">Uptime Protocol</div>
                </div>
                <div class="space-y-1">
                    <div class="font-extrabold text-2xl text-blue-600">&lt; 20ms</div>
                    <div class="text-slate-500 font-semibold text-[11px] font-sans">Dispatch Latency</div>
                </div>
                <div class="space-y-1">
                    <div class="font-extrabold text-2xl text-emerald-600">UUIDv4</div>
                    <div class="text-slate-500 font-semibold text-[11px] font-sans">Session Isolated</div>
                </div>
            </div>
        </div>

        <!-- Right: Interactive Live Realtime WhatsApp Simulator & Telemetry Widget -->
        <div class="lg:col-span-6" id="simulator">
            <div class="app-card bg-slate-950 text-white rounded-2xl shadow-2xl border border-slate-800 overflow-hidden">
                <!-- Card Header -->
                <div class="px-5 py-3.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                        <span class="text-xs font-mono text-slate-400 ml-2">Live API Dispatch Simulator</span>
                    </div>

                    <!-- Simulator Mode Switcher -->
                    <div class="flex items-center bg-slate-950 p-1 rounded-lg border border-slate-800 text-[11px] font-mono">
                        <button @click="viewMode = 'simulator'" :class="viewMode === 'simulator' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white'" class="px-2.5 py-1 rounded-md transition-colors cursor-pointer flex items-center gap-1">
                            <i data-lucide="smartphone" class="w-3 h-3"></i>
                            <span>Simulator</span>
                        </button>
                        <button @click="viewMode = 'json'" :class="viewMode === 'json' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white'" class="px-2.5 py-1 rounded-md transition-colors cursor-pointer flex items-center gap-1">
                            <i data-lucide="code" class="w-3 h-3"></i>
                            <span>JSON Response</span>
                        </button>
                    </div>
                </div>

                <!-- Simulator Interactive Body -->
                <div class="p-5 sm:p-6 space-y-5">
                    <!-- Quick Inputs Form -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 font-sans">
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Nomor WhatsApp Target</label>
                            <input type="text" x-model="targetPhone" class="w-full bg-slate-900 border border-slate-750 text-white rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:border-blue-500" placeholder="6281234567890">
                        </div>
                        <div class="sm:col-span-7">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pesan OTP / Notifikasi</label>
                            <input type="text" x-model="targetMessage" class="w-full bg-slate-900 border border-slate-750 text-white rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:border-blue-500" placeholder="Kode OTP Anda: 894-201">
                        </div>
                    </div>

                    <!-- Dispatch Trigger Button -->
                    <button @click="triggerSimulatedSend()" :disabled="isSending" class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-xl font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-all shadow-lg shadow-blue-600/30 disabled:opacity-50">
                        <template x-if="!isSending">
                            <span class="flex items-center gap-2">
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                <span>Simulasi Kirim Pesan Realtime</span>
                            </span>
                        </template>
                        <template x-if="isSending">
                            <span class="flex items-center gap-2 font-mono">
                                <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i>
                                <span>Mengirim via Enterprise Core...</span>
                            </span>
                        </template>
                    </button>

                    <!-- VIEW MODE 1: PHONE CHAT SIMULATOR -->
                    <div x-show="viewMode === 'simulator'" class="space-y-3">
                        <div class="flex items-center justify-between text-xs border-b border-slate-800/80 pb-2 font-mono">
                            <span class="text-slate-400">Status Perangkat:</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold" 
                                  :class="sendStatus === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : (sendStatus === 'SENDING' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 animate-pulse' : 'bg-slate-800 text-slate-300')">
                                <span x-text="sendStatus === 'DELIVERED' ? 'DELIVERED (' + lastLatency + 'ms)' : (sendStatus === 'SENDING' ? 'DISPATCHING...' : 'READY')">IDLE</span>
                            </span>
                        </div>

                        <!-- Simulated WhatsApp Phone Screen -->
                        <div class="rounded-xl border border-slate-800 bg-[#0b141a] p-4 sm:p-5 font-sans min-h-[170px] flex flex-col justify-end relative overflow-hidden shadow-inner">
                            <!-- Subtle WhatsApp chat background pattern -->
                            <div class="absolute inset-0 opacity-5 pointer-events-none bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>

                            <div class="space-y-3 relative z-10">
                                <template x-for="(chat, index) in chats" :key="index">
                                    <div class="flex justify-end transition-all transform duration-300 translate-y-0 opacity-100">
                                        <div class="bg-[#005c4b] text-slate-100 rounded-2xl rounded-tr-xs px-3.5 py-2.5 max-w-[85%] sm:max-w-[75%] space-y-1 shadow-md border border-emerald-600/30">
                                            <p class="text-xs leading-relaxed font-normal whitespace-pre-wrap" x-text="chat.message"></p>
                                            <div class="flex items-center justify-end gap-1.5 text-[10px] text-emerald-200/70 font-mono">
                                                <span x-text="chat.time"></span>
                                                <!-- Single checkmark when sending, double blue checkmark when delivered -->
                                                <span x-show="chat.status === 'sending'" class="text-slate-300">✓</span>
                                                <span x-show="chat.status === 'delivered'" class="text-sky-300 font-bold">✓✓</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW MODE 2: JSON RESPONSE PAYLOAD -->
                    <div x-show="viewMode === 'json'" style="display: none;" class="space-y-2 font-mono text-xs">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 border-b border-slate-800 pb-2">
                            <span>HTTP POST /api/v1/messages/send-text</span>
                            <span class="text-emerald-400 font-bold">200 OK</span>
                        </div>
                        <pre class="p-3 bg-slate-900 border border-slate-800 rounded-xl text-emerald-400 overflow-x-auto text-[11px] leading-relaxed"><code x-text="jsonOutput"></code></pre>
                    </div>

                </div>

                <!-- Footer Bar Telemetry -->
                <div class="px-5 py-2.5 bg-slate-900/90 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>SHA-256 Hashed API Key</span>
                    </span>
                    <span class="text-slate-500">Rate Limit: 60 req/min</span>
                </div>
            </div>
        </div>
    </section>

    <!-- METODE KONEKSI PERANGKAT (DUAL HANDSHAKE SYSTEM) -->
    <section id="koneksi" class="space-y-8 pt-4">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <i data-lucide="cpu" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>DUAL CONNECTION PROTOCOL</span>
            </span>
            <h2 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Metode Koneksi Tanpa Ribet</h2>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Dua pilihan metode otentikasi yang disesuaikan dengan kebutuhan infrastruktur server Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <!-- Pairing Code Card -->
            <div class="app-card app-card-hover p-6 sm:p-8 bg-white space-y-5 border-blue-100 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center font-bold text-blue-600 shadow-2xs">
                        <i data-lucide="key-round" class="w-6 h-6"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-mono text-[11px] font-bold border border-blue-200">REKOMENDASI VPS</span>
                </div>

                <div class="space-y-2">
                    <h3 class="font-bold text-lg text-slate-900">1. Pairing Code 8-Karakter</h3>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">
                        Masukkan nomor WhatsApp ponsel Anda di console. Gateway secara instan meminta 8-digit kode pairing resmi dari server WhatsApp tanpa perlu scan kamera HP.
                    </p>
                </div>

                <!-- Live Pairing Code Visual Mockup -->
                <div class="p-3 bg-slate-900 text-white rounded-xl font-mono text-center space-y-1">
                    <div class="text-[10px] text-slate-400 uppercase tracking-widest">Contoh Display Kode Pairing</div>
                    <div class="font-extrabold text-xl sm:text-2xl text-emerald-400 tracking-widest">4829 - 1037</div>
                </div>

                <div class="space-y-2 text-xs font-medium text-slate-700 pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Sangat ideal untuk deploy VPS jarak jauh tanpa layar monitor</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Input kode langsung dari menu WhatsApp &gt; Perangkat Tertaut</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Handshake aman dan instan hanya dalam 3 detik</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="app-card app-card-hover p-6 sm:p-8 bg-white space-y-5 border-emerald-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center font-bold text-emerald-600 shadow-2xs">
                        <i data-lucide="qr-code" class="w-6 h-6"></i>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[11px] font-bold border border-emerald-200">1-STEP SCAN</span>
                </div>

                <div class="space-y-2">
                    <h3 class="font-bold text-lg text-slate-900">2. Scan QR Code Resolusi Tinggi</h3>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">
                        Metode visual konvensional. Tampilkan QR Code live di dashboard console lalu scan menggunakan kamera WhatsApp di ponsel Anda.
                    </p>
                </div>

                <!-- Live QR Visual Mockup -->
                <div class="p-3 bg-emerald-50/60 border border-emerald-100 rounded-xl flex items-center gap-3">
                    <div class="w-12 h-12 bg-white rounded-lg border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-2xs flex-shrink-0">
                        <i data-lucide="scan" class="w-6 h-6"></i>
                    </div>
                    <div class="text-xs space-y-0.5">
                        <strong class="text-slate-800 font-bold block">Auto Refresh Realtime</strong>
                        <p class="text-slate-500 text-[11px]">QR Code otomatis diperbarui sebelum masa berlaku habis.</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs font-medium text-slate-700 pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Proses menghubungkan instan satu langkah</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Dukungan auto reconnection saat perangkat offline</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-[10px]">✓</div>
                        <span>Isolasi sesi mandiri per nomor WhatsApp (UUIDv4)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR INFRASTRUKTUR ENTERPRISE -->
    <section id="fitur" class="space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                <i data-lucide="layers" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>ENTERPRISE FEATURES</span>
            </span>
            <h2 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Performa Tinggi & Keamanan Produk</h2>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Dirancang khusus untuk skala enterprise dengan keandalan maksimal.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Multi-Device & Session Isolated</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Kelola banyak nomor WhatsApp dalam satu akun console dengan session storage terisolasi berbasis UUIDv4.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="webhook" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Realtime Webhook Event</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Terima notifikasi pesan masuk dan status pesan (Sent, Delivered, Read) secara instant ke URL endpoint Anda.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">API Key SHA-256 Security</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Setiap request diautentikasi dengan token hashed SHA-256 yang terenkripsi dan rate limit teratur.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-sky-50 border border-sky-100 text-sky-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="image" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Support Pesan Gambar & Dokumen</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Kirim pesan teks biasa, pesan bergambar (JPG/PNG), serta dokumen PDF / invoice secara langsung via API.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-amber-50 border border-amber-100 text-amber-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Auto Daily Limit Scheduler</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Sistem pemulihan limit harian otomatis setiap tengah malam (00:00) agar operasional pesan tidak terganggu.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="app-card app-card-hover p-6 bg-white space-y-3.5 border-slate-200/90 shadow-2xs">
                <div class="w-10 h-10 bg-purple-50 border border-purple-100 text-purple-600 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                    <i data-lucide="code-2" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">Dokumentasi API Lengkap</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Panduan integrasi lengkap dengan contoh kode dalam bahasa cURL, Node.js, PHP Laravel, Python, dan Go.
                </p>
            </div>
        </div>
    </section>

    <!-- CODE PLAYGROUND & INTEGRATION SNIPPETS -->
    <section id="playground" class="space-y-6" x-data="codePlayground()">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <i data-lucide="code" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>DEVELOPER PLAYGROUND</span>
            </span>
            <h2 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Integrasi API Dalam 3 Menit</h2>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Pilih bahasa pemrograman favorit Anda dan langsung salin potongan kode integrasi.</p>
        </div>

        <div class="app-card bg-slate-950 text-white p-5 sm:p-7 font-mono text-xs overflow-hidden shadow-2xl border border-slate-800 rounded-2xl space-y-4">
            <!-- Language Selector Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3.5">
                <div class="flex items-center gap-2 overflow-x-auto py-1">
                    <button @click="lang = 'curl'" :class="lang === 'curl' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900 border-slate-800'" class="px-3 py-1.5 rounded-lg text-xs border transition-colors cursor-pointer">cURL</button>
                    <button @click="lang = 'node'" :class="lang === 'node' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900 border-slate-800'" class="px-3 py-1.5 rounded-lg text-xs border transition-colors cursor-pointer">Node.js</button>
                    <button @click="lang = 'php'" :class="lang === 'php' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900 border-slate-800'" class="px-3 py-1.5 rounded-lg text-xs border transition-colors cursor-pointer">PHP (Laravel)</button>
                    <button @click="lang = 'python'" :class="lang === 'python' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900 border-slate-800'" class="px-3 py-1.5 rounded-lg text-xs border transition-colors cursor-pointer">Python</button>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-slate-400 text-[11px] font-sans hidden sm:inline">POST /api/v1/messages/send-text</span>
                    <button @click="copySnippet()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-sans font-semibold flex items-center gap-1.5 transition-colors cursor-pointer border border-slate-700">
                        <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copied"></i>
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copied" style="display:none;"></i>
                        <span x-text="copied ? 'Tersalin!' : 'Copy Code'">Copy Code</span>
                    </button>
                </div>
            </div>

            <!-- Code Blocks -->
            <div x-show="lang === 'curl'">
                <pre class="text-emerald-400 overflow-x-auto p-2 leading-relaxed"><code>curl -X POST "{{ url('/api/v1/messages/send-text') }}" \
  -H "X-API-Key: lpk_live_your_secret_key" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP Anda: 894-201. Valid selama 5 menit."
  }'</code></pre>
            </div>

            <div x-show="lang === 'node'" style="display: none;">
                <pre class="text-sky-300 overflow-x-auto p-2 leading-relaxed"><code>const axios = require('axios');

async function sendOtp() {
  const response = await axios.post('{{ url('/api/v1/messages/send-text') }}', {
    device_id: 1,
    phone: '6281234567890',
    message: 'Kode OTP Anda: 894-201. Valid selama 5 menit.'
  }, {
    headers: { 'X-API-Key': 'lpk_live_your_secret_key' }
  });

  console.log(response.data);
}

sendOtp();</code></pre>
            </div>

            <div x-show="lang === 'php'" style="display: none;">
                <pre class="text-amber-300 overflow-x-auto p-2 leading-relaxed"><code>use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-API-Key' => 'lpk_live_your_secret_key',
])->post('{{ url('/api/v1/messages/send-text') }}', [
    'device_id' => 1,
    'phone' => '6281234567890',
    'message' => 'Kode OTP Anda: 894-201. Valid selama 5 menit.',
]);

$result = $response->json();</code></pre>
            </div>

            <div x-show="lang === 'python'" style="display: none;">
                <pre class="text-indigo-300 overflow-x-auto p-2 leading-relaxed"><code>import requests

url = "{{ url('/api/v1/messages/send-text') }}"
headers = {"X-API-Key": "lpk_live_your_secret_key"}
payload = {
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP Anda: 894-201. Valid selama 5 menit."
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())</code></pre>
            </div>
        </div>
    </section>

    <!-- FAQ ACCORDION -->
    <section class="space-y-8 max-w-4xl mx-auto" x-data="{ openFaq: null }">
        <div class="text-center space-y-2">
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-xs sm:text-sm text-slate-600 font-medium">Jawaban cepat seputar penggunaan WhatsApp Gateway Enterprise.</p>
        </div>

        <div class="space-y-3 font-sans">
            <!-- FAQ 1 -->
            <div class="app-card bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
                <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-5 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-50/80 transition-colors">
                    <span>Apakah saya membutuhkan layar monitor untuk melakukan koneksi Pairing Code 8-Digit?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="openFaq === 1 ? 'rotate-180 text-blue-600' : ''"></i>
                </button>
                <div x-show="openFaq === 1" class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                    Tidak perlu. Metode Pairing Code 8-Digit dirancang khusus agar dapat berjalan lancar di VPS server tanpa antarmuka GUI (Headless Server). Cukup masukkan nomor ponsel di console, lalu masukkan 8-digit kode yang muncul ke menu <em>Perangkat Tertaut</em> pada aplikasi WhatsApp HP Anda.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="app-card bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
                <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-5 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-50/80 transition-colors">
                    <span>Bagaimana cara kerja Webhook event pesan masuk?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="openFaq === 2 ? 'rotate-180 text-blue-600' : ''"></i>
                </button>
                <div x-show="openFaq === 2" class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                    Setiap kali nomor WhatsApp Anda menerima pesan baru atau terjadi perubahan status pengiriman (Sent/Delivered/Read), gateway secara otomatis melakukan HTTP POST request berisi JSON payload ke URL Webhook yang Anda daftarkan di dashboard.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="app-card bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
                <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-5 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between gap-4 cursor-pointer hover:bg-slate-50/80 transition-colors">
                    <span>Apakah mendukung Single Sign-On (SSO) Google dan GitHub?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="openFaq === 3 ? 'rotate-180 text-blue-600' : ''"></i>
                </button>
                <div x-show="openFaq === 3" class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                    Ya! Platform kami telah terintegrasi penuh dengan OAuth SSO Google & GitHub. Pengguna dapat login dan mendaftar dengan aman dalam 1-klik, serta menghubungkan akun SSO di menu Profil.
                </div>
            </div>
        </div>
    </section>

    <!-- BOTTOM CTA BANNER -->
    <section class="app-card bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-800 text-white p-8 sm:p-12 rounded-3xl text-center space-y-4 shadow-xl border border-blue-500/30">
        <h2 class="font-extrabold text-2xl sm:text-4xl text-white tracking-tight">
            Mulai Kirim Pesan WhatsApp Hari Ini
        </h2>
        <p class="text-xs sm:text-sm text-blue-100 font-medium max-w-md mx-auto leading-relaxed">
            Daftar akun gratis sekarang, hubungkan nomor WhatsApp Anda, dan dapatkan API Key dalam hitungan detik.
        </p>
        <div class="pt-3">
            <a href="{{ route('register') }}" class="app-btn bg-white hover:bg-slate-50 text-blue-700 font-bold text-xs sm:text-sm py-3.5 px-7 inline-flex items-center gap-2 shadow-lg hover:scale-105 transition-all">
                <span>Daftar Akun Gratis</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </section>

</div>

<!-- Realtime Landing Page Alpine.js Script -->
<script>
function landingSimulator() {
    return {
        viewMode: 'simulator',
        targetPhone: '6281234567890',
        targetMessage: 'Kode OTP Anda: 894-201. Valid 5 menit.',
        isSending: false,
        sendStatus: 'READY',
        ping: 18,
        lastLatency: 14,
        chats: [
            {
                message: 'Halo! Selamat datang di WhatsApp Gateway Enterprise.',
                time: '12:00',
                status: 'delivered'
            }
        ],
        jsonOutput: '// Klik tombol "Simulasi Kirim Pesan Realtime" untuk melihat live JSON response',

        init() {
            // Subtle ping fluctuation for realtime telemetry feel
            setInterval(() => {
                this.ping = Math.floor(Math.random() * (24 - 15 + 1)) + 15;
            }, 3000);
        },

        triggerSimulatedSend() {
            if (!this.targetMessage || this.isSending) return;

            this.isSending = true;
            this.sendStatus = 'SENDING';

            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            const currentPhone = this.targetPhone || '6281234567890';
            const currentMsg = this.targetMessage;

            // Push chat with 'sending' status (single checkmark)
            const chatIndex = this.chats.length;
            this.chats.push({
                message: currentMsg,
                time: timeStr,
                status: 'sending'
            });

            // Simulate realistic API dispatch latency (350ms)
            setTimeout(() => {
                this.lastLatency = Math.floor(Math.random() * (19 - 12 + 1)) + 12;
                this.chats[chatIndex].status = 'delivered';
                this.sendStatus = 'DELIVERED';
                this.isSending = false;

                // Format live JSON response
                this.jsonOutput = JSON.stringify({
                    "status": true,
                    "code": 200,
                    "message": "Pesan berhasil diproses ke dalam antrean dispatcher.",
                    "data": {
                        "message_id": "MSG-" + Math.random().toString(36).substring(2, 10).toUpperCase(),
                        "device_id": 1,
                        "recipient": currentPhone,
                        "content": currentMsg,
                        "dispatch_status": "DELIVERED",
                        "latency_ms": this.lastLatency,
                        "timestamp": new Date().toISOString()
                    }
                }, null, 2);
            }, 400);
        }
    }
}

function codePlayground() {
    return {
        lang: 'curl',
        copied: false,
        copySnippet() {
            let textToCopy = '';
            if (this.lang === 'curl') {
                textToCopy = `curl -X POST "{{ url('/api/v1/messages/send-text') }}" \\\n  -H "X-API-Key: lpk_live_your_secret_key" \\\n  -H "Content-Type: application/json" \\\n  -d '{\n    "device_id": 1,\n    "phone": "6281234567890",\n    "message": "Kode OTP Anda: 894-201. Valid selama 5 menit."\n  }'`;
            } else if (this.lang === 'node') {
                textToCopy = `const axios = require('axios');\n\nasync function sendOtp() {\n  const response = await axios.post('{{ url('/api/v1/messages/send-text') }}', {\n    device_id: 1,\n    phone: '6281234567890',\n    message: 'Kode OTP Anda: 894-201. Valid selama 5 menit.'\n  }, {\n    headers: { 'X-API-Key': 'lpk_live_your_secret_key' }\n  });\n  console.log(response.data);\n}\nsendOtp();`;
            } else if (this.lang === 'php') {
                textToCopy = `use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::withHeaders([\n    'X-API-Key' => 'lpk_live_your_secret_key',\n])->post('{{ url('/api/v1/messages/send-text') }}', [\n    'device_id' => 1,\n    'phone' => '6281234567890',\n    'message' => 'Kode OTP Anda: 894-201. Valid selama 5 menit.',\n]);`;
            } else if (this.lang === 'python') {
                textToCopy = `import requests\n\nurl = "{{ url('/api/v1/messages/send-text') }}"\nheaders = {"X-API-Key": "lpk_live_your_secret_key"}\npayload = {\n    "device_id": 1,\n    "phone": "6281234567890",\n    "message": "Kode OTP Anda: 894-201. Valid selama 5 menit."\n}\nresponse = requests.post(url, json=payload, headers=headers)\nprint(response.json())`;
            }

            navigator.clipboard.writeText(textToCopy);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
    }
}
</script>
@endsection
