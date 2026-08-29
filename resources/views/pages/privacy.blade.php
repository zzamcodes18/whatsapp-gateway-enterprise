@extends('layouts.landing')

@section('title', 'Kebijakan Privasi - Whatsapp Gateway Enterprise')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-8">

    <div class="border-b border-slate-200/80 pb-4 space-y-2">
        <span class="app-tag app-tag-blue text-[9px]">DATA PROTECTION</span>
        <h1 class="font-extrabold text-2xl sm:text-4xl text-slate-900">Kebijakan Privasi</h1>
        <p class="text-xs text-slate-500 font-medium">Terakhir diperbarui: {{ date('d F Y') }}</p>
    </div>

    <div class="app-card p-6 sm:p-8 bg-white space-y-6 text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">1. Pengumpulan Informasi</h2>
            <p>
                Kami mengumpulkan data yang diperlukan untuk mengoperasikan platform, termasuk nama, alamat email, nomor telepon terdaftar, serta catatan teknis transmisi pesan (timestamp, status delivery).
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">2. Penyimpanan Sesi & Kredensial</h2>
            <p>
                Data autentikasi sesi WhatsApp (Baileys auth credentials) disimpan dalam direktori penyimpanan server yang terisolasi per sesi menggunakan UUIDv4. Token API Key di-hash menggunakan algoritma standar industri SHA-256 dan tidak disimpan dalam teks polos.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">3. Kerahasiaan Kontak Pelanggan</h2>
            <p>
                Kami tidak memperjualbelikan, menyewakan, atau memberikan data nomor kontak pelanggan Anda kepada pihak ketiga manapun untuk kepentingan periklanan atau komersial di luar penyediaan layanan gateway.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="font-bold text-base sm:text-lg text-slate-900">4. Hak Pengguna</h2>
            <p>
                Anda berhak untuk memutuskan koneksi perangkat, mencabut (*revoke*) API Key, atau menghapus riwayat sesi kapan saja melalui console dashboard.
            </p>
        </section>
    </div>

</div>
@endsection
