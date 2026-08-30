@extends('layouts.auth')

@section('title', 'Verifikasi Kode OTP')

@section('content')
@include('auth.partials.recaptcha', ['action' => 'verify_otp'])
<div class="space-y-6" x-data="otpForm({{ $expiresAt }})">
    
    <!-- Title & Info -->
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-full border border-emerald-200/80 dark:border-emerald-500/20 mb-3">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            <span>Verifikasi Akun</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy dark:text-white">
            Masukkan Kode OTP
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1.5 leading-relaxed">
            @if($currentMethod === 'email')
                Kode verifikasi 6 digit telah dikirimkan ke alamat Email:
            @else
                Kode verifikasi 6 digit telah dikirimkan melalui pesan WhatsApp ke nomor:
            @endif
        </p>
        <div class="mt-2 inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
            @if($currentMethod === 'email')
                <i data-lucide="mail" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                <span>{{ $email }}</span>
            @else
                <i data-lucide="smartphone" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span>+{{ $phone }}</span>
            @endif
            <a href="{{ route('register') }}" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline font-sans font-semibold ml-1">Ubah</a>
        </div>
    </div>

    <!-- Flash Error Alert -->
    @if($errors->any())
        <div class="p-3.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-900 dark:text-rose-300 rounded-xl text-xs font-medium space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- OTP Verification Form -->
    <form method="POST" action="{{ route('register.verify-otp') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="recaptcha_token" class="recaptcha-token">

        <!-- Hidden full OTP field -->
        <input type="hidden" name="otp" :value="otpCode">

        <!-- 6 Digit Input Group -->
        <div class="space-y-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">6 Digit Kode OTP</label>
            <div class="grid grid-cols-6 gap-2 sm:gap-3" @paste="handlePaste($event)">
                <template x-for="(digit, index) in digits" :key="index">
                    <input 
                        type="text" 
                        maxlength="1" 
                        pattern="[0-9]*" 
                        inputmode="numeric"
                        x-model="digits[index]"
                        @input="handleInput(index)"
                        @keydown.backspace="handleBackspace(index, $event)"
                        :id="'otp-input-' + index"
                        class="w-full h-12 sm:h-14 text-center text-lg sm:text-xl font-extrabold font-mono bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 dark:text-white rounded-xl focus:border-blue-600 focus:bg-white dark:focus:bg-[#0D1526] focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                        :autofocus="index === 0"
                    >
                </template>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button 
                type="submit" 
                :disabled="otpCode.length !== 6"
                class="btn-xl btn-primary w-full cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Verifikasi Kode & Buat Akun &rarr;</span>
            </button>
        </div>
    </form>

    <!-- Resend OTP Options Section -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium text-center">Tidak menerima kode OTP?</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Resend via WhatsApp -->
            <form method="POST" action="{{ route('register.resend-otp') }}">
                @csrf
                <input type="hidden" name="channel" value="whatsapp">
                <button 
                    type="submit" 
                    :disabled="timer > 0 || !{{ $hasWa ? 'true' : 'false' }}"
                    class="w-full btn border border-emerald-200 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                >
                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                    <span>Kirim ke WhatsApp</span>
                </button>
            </form>

            <!-- Resend via Email -->
            <form method="POST" action="{{ route('register.resend-otp') }}">
                @csrf
                <input type="hidden" name="channel" value="email">
                <button 
                    type="submit" 
                    :disabled="timer > 0 || !{{ $hasSmtp ? 'true' : 'false' }}"
                    class="w-full btn border border-blue-200 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                >
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    <span>Kirim ke Email</span>
                </button>
            </form>
        </div>

        <div class="text-center pt-1" x-show="timer > 0">
            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                Tunggu <span x-text="timer" class="font-mono text-blue-600 dark:text-blue-400 font-bold"></span> detik sebelum minta kirim ulang
            </span>
        </div>
    </div>

    <!-- Back to Register -->
    <div class="text-center pt-2">
        <a href="{{ route('register') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors inline-flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Batal & Kembali ke Pendaftaran</span>
        </a>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('otpForm', (expiresAt) => ({
        digits: ['', '', '', '', '', ''],
        timer: 60,

        get otpCode() {
            return this.digits.join('');
        },

        init() {
            this.startTimer();
            this.$nextTick(() => {
                const el = document.getElementById('otp-input-0');
                if (el) el.focus();
            });
        },

        startTimer() {
            const interval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    clearInterval(interval);
                }
            }, 1000);
        },

        handleInput(index) {
            // Strip non-digit
            this.digits[index] = this.digits[index].replace(/[^0-9]/g, '');

            if (this.digits[index] && index < 5) {
                const nextInput = document.getElementById('otp-input-' + (index + 1));
                if (nextInput) nextInput.focus();
            }
        },

        handleBackspace(index, event) {
            if (!this.digits[index] && index > 0) {
                const prevInput = document.getElementById('otp-input-' + (index - 1));
                if (prevInput) {
                    prevInput.focus();
                    this.digits[index - 1] = '';
                }
            }
        },

        handlePaste(event) {
            event.preventDefault();
            const pasteData = (event.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').trim();
            if (pasteData) {
                const chars = pasteData.split('').slice(0, 6);
                chars.forEach((char, idx) => {
                    this.digits[idx] = char;
                });
                const nextIndex = Math.min(chars.length, 5);
                const nextInput = document.getElementById('otp-input-' + nextIndex);
                if (nextInput) nextInput.focus();
            }
        }
    }));
});
</script>
@endsection
