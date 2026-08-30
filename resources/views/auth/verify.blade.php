@extends('layouts.auth')

@section('title', 'Verifikasi Keamanan')

@section('content')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
    // Auto-submit saat captcha lolos (ala Duitku)
    function onTurnstileSuccess(token) {
        document.getElementById('verify-form').submit();
    }
</script>

<div class="w-full flex flex-col items-center justify-center" style="min-height: 60vh;">

    <!-- Loading state saat verifikasi -->
    <div id="verify-loading" class="hidden flex-col items-center gap-3 mb-6">
        <svg class="animate-spin w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Verifikasi berhasil, mengalihkan...</p>
    </div>

    <!-- Widget Card -->
    <div id="verify-card" class="w-full max-w-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm p-6 sm:p-8 flex flex-col items-center gap-5">
        <div class="text-center">
            <h1 class="text-sm font-bold text-slate-800 dark:text-white">Verifikasi Keamanan</h1>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">
                Selesaikan verifikasi untuk melanjutkan ke halaman {{ $target === 'register' ? 'pendaftaran' : 'login' }}.
            </p>
        </div>

        <form id="verify-form" method="POST" action="{{ route('auth.verify.submit') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ $target }}">
            <div class="cf-turnstile"
                 data-sitekey="{{ $siteKey }}"
                 data-theme="auto"
                 data-language="id"
                 data-callback="onTurnstileSuccess">
            </div>
        </form>

        @error('cf-turnstile-response')
            <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 text-center">{{ $message }}</p>
        @enderror
    </div>

    <a href="{{ route('home') }}" class="mt-5 text-[11px] font-semibold text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
        &larr; Kembali ke Beranda
    </a>
</div>

<script>
    // Sembunyikan kartu & tampilkan loading saat form disubmit
    document.getElementById('verify-form').addEventListener('submit', function () {
        document.getElementById('verify-card').classList.add('hidden');
        const loading = document.getElementById('verify-loading');
        loading.classList.remove('hidden');
        loading.classList.add('flex');
    });
</script>
@endsection
