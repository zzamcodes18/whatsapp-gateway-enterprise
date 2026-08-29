@extends('layouts.landing')

@section('title', 'Syarat & Ketentuan Layanan - Whatsapp Gateway Enterprise')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-8">

    <div class="border-b border-slate-200/80 pb-4 space-y-2">
        <span class="app-tag app-tag-blue text-[9px]">LEGAL & COMPLIANCE</span>
        <h1 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Syarat dan Ketentuan Layanan</h1>
        <p class="text-xs text-slate-500 font-medium">Terakhir diperbarui: {{ date('d F Y') }}</p>
    </div>

    <div class="app-card p-6 sm:p-8 bg-white space-y-6 text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">1. Ketentuan Umum</h2>
            <p>
                Dengan mengakses dan menggunakan platform {{ $siteName }}, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat oleh seluruh Syarat dan Ketentuan yang berlaku.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">2. Penggunaan Layanan dan API</h2>
            <p>
                Layanan ini disediakan untuk tujuan integrasi pengiriman notifikasi, verifikasi OTP, pesan transaksional, dan otomasi customer service. Pengguna dilarang keras menggunakan gateway untuk:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-800">
                <li>Mengirim pesan spam massal tanpa izin, phishing, perjudian, atau konten ilegal.</li>
                <li>Menyalahgunakan alokasi kuota pesan harian atau mencoba memanipulasi rate limiting API.</li>
                <li>Menyebarkan materi berbahaya yang melanggar hukum Republik Indonesia dan kebijakan WhatsApp.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">3. Kuota Limit dan Jadwal Reset</h2>
            <p>
                Setiap akun diberikan batas maksimum perangkat yang terhubung (*device limit*) dan kuota pengiriman pesan harian (*daily message limit*). Kuota pesan harian yang terpakai akan direset secara otomatis oleh sistem setiap hari pada pukul <strong>00:05 WIB</strong>.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">4. Tanggung Jawab Keamanan Akun</h2>
            <p>
                Pengguna bertanggung jawab penuh atas kerahasiaan API Key dan kredensial akun masing-masing. Whatsapp Gateway Enterprise tidak bertanggung jawab atas penyalahgunaan akibat kelalaian dalam mengamankan kunci akses API.
            </p>
        </section>
    </div>

</div>
@endsection
