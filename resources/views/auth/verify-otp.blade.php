@extends('layouts.auth')

@section('title', 'Verifikasi OTP WhatsApp')

@section('content')
<div class="space-y-6" x-data="otpForm({{ $expiresAt }})">
    
    <!-- Title & Info -->
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200/80 mb-3">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            <span>Verifikasi WhatsApp OTP</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy">
            Masukkan Kode OTP
        </h1>
        <p class="text-xs text-slate-500 font-medium mt-1.5 leading-relaxed">
            Kode verifikasi 6 digit telah dikirimkan melalui pesan WhatsApp ke nomor:
        </p>
        <div class="mt-2 inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200 font-mono text-xs font-bold text-slate-800">
            <i data-lucide="smartphone" class="w-4 h-4 text-emerald-600"></i>
            <span>+{{ $phone }}</span>
            <a href="{{ route('register') }}" class="text-[11px] text-blue-600 hover:underline font-sans font-semibold ml-1">Ubah</a>
        </div>
    </div>

    <!-- Flash Error Alert -->
    @if($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-medium space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- OTP Verification Form -->
    <form method="POST" action="{{ route('register.verify-otp') }}" class="space-y-5">
        @csrf

        <!-- Hidden full OTP field -->
        <input type="hidden" name="otp" :value="otpCode">

        <!-- 6 Digit Input Group -->
        <div class="space-y-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">6 Digit Kode OTP</label>
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
                        class="w-full h-12 sm:h-14 text-center text-lg sm:text-xl font-extrabold font-mono bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100 outline-none transition-all"
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

    <!-- Resend OTP Section -->
    <div class="pt-2 border-t border-slate-100 flex flex-col items-center gap-2">
        <p class="text-xs text-slate-500 font-medium">Tidak menerima kode di WhatsApp?</p>
        
        <form method="POST" action="{{ route('register.resend-otp') }}">
            @csrf
            <button 
                type="submit" 
                :disabled="timer > 0"
                class="btn btn-secondary text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                <span x-show="timer > 0">Kirim Ulang Kode (<span x-text="timer"></span>s)</span>
                <span x-show="timer <= 0">Kirim Ulang Kode OTP</span>
            </button>
        </form>
    </div>

    <!-- Back to Register -->
    <div class="text-center">
        <a href="{{ route('register') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1">
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
