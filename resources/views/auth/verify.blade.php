@extends('layouts.auth')

@section('title', 'Verifikasi Keamanan')

@section('content')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<style>
    /* Entrance animation */
    .verify-card { animation: verifyRise 0.5s cubic-bezier(0.22, 1, 0.36, 1) both; }
    @keyframes verifyRise {
        from { opacity: 0; transform: translateY(14px) scale(0.985); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .verify-stagger-1 { animation: verifyRise 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.08s both; }
    .verify-stagger-2 { animation: verifyRise 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.16s both; }
    .verify-stagger-3 { animation: verifyRise 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.24s both; }

    /* Shield ring pulse — subtle */
    .shield-ring::before,
    .shield-ring::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        border: 1px solid rgb(37 99 235 / 0.35);
        animation: shieldPulse 2.6s ease-out infinite;
        pointer-events: none;
    }
    .shield-ring::after { animation-delay: 1.3s; }
    @keyframes shieldPulse {
        0%   { transform: scale(1); opacity: 0.7; }
        70%  { transform: scale(1.35); opacity: 0; }
        100% { transform: scale(1.35); opacity: 0; }
    }

    /* Slow float — very subtle */
    .shield-float { animation: shieldFloat 4s ease-in-out infinite; }
    @keyframes shieldFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-3px); }
    }

    /* Fine grid texture */
    .verify-grid {
        background-image: radial-gradient(rgb(37 99 235 / 0.10) 1px, transparent 1px);
        background-size: 18px 18px;
        mask-image: radial-gradient(ellipse 80% 70% at 50% 0%, black 30%, transparent 75%);
        -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 0%, black 30%, transparent 75%);
    }
    .dark .verify-grid {
        background-image: radial-gradient(rgb(96 165 250 / 0.14) 1px, transparent 1px);
    }
</style>

<div class="space-y-6">

    <!-- Main Verification Card -->
    <div class="verify-card relative overflow-hidden rounded-2xl border border-slate-200/90 dark:border-slate-700/60 bg-white dark:bg-slate-900/60 shadow-xl shadow-slate-200/50 dark:shadow-black/30">

        <!-- Top accent line -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-blue-500/60 to-transparent"></div>

        <!-- Fine grid texture -->
        <div class="verify-grid absolute inset-0 pointer-events-none"></div>

        <div class="relative px-6 sm:px-8 pt-8 pb-7">

            <!-- Shield Icon -->
            <div class="verify-stagger-1 flex justify-center">
                <div class="shield-ring shield-float relative w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <svg class="w-8 h-8 text-white drop-shadow-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="verify-stagger-2 text-center mt-5">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    {{ $target === 'register' ? 'Step 1 of 2 · Pendaftaran' : 'Step 1 of 2 · Login' }}
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-navy dark:text-white mt-3">
                    Verifikasi Keamanan
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1.5 max-w-[17rem] mx-auto leading-relaxed">
                    Konfirmasi bahwa Anda manusia untuk melanjutkan ke halaman {{ $target === 'register' ? 'pendaftaran' : 'login' }}.
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('auth.verify.submit') }}" class="verify-stagger-3 space-y-4 mt-6" x-data="{ loading: false }">
                @csrf
                <input type="hidden" name="redirect" value="{{ $target }}">

                <!-- Cloudflare Turnstile -->
                <div class="flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="auto" data-language="id"></div>
                </div>
                @error('cf-turnstile-response')
                    <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 text-center">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <button type="submit" :disabled="loading" @click="loading = true"
                        class="btn-xl btn-primary w-full cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!loading">Lanjutkan ke {{ $target === 'register' ? 'Daftar' : 'Login' }} &rarr;</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memverifikasi...
                    </span>
                </button>
            </form>
        </div>

        <!-- Card Footer: Security badges -->
        <div class="verify-stagger-3 relative border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/70 dark:bg-slate-900/40 px-6 sm:px-8 py-3.5 flex items-center justify-center gap-4 text-[10px] font-semibold text-slate-400 dark:text-slate-500">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500" viewBox="0 0 24 24" fill="currentColor"><path d="M6.34 0 4.59 1.76 2.83 0 .72 2.11l1.76 1.76L.72 5.64l1.76 1.76L4.24 5.64 6 7.4l2.11-2.11-1.76-1.76 1.76-1.76zM23.28 18.36l-1.76-1.76 1.76-1.76-2.11-2.11-1.76 1.76-1.76-1.76-2.11 2.11 1.76 1.76-1.76 1.76 2.11 2.11 1.76-1.76 1.76 1.76zM23.28 2.11 21.17 0l-1.76 1.76L17.65 0l-2.11 2.11 1.76 1.76-1.76 1.76 2.11 2.11 1.76-1.76 1.76 1.76 2.11-2.11-1.76-1.76zM6.34 16.6 4.59 18.36 2.83 16.6.72 18.71l1.76 1.76-1.76 1.76L2.83 24l1.76-1.76L6.34 24l2.11-2.11-1.76-1.76 1.76-1.76z"/></svg>
                Protected by Cloudflare
            </span>
            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full"></span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                TLS 256-bit Encrypted
            </span>
        </div>
    </div>

    <!-- Back Link -->
    <div class="text-center">
        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors inline-flex items-center gap-1.5 group">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</div>
@endsection
