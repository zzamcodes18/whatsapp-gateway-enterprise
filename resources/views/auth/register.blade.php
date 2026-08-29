@extends('layouts.auth')

@section('title', 'Sign Up · Whatsapp Gateway Enterprise')

@section('content')
<div class="space-y-6">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy">
            Create Your Account
        </h1>
        <p class="text-xs text-slate-500 font-medium mt-1.5">
            Mulai integrasi WhatsApp Multi-Device & REST API gratis dalam hitungan menit.
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div class="space-y-1.5">
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Full Name</label>
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
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
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
            <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700">WhatsApp Phone (Opsional)</label>
            <div class="relative flex items-center">
                <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none z-10"></i>
                <input 
                    type="text" 
                    name="phone_number" 
                    id="phone_number" 
                    value="{{ old('phone_number') }}" 
                    placeholder="08123456789 atau 628123456789" 
                    class="input-text py-2.5 sm:py-3 text-xs font-mono pl-10 pr-3.5"
                >
            </div>
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password (Min. 8 Karakter)</label>
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
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Confirm Password</label>
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

        <!-- Login Link -->
        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="btn btn-link w-full text-xs font-semibold text-slate-600 hover:text-blue-600">
                Already have an account? <span class="text-blue-600 font-bold ml-1">Sign in here &rarr;</span>
            </a>
        </div>
    </form>

</div>
@endsection
