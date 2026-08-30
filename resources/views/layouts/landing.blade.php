<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta SEO Tags -->
    <title>@yield('title', 'Multi-Device REST API') · {{ $siteName }}</title>
    <meta name="title" content="@yield('meta_title', $siteName . ' & Multi-Device REST API')">
    <meta name="description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Enterprise Core v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
    <meta name="keywords" content="{{ $siteKeywords ?? 'whatsapp gateway, wa gateway api, whatsapp otp, whatsapp bot api, rest api whatsapp, whatsapp multi device, webhook whatsapp, whatsapp gateway enterprise' }}">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2563EB">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', ($siteName ?? 'Whatsapp Gateway Enterprise') . ' & Multi-Device REST API')">
    <meta property="og:description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Enterprise Core v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
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
    <meta name="twitter:description" content="@yield('meta_description', $siteDescription ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Enterprise Core v1.0. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.')">
    <meta name="twitter:image" content="{{ asset('og-image.svg') }}">
    <meta name="twitter:site" content="{{ $siteName }}">

    <!-- Favicon -->
    @if(!empty($siteFavicon))
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
        <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
        <link rel="icon" type="image/png" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Theme Initializer (Prevent Flash of Unstyled Theme) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="soft-canvas text-slate-900 dark:text-slate-100 dark:bg-slate-950 font-sans antialiased selection:bg-blue-600 selection:text-white" 
      x-data="{ 
          mobileNav: false, 
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }">

    <!-- Top Status Bar -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-900 text-white py-2 px-4 text-center font-mono text-xs tracking-tight flex items-center justify-center gap-2 border-b border-blue-950">
        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
        <span class="text-slate-300">WhatsApp Multi-Device Engine <strong class="text-white">v1.0.0</strong></span>
        <span class="hidden sm:inline text-slate-500">•</span>
        <span class="hidden sm:inline text-slate-300">Scan QR Code & Pairing Code 8-Digit Ready</span>
    </div>

    <!-- Responsive Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 border-b border-slate-200/80 dark:border-slate-800 shadow-xs transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                @if(!empty($siteLogo))
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 shadow-xs p-1 flex items-center justify-center flex-shrink-0 group-hover:scale-105 group-hover:border-blue-300 transition-all">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="max-h-full max-w-full object-contain rounded-lg">
                    </div>
                @else
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-500 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </div>
                @endif
                <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $siteName }}</span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <a href="{{ route('home') }}#fitur" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Fitur Platform</a>
                <a href="{{ route('home') }}#koneksi" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Metode Koneksi</a>
                <a href="{{ route('home') }}#playground" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">API Docs</a>
                <a href="{{ route('pages.faq') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">FAQ</a>
                <a href="{{ route('pages.support') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Bantuan</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Light / Dark Theme Switcher (Animated Pill Toggle) -->
                <button @click="toggleTheme()" 
                        type="button" 
                        class="relative w-14 h-7 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-300/70 dark:border-slate-600 transition-colors duration-300 cursor-pointer flex-shrink-0 group"
                        :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                        aria-label="Toggle Light/Dark Theme">
                    <!-- Sliding Knob -->
                    <span class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white dark:bg-slate-900 shadow-md flex items-center justify-center transition-all duration-300 ease-out"
                          :class="darkMode ? 'translate-x-7' : 'translate-x-0'">
                        <i data-lucide="sun" class="w-3.5 h-3.5 text-amber-500 transition-opacity duration-200" :class="darkMode ? 'opacity-0 absolute' : 'opacity-100'"></i>
                        <i data-lucide="moon" class="w-3.5 h-3.5 text-indigo-400 transition-opacity duration-200" :class="darkMode ? 'opacity-100' : 'opacity-0 absolute'"></i>
                    </span>
                    <!-- Track Icons (Background Hints) -->
                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-amber-400/60 dark:opacity-0 transition-opacity duration-300"></span>
                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-indigo-400/60 opacity-0 dark:opacity-100 transition-opacity duration-300"></span>
                </button>

                @auth
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
                <button @click="mobileNav = !mobileNav" type="button" class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Toggle Navigation">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>

        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white/98 dark:bg-slate-900/98 px-5 py-4 space-y-2.5" style="display: none;" x-cloak @click.away="mobileNav = false">
            <a href="{{ route('home') }}#fitur" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">Fitur Platform</a>
            <a href="{{ route('home') }}#koneksi" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">Metode Koneksi</a>
            <a href="{{ route('home') }}#playground" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">API Documentation</a>
            <a href="{{ route('pages.faq') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">FAQ</a>
            <a href="{{ route('pages.support') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">Pusat Bantuan</a>
            <a href="{{ route('pages.terms') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">Ketentuan Layanan</a>
            <a href="{{ route('pages.privacy') }}" @click="mobileNav = false" class="block text-xs font-semibold py-1.5 text-slate-700 dark:text-slate-300 hover:text-blue-600">Kebijakan Privasi</a>
            @guest
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex gap-2">
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
    <footer class="bg-white dark:bg-[#0D1526] border-t border-slate-200 dark:border-slate-800 mt-20 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                
                <!-- Brand & Description -->
                <div class="col-span-2 space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto max-w-[150px] object-contain">
                        @else
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </div>
                            <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">{{ strtoupper($siteName) }}</span>
                        @endif
                    </div>
                    <p class="text-xs font-medium text-slate-600 dark:text-slate-400 max-w-sm leading-relaxed">
                        {{ $siteDescription }}
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <span class="app-tag app-tag-emerald text-[10px]">Uptime 99.9%</span>
                        <span class="app-tag app-tag-blue text-[10px]">Enterprise WA Core</span>
                    </div>
                </div>

                <!-- Produk -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3.5">Produk</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600 dark:text-slate-400">
                        <li><a href="{{ route('home') }}#fitur" class="hover:text-blue-600 transition-colors">Fitur Gateway</a></li>
                        <li><a href="{{ route('home') }}#koneksi" class="hover:text-blue-600 transition-colors">Dual Connection</a></li>
                        <li><a href="{{ route('home') }}#playground" class="hover:text-blue-600 transition-colors">REST API v1</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Console Dashboard</a></li>
                    </ul>
                </div>

                <!-- Bantuan -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3.5">Dukungan</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600 dark:text-slate-400">
                        <li><a href="{{ route('pages.faq') }}" class="hover:text-blue-600 transition-colors">Tanya Jawab (FAQ)</a></li>
                        <li><a href="{{ route('pages.support') }}" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-blue-600 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Infrastruktur -->
                <div>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3.5">Infrastruktur</h4>
                    <ul class="space-y-2 text-xs font-medium text-slate-600 dark:text-slate-400 font-mono text-[11px]">
                        <li><span>Laravel 11 PHP 8.3</span></li>
                        <li><span>Node.js Engine Core v1.0</span></li>
                        <li><span>Multi-Device Sockets</span></li>
                        <li><span>Webhook Dispatcher</span></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-medium text-slate-500 dark:text-slate-400">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. Hak cipta dilindungi.</p>
                <div class="flex items-center gap-3 font-mono text-[11px] text-slate-400 dark:text-slate-500">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> SYSTEM ONLINE</span>
                    <span>•</span>
                    <span>WAKTU SERVER: {{ date('H:i') }} WIB</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
