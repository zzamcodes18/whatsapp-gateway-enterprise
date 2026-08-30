@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
@php
    $enableRegisterOtp = \App\Models\SystemSetting::get('enable_register_otp', 'true') === 'true';
    $botDeviceId = \App\Models\SystemSetting::get('otp_server_device_id');
    $botDevice = $botDeviceId ? \App\Models\Device::find($botDeviceId) : null;
    $isBotActive = $enableRegisterOtp && $botDevice && $botDevice->isConnected();
    $enableGoogle = \App\Models\SystemSetting::get('enable_google_login', 'false') === 'true';
    $enableGithub = \App\Models\SystemSetting::get('enable_github_login', 'false') === 'true';
@endphp
<div class="space-y-6">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy dark:text-white">
            Create Your Account
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1.5">
            Mulai integrasi WhatsApp Multi-Device & REST API gratis dalam hitungan menit.
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div class="space-y-1.5">
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Full Name</label>
            <div class="relative flex items-center">
                <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}" 
                    placeholder="John Doe" 
                    required 
                    autofocus
                    class="input-text py-2.5 sm:py-3 text-xs pl-10 pr-3.5 font-medium"
                >
            </div>
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Email Address</label>
            <div class="relative flex items-center">
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    placeholder="nama@perusahaan.com" 
                    required 
                    class="input-text py-2.5 sm:py-3 text-xs pl-10 pr-3.5 font-medium"
                >
            </div>
        </div>

        <!-- Phone Number -->
        <div class="space-y-1.5">
            <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                WhatsApp Phone <span class="text-rose-500">*</span>
            </label>
            <div class="relative flex items-center">
                <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="text" 
                    name="phone_number" 
                    id="phone_number" 
                    value="{{ old('phone_number') }}" 
                    placeholder="08123456789 atau 628123456789" 
                    required
                    class="input-text py-2.5 sm:py-3 text-xs font-mono pl-10 pr-3.5"
                >
            </div>
            @if($isBotActive)
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Kode OTP verifikasi akan dikirimkan otomatis ke WhatsApp ini.</p>
            @endif
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Password (Min. 8 Karakter)</label>
            <div class="relative flex items-center">
                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="••••••••" 
                    required 
                    class="input-text py-2.5 sm:py-3 text-xs pl-10 pr-3.5"
                >
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Confirm Password</label>
            <div class="relative flex items-center">
                <i data-lucide="shield-check" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    placeholder="••••••••" 
                    required 
                    class="input-text py-2.5 sm:py-3 text-xs pl-10 pr-3.5"
                >
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-xl btn-primary w-full cursor-pointer flex items-center justify-center gap-2">
                <span>Create Account &rarr;</span>
            </button>
        </div>

        @if($enableGoogle || $enableGithub)
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white dark:bg-[#0B1120] px-2 text-slate-500 dark:text-slate-400 font-medium transition-colors">atau daftar dengan</span>
                </div>
            </div>

            <div class="grid grid-cols-{{ ($enableGoogle && $enableGithub) ? '2' : '1' }} gap-3">
                @if($enableGoogle)
                    <a href="{{ route('auth.google', ['action' => 'register']) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-2xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                            <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3S.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15s.7 5.3 1.9 7.7l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16c1.8 3.7 5.6 7 10.1 7z"/>
                        </svg>
                        <span>Google</span>
                    </a>
                @endif

                @if($enableGithub)
                    <a href="{{ route('auth.github', ['action' => 'register']) }}" class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-800 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors shadow-2xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                        </svg>
                        <span>GitHub</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- Login Link -->
        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="btn btn-link w-full text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600">
                Already have an account? <span class="text-blue-600 dark:text-blue-400 font-bold ml-1">Sign in here &rarr;</span>
            </a>
        </div>
    </form>

</div>
@endsection
