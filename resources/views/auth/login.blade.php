@extends('layouts.auth')

@section('title', 'Sign In · Whatsapp Gateway Enterprise')

@section('content')
<div class="space-y-6">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy">
            Sign in to Your Account
        </h1>
        <p class="text-xs text-slate-500 font-medium mt-1.5">
            Kelola sesi perangkat dan integrasi REST API WhatsApp Anda.
        </p>
    </div>

    <!-- Quick Demo helper -->
    <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl text-xs space-y-1.5">
        <div class="font-bold text-blue-900 flex items-center gap-1.5 text-[11px]">
            <i data-lucide="key" class="w-3.5 h-3.5 text-blue-600"></i>
            <span>Akun Demo Cepat:</span>
        </div>
        <div class="font-mono text-[11px] text-slate-600 flex flex-wrap gap-2">
            <span class="cursor-pointer hover:underline font-semibold text-blue-700 bg-white px-2.5 py-1 rounded-lg border border-blue-200 shadow-2xs" onclick="document.getElementById('email').value='demo@lapakotp.com'; document.getElementById('password').value='password123'">demo@lapakotp.com</span>
            <span class="cursor-pointer hover:underline font-semibold text-indigo-700 bg-white px-2.5 py-1 rounded-lg border border-indigo-200 shadow-2xs" onclick="document.getElementById('email').value='admin@lapakotp.com'; document.getElementById('password').value='password123'">admin@lapakotp.com</span>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
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

        <!-- Password -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Forgotten password?
                </a>
            </div>
            <div class="relative">
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="••••••••" 
                    required 
                    class="input-text p-2.5 sm:p-3 text-xs pl-9"
                >
                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3 top-3.5"></i>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2 pt-0.5">
            <input 
                type="checkbox" 
                name="remember" 
                id="remember" 
                class="w-4 h-4 border-slate-300 rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
            >
            <label for="remember" class="text-xs font-medium text-slate-600 cursor-pointer select-none">
                Ingat sesi login saya
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="btn-xl btn-primary w-full cursor-pointer flex items-center justify-center gap-2">
                <span>Sign In to Dashboard</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Register Link -->
        <div class="pt-2 text-center">
            <a href="{{ route('register') }}" class="btn btn-link w-full text-xs font-semibold text-slate-600 hover:text-blue-600">
                Need an Account? <span class="text-blue-600 font-bold ml-1">Sign up for free &rarr;</span>
            </a>
        </div>
    </form>

</div>
@endsection
