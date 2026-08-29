@extends('layouts.auth')

@section('title', 'Reset Password · LAPAKOTP WhatsApp Gateway')

@section('content')
<div class="space-y-6">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy">
            Reset Your Password
        </h1>
        <p class="text-xs text-slate-500 font-medium mt-1.5">
            Masukkan email terdaftar untuk menerima tautan pemulihan kata sandi.
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
            <div class="relative">
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    placeholder="nama@perusahaan.com" 
                    required 
                    autofocus
                    class="input-text p-2.5 sm:p-3 text-xs pl-9 font-medium"
                >
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-3.5"></i>
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
            <a href="{{ route('login') }}" class="btn btn-link w-full text-xs font-semibold text-slate-600 hover:text-blue-600">
                &larr; Back to sign in
            </a>
        </div>
    </form>

</div>
@endsection
