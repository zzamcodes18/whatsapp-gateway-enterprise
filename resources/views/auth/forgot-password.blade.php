@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
@include('auth.partials.recaptcha', ['action' => 'forgot_password'])
<div class="space-y-6">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy dark:text-white">
            Reset Your Password
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1.5">
            Masukkan email terdaftar untuk menerima tautan pemulihan kata sandi.
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="recaptcha_token" class="recaptcha-token">

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
                    autofocus
                    class="input-text py-2.5 sm:py-3 text-xs pl-10 pr-3.5 font-medium"
                >
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-xl btn-primary w-full cursor-pointer flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Send Password Reset Link</span>
            </button>
        </div>

        <!-- Back to Login -->
        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="btn btn-link w-full text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600">
                &larr; Back to sign in
            </a>
        </div>
    </form>

</div>
@endsection
