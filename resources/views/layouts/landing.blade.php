<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta SEO Tags -->
    <title>@yield('title', ($siteName ?? 'Whatsapp Gateway Enterprise') . ' & Multi-Device REST API')</title>
    <meta name="title" content="@yield('meta_title', ($siteName ?? 'Whatsapp Gateway Enterprise') . ' & Multi-Device REST API')">
    <meta name="description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Baileys v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
    <meta name="keywords" content="{{ $siteKeywords ?? 'whatsapp gateway, wa gateway api, whatsapp otp, baileys v1, whatsapp bot api, rest api whatsapp, whatsapp multi device, webhook whatsapp, whatsapp gateway enterprise' }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2563EB">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', ($siteName ?? 'Whatsapp Gateway Enterprise') . ' & Multi-Device REST API')">
    <meta property="og:description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Baileys v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
    <meta property="og:image" content="{{ asset('og-image.svg') }}">
    <meta property="og:image:type" content="image/svg+xml">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', ($siteName ?? 'Whatsapp Gateway Enterprise') . ' & Multi-Device REST API')">
    <meta name="twitter:description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Baileys v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
    <meta name="twitter:image" content="{{ asset('og-image.svg') }}">
    <meta name="twitter:site" content="{{ $siteName }}">

    <!-- Favicon -->
    @if(!empty($siteFavicon))
        <link rel="icon" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="soft-canvas text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileNav: false }">

    <!-- Top Status Bar -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-900 text-white py-2 px-4 text-center font-mono text-xs tracking-tight flex items-center justify-center gap-2 border-b border-blue-950">
        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
        <span class="text-slate-300">WhatsApp Multi-Device Engine <strong class="text-white">v1.0.0</strong></span>
        <span class="hidden sm:inline text-slate-500">•</span>
        <span class="hidden sm:inline text-slate-300">Scan QR Code & Pairing Code 8-Digit Ready</span>
    </div>

    <!-- Responsive Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-[170px] object-contain group-hover:scale-105 transition-transform">
                @else
                    <div class="w-9 h-9 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-lg tracking-tight text-slate-900">{{ $siteName }}</span>
                    </div>
                @endif
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-600">
                <a href="{{ route('home') }}#fitur" class="hover:text-blue-600 transition-colors">Fitur Platform</a>
                <a href="{{ route('home') }}#koneksi" class="hover:text-blue-600 transition-colors">Metode Koneksi</a>
                <a href="{{ route('home') }}#playground" class="hover:text-blue-600 transition-colors">API Docs</a>
                <a href="{{ route('pages.faq') }}" class="hover:text-blue-600 transition-colors">FAQ</a>
                <a href="{{ route('pages.support') }}" class="hover:text-blue-600 transition-colors">Bantuan</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-2.5">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="app-btn app-btn-soft-blue text-xs py-1.5 px-3 flex items-center gap-1.5">
                            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                            <span>Admin Panel</span>
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="app-btn app-btn-primary text-xs py-1.5 px-3.5 flex items-center gap-1.5">
                        <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                        <span>Buka Console</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="app-btn app-btn-secondary text-xs py-1.5 px-3.5 hidden sm:inline-flex">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="app-btn app-btn-primary text-xs py-1.5 px-4 flex items-center gap-1.5">
                        <span>Mulai Sekarang</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="mobileNav = !mobileNav" type="button" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100" aria-label="Toggle Navigation">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>

        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" class="md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-md px-5 py-4 space-y-2.5" style="display: none;" x-cloak @click.away="mobileNav = false">
            <a href="{{ route('home') }}#fitur" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">Fitur Platform</a>
            <a href="{{ route('home') }}#koneksi" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">Metode Koneksi</a>
            <a href="{{ route('home') }}#playground" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">API Documentation</a>
            <a href="{{ route('pages.faq') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">FAQ</a>
            <a href="{{ route('pages.support') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">Pusat Bantuan</a>
            <a href="{{ route('pages.terms') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">Ketentuan Layanan</a>
            <a href="{{ route('pages.privacy') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 hover:text-blue-600">Kebijakan Privasi</a>
            @guest
                <div class="pt-3 border-t border-slate-100 flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 app-btn app-btn-secondary text-xs py-2 text-center">Masuk</a>
                    <a href="{{ route('register') }}" class="flex-1 app-btn app-btn-primary text-xs py-2 text-center">Daftar</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- High-end Modern Footer -->
    <footer class="bg-white border-t border-slate-200 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                
                <!-- Brand & Description -->
                <div class="col-span-2 space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto max-w-[150px] object-contain">
                        @else
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <span class="font-extrabold text-base tracking-tight text-slate-900">{{ strtoupper($siteName) }}</span>
                        @endif
                    </div>
                    <p class="text-xs font-medium text-slate-600 max-w-sm leading-relaxed">
                        {{ $siteDescription }}
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <span class="app-tag app-tag-emerald text-[10px]">Uptime 99.9%</span>
                        <span class="app-tag app-tag-blue text-[10px]">Baileys v1.0 Core</span>
                    </div>
                </div>

                <!-- Produk -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 mb-3.5">Produk</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600">
                        <li><a href="{{ route('home') }}#fitur" class="hover:text-blue-600 transition-colors">Fitur Gateway</a></li>
                        <li><a href="{{ route('home') }}#koneksi" class="hover:text-blue-600 transition-colors">Dual Connection</a></li>
                        <li><a href="{{ route('home') }}#playground" class="hover:text-blue-600 transition-colors">REST API v1</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Console Dashboard</a></li>
                    </ul>
                </div>

                <!-- Bantuan -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 mb-3.5">Dukungan</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600">
                        <li><a href="{{ route('pages.faq') }}" class="hover:text-blue-600 transition-colors">Tanya Jawab (FAQ)</a></li>
                        <li><a href="{{ route('pages.support') }}" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-blue-600 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Infrastruktur -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 mb-3.5">Infrastruktur</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600 font-mono text-[11px]">
                        <li><span>Laravel 11 PHP 8.3</span></li>
                        <li><span>Node.js Baileys v1.0</span></li>
                        <li><span>Multi-Device Sockets</span></li>
                        <li><span>Webhook Dispatcher</span></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-100 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-medium text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. Hak cipta dilindungi.</p>
                <div class="flex items-center gap-3 font-mono text-[11px] text-slate-400">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> SYSTEM ONLINE</span>
                    <span>•</span>
                    <span>WAKTU SERVER: {{ date('H:i') }} WIB</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
