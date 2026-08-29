@extends('layouts.landing')

@section('title', 'Tanya Jawab (FAQ) - Whatsapp Gateway Enterprise')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10" x-data="{ active: null, search: '' }">

    <div class="border-b border-slate-200/80 pb-4 space-y-2">
        <span class="app-tag app-tag-blue text-[9px]">KNOWLEDGE BASE</span>
        <h1 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium">Temukan panduan teknis dan jawaban seputar integrasi LAPAKOTP Gateway.</p>
    </div>

    <!-- Search Box -->
    <div class="app-card p-2.5 bg-white flex items-center gap-3 border-slate-200/80">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 ml-2"></i>
        <input type="text" x-model="search" placeholder="Cari topik atau pertanyaan (contoh: pairing code, webhook, limit)..." class="w-full text-xs font-medium border-none outline-none py-1.5 px-1 text-slate-900 placeholder:text-slate-400">
    </div>

    <div class="space-y-4">
        
        <!-- FAQ 1 -->
        <div class="app-card p-5 sm:p-6 bg-white space-y-2 cursor-pointer transition-all hover:border-blue-200" @click="active = active === 1 ? null : 1" x-show="!search || 'pairing code qr perbedaan scan'.includes(search.toLowerCase())">
            <div class="flex items-center justify-between font-bold text-sm sm:text-base text-slate-900">
                <span>Apa perbedaan metode Scan QR Code dan Pairing Code 8-Digit?</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="active === 1 ? 'rotate-180 text-blue-600' : ''"></i>
            </div>
            <div x-show="active === 1" class="text-xs sm:text-sm text-slate-600 font-medium pt-3 border-t border-slate-100 leading-relaxed" style="display: none;">
                Metode <strong>Scan QR</strong> menggunakan kamera ponsel untuk memindai kode QR visual di layar console. Sedangkan metode <strong>Pairing Code</strong> memungkinkan server meminta 8-digit kode alfanumerik dari WhatsApp secara langsung ke nomor ponsel Anda, kemudian Anda tinggal mengetik kode tersebut pada menu <em>Perangkat Tertaut</em> di WhatsApp ponsel Anda.
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="app-card p-5 sm:p-6 bg-white space-y-2 cursor-pointer transition-all hover:border-blue-200" @click="active = active === 2 ? null : 2" x-show="!search || 'limit kuota harian reset 00:05 pesan'.includes(search.toLowerCase())">
            <div class="flex items-center justify-between font-bold text-sm sm:text-base text-slate-900">
                <span>Bagaimana mekanisme kuota pengiriman pesan harian?</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="active === 2 ? 'rotate-180 text-blue-600' : ''"></i>
            </div>
            <div x-show="active === 2" class="text-xs sm:text-sm text-slate-600 font-medium pt-3 border-t border-slate-100 leading-relaxed" style="display: none;">
                Setiap akun pengguna memiliki alokasi kuota pesan harian (misal: 500 pesan/hari) dan batas jumlah perangkat (device limit). Kuota pesan yang terpakai akan direset secara otomatis oleh sistem terjadwal setiap hari pada pukul <strong>00:05 WIB</strong>.
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="app-card p-5 sm:p-6 bg-white space-y-2 cursor-pointer transition-all hover:border-blue-200" @click="active = active === 3 ? null : 3" x-show="!search || 'media gambar pdf dokumen video audio'.includes(search.toLowerCase())">
            <div class="flex items-center justify-between font-bold text-sm sm:text-base text-slate-900">
                <span>Apakah mendukung pengiriman berkas media seperti Gambar dan Dokumen PDF?</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="active === 3 ? 'rotate-180 text-blue-600' : ''"></i>
            </div>
            <div x-show="active === 3" class="text-xs sm:text-sm text-slate-600 font-medium pt-3 border-t border-slate-100 leading-relaxed" style="display: none;">
                Ya, platform mendukung penuh pengiriman pesan teks biasa, berkas gambar (JPEG/PNG), dokumen (PDF), dan berkas audio melalui endpoint REST API <code>/api/v1/messages/send-media</code> dengan menyertakan direct URL berkas.
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="app-card p-5 sm:p-6 bg-white space-y-2 cursor-pointer transition-all hover:border-blue-200" @click="active = active === 4 ? null : 4" x-show="!search || 'webhook callback pesan masuk event'.includes(search.toLowerCase())">
            <div class="flex items-center justify-between font-bold text-sm sm:text-base text-slate-900">
                <span>Bagaimana cara menerima pesan masuk (Inbound Message)?</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="active === 4 ? 'rotate-180 text-blue-600' : ''"></i>
            </div>
            <div x-show="active === 4" class="text-xs sm:text-sm text-slate-600 font-medium pt-3 border-t border-slate-100 leading-relaxed" style="display: none;">
                Daftarkan URL endpoint server Anda pada menu <strong>Webhook Callbacks</strong>. Setiap ada pesan masuk ke nomor WhatsApp yang terhubung, gateway akan mengirimkan HTTP POST request dengan payload JSON berisi pesan, nomor pengirim, nama pengirim, dan timestamp.
            </div>
        </div>

    </div>

    <!-- Need help? -->
    <div class="app-card p-6 bg-gradient-to-r from-blue-50 to-indigo-50/60 border-blue-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-base text-slate-900">Masih membutuhkan bantuan teknis?</h3>
            <p class="text-xs text-slate-600 font-medium mt-0.5">Tim teknis kami siap mendampingi proses integrasi API Anda.</p>
        </div>
        <a href="{{ route('pages.support') }}" class="app-btn app-btn-primary text-xs py-2.5 px-4 flex items-center gap-2 whitespace-nowrap">
            <i data-lucide="help-circle" class="w-4 h-4"></i>
            <span>Buka Tiket Bantuan</span>
        </a>
    </div>

</div>
@endsection
