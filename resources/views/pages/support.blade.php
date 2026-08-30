@extends('layouts.landing')

@section('title', 'Pusat Bantuan & Support')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-10">

    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-4 space-y-2">
        <span class="app-tag app-tag-blue text-[9px]">CUSTOMER SUPPORT</span>
        <h1 class="font-extrabold text-2xl sm:text-4xl text-slate-900 dark:text-white">Pusat Bantuan & Dukungan Teknis</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">Kirimkan tiket bantuan atau pertanyaan teknis seputar konfigurasi API gateway.</p>
    </div>

    <!-- Alert Message -->
    @if(session('success'))
        <div class="app-card p-4 bg-emerald-50/80 border-emerald-200 text-emerald-900 font-semibold flex items-center gap-2.5 text-xs sm:text-sm">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        
        <!-- Left: Form Tiket (7 cols) -->
        <div class="md:col-span-7 space-y-4">
            <div class="app-card p-6 sm:p-7 bg-white dark:bg-[#111A2E] space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h2 class="font-bold text-base text-slate-900 dark:text-white">Kirim Tiket Bantuan</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tim teknis kami akan merespon pertanyaan Anda melalui email.</p>
                </div>

                <form method="POST" action="{{ route('pages.support.submit') }}" class="space-y-3.5">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="name" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Nama Anda" class="app-input text-xs">
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Alamat Email</label>
                        <input type="email" name="email" id="email" required value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="nama@domain.com" class="app-input text-xs">
                    </div>

                    <div class="space-y-1.5">
                        <label for="subject" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Topik / Subjek</label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}" placeholder="Contoh: Kendala Pairing Code / Request Upgrade Kuota" class="app-input text-xs">
                    </div>

                    <div class="space-y-1.5">
                        <label for="message" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Detail Pertanyaan / Masalah</label>
                        <textarea name="message" id="message" rows="4" required placeholder="Jelaskan kendala teknis atau pertanyaan Anda secara rinci..." class="app-input text-xs font-medium">{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full app-btn app-btn-primary py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirim Tiket Bantuan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Contact & SLA (5 cols) -->
        <div class="md:col-span-5 space-y-4">
            
            <div class="app-card p-5 bg-gradient-to-br from-blue-50/70 to-slate-50 dark:from-blue-500/10 dark:to-slate-800/40 border-blue-100 dark:border-blue-500/20 space-y-3">
                <div class="flex items-center gap-2 font-bold text-sm text-slate-900 dark:text-white">
                    <i data-lucide="clock" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    <span>Jam Layanan Support</span>
                </div>
                <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                    <div>Senin - Jumat: 08:00 - 22:00 WIB</div>
                    <div>Sabtu - Minggu: 09:00 - 18:00 WIB</div>
                    <div class="pt-1 text-[11px] text-slate-500 dark:text-slate-400 font-mono">Estimasi respon: &lt; 2 jam kerja</div>
                </div>
            </div>

            <div class="app-card p-5 bg-white dark:bg-[#111A2E] space-y-3">
                <div class="flex items-center gap-2 font-bold text-sm text-slate-900 dark:text-white">
                    <i data-lucide="mail" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    <span>Dukungan Kontak</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                    Untuk kebutuhan enterprise atau kendala darurat, Anda dapat menghubungi kami melalui:
                </p>
                <div class="font-mono text-xs font-bold text-blue-700 dark:text-blue-300 p-2.5 bg-blue-50/70 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl space-y-1.5">
                    <div>Email: {{ !empty($supportEmail) ? $supportEmail : 'support@zzam.dev' }}</div>
                    @if(!empty($supportWhatsapp))
                        <div>
                            WhatsApp CS: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" class="underline hover:text-blue-800 dark:hover:text-blue-200">+{{ $supportWhatsapp }}</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
