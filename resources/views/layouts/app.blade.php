<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Console') · {{ $siteName }}</title>
    <meta name="title" content="@yield('meta_title', 'Console · ' . $siteName)">
    <meta name="description" content="{{ $siteDescription ?? 'Console manajemen gateway WhatsApp terpadu. Pantau nomor terhubung, kirim pesan REST API, dan kelola API Key.' }}">
    <meta name="keywords" content="{{ $siteKeywords ?? 'whatsapp gateway, wa gateway api, whatsapp otp, whatsapp bot api, rest api whatsapp' }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2563EB">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Console · {{ $siteName ?? 'Whatsapp Gateway Enterprise' }}">
    <meta property="og:description" content="{{ $siteDescription ?? 'Console manajemen gateway WhatsApp terpadu.' }}">
    <meta property="og:image" content="{{ asset('og-image.svg') }}">
    <meta property="og:image:type" content="image/svg+xml">

    <!-- Favicon -->
    @php
        $faviconSrc = $siteFavicon ?? $siteLogo;
    @endphp
    @if(!empty($faviconSrc))
        <link rel="shortcut icon" href="{{ $faviconSrc }}">
        <link rel="icon" type="image/x-icon" href="{{ $faviconSrc }}">
        <link rel="icon" type="image/png" href="{{ $faviconSrc }}">
        <link rel="apple-touch-icon" href="{{ $faviconSrc }}">
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
<body class="h-full bg-[#F8FAFC] dark:bg-[#0B1120] text-slate-900 dark:text-slate-200 font-sans antialiased overflow-hidden selection:bg-blue-600 selection:text-white transition-colors" 
      x-data="{ 
          mobileOpen: false,
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

    <!-- Full Screen App Shell Container -->
    <div class="h-[100dvh] w-full flex overflow-hidden">

        <!-- ================= FULL SCREEN DESKTOP SIDEBAR ================= -->
        <aside class="hidden md:flex w-64 lg:w-72 h-[100dvh] flex-col justify-between bg-white dark:bg-[#0D1526] border-r border-slate-200/80 dark:border-slate-800 shadow-xs z-30 flex-shrink-0 select-none overflow-y-auto custom-scrollbar touch-scroll transition-colors">
            
            <!-- Top: Brand Header & Navigation Menu -->
            <div class="p-5 space-y-6">
                
                <!-- Brand Logo (Navigates to Landing Page) -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group" title="Lihat Website Landing Page">
                    @if(!empty($siteLogo))
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 shadow-xs p-1 flex items-center justify-center flex-shrink-0 group-hover:scale-105 group-hover:border-blue-300 transition-all">
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="max-h-full max-w-full object-contain rounded-lg" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.classList.remove('hidden'); this.parentElement.nextElementSibling.classList.add('flex');">
                        </div>
                        <div class="hidden w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform flex-shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform flex-shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </div>
                    @endif
                    <div class="flex flex-col truncate">
                        <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white truncate leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $siteName }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 tracking-wide uppercase">WhatsApp Gateway</span>
                    </div>
                </a>

                <!-- Nav Menu Items -->
                <nav class="space-y-1 text-xs font-semibold">
                    @if(request()->routeIs('admin.*'))
                        <!-- ADMIN PANEL NAVIGATION -->
                        <div class="px-3 py-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Administrator Panel</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 font-extrabold">ADMIN</span>
                        </div>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="shield" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Admin Overview</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Kelola Pengguna</span>
                        </a>

                        <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.plans.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="package" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Kelola Paket (Plan)</span>
                        </a>

                        <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="server" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Semua Device Sistem</span>
                        </a>

                        <a href="{{ route('admin.bot-server.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bot-server.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="bot" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Server Bot OTP</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="settings" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Pengaturan Website</span>
                        </a>
                    @else
                        <!-- USER WORKSPACE NAVIGATION -->
                        <div class="px-3 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            Workspace
                        </div>

                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Dashboard Overview</span>
                        </a>

                        <a href="{{ route('devices.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="smartphone" class="w-4 h-4 flex-shrink-0"></i>
                                <span>Perangkat WA</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('devices.*') ? 'bg-white/20 text-white' : 'bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-200/50 dark:border-blue-500/20' }}">v1.0</span>
                        </a>

                        <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('messages.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="send" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Kirim & Log Pesan</span>
                        </a>

                        <a href="{{ route('templates.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('templates.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="file-code-2" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Template Pesan</span>
                        </a>

                        <a href="{{ route('api-keys.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('api-keys.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="key-round" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Integrasi & Callback</span>
                        </a>

                        <a href="{{ route('plans.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('plans.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="rocket" class="w-4 h-4 flex-shrink-0"></i>
                                <span>Upgrade Paket</span>
                            </div>
                            @if(!auth()->user()->plan || !auth()->user()->hasActivePlan() || (auth()->user()->plan && auth()->user()->plan->slug === 'free'))
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('plans.*') ? 'bg-white/20 text-white' : 'bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-200/50 dark:border-amber-500/20' }}">NEW</span>
                            @endif
                        </a>

                        <a href="{{ route('docs.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('docs.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                            <i data-lucide="book-open" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Dokumentasi API</span>
                        </a>
                    @endif
                </nav>

            </div>

            <!-- Bottom: Quota Widget -->
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-transparent transition-colors">

                <!-- Quota Widget -->
                <div class="p-3 bg-white dark:bg-[#111A2E] border border-slate-200/80 dark:border-slate-800 rounded-xl space-y-2 text-xs shadow-2xs transition-colors">
                    <div class="flex items-center justify-between font-bold text-slate-800 dark:text-slate-200 text-[11px]">
                        <span>Kuota Pesan Hari Ini</span>
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600"></i>
                    </div>
                    <div class="font-mono text-xs font-bold text-slate-900 dark:text-white">
                        {{ auth()->user()->messages_sent_today }} / {{ auth()->user()->effectiveDailyMessageLimit() ? auth()->user()->effectiveDailyMessageLimit() . ' msg' : 'Unlimited' }}
                    </div>
                    @php
                        $effLimit = auth()->user()->effectiveDailyMessageLimit();
                        $pct = $effLimit ? min(100, round((auth()->user()->messages_sent_today / $effLimit) * 100)) : 0;
                    @endphp
                    <div class="w-full bg-slate-200/80 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                    </div>

                    @php $effMonthlyLimit = auth()->user()->effectiveMonthlyMessageLimit(); @endphp
                    @if($effMonthlyLimit > 0)
                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-slate-200 text-[11px]">
                                <span>Kuota Bulanan</span>
                                <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-violet-600"></i>
                            </div>
                            <div class="font-mono text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format(auth()->user()->messages_sent_this_month, 0, ',', '.') }} / {{ number_format($effMonthlyLimit, 0, ',', '.') }} msg
                            </div>
                            @php
                                $monthlyPct = min(100, round((auth()->user()->messages_sent_this_month / $effMonthlyLimit) * 100));
                            @endphp
                            <div class="w-full bg-slate-200/80 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $monthlyPct >= 90 ? 'bg-rose-500' : ($monthlyPct >= 70 ? 'bg-amber-500' : 'bg-violet-600') }} h-1.5 rounded-full transition-all duration-300" style="width: {{ $monthlyPct }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

            </div>

        </aside>

        <!-- ================= MAIN CONTENT VIEWPORT ================= -->
        <div class="flex-1 flex flex-col min-w-0 h-[100dvh] overflow-hidden">
            
            <!-- Top Viewport Navbar -->
            <header class="h-16 bg-white/95 dark:bg-[#0D1526]/95 border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-shrink-0 z-20 shadow-xs transition-colors">
                
                <!-- Left: Mobile Menu Toggle & Title -->
                <div class="flex items-center gap-3">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors" aria-label="Toggle Menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="hidden sm:inline-flex text-xs font-semibold text-slate-400 dark:text-slate-500">Console /</span>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                            @yield('title', $siteName)
                        </h2>
                    </div>
                </div>

                <!-- Right: Telemetry Badge & Profile Dropdown -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    
                    <!-- Engine Status Pill Badge -->
                    <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-500/20 rounded-full text-xs font-semibold shadow-2xs transition-colors">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span>Engine v1.0 Ready</span>
                    </div>

                    <!-- Light / Dark Theme Switcher (Animated Pill Toggle) -->
                    <button @click="toggleTheme()" 
                            type="button" 
                            class="relative w-12 h-6.5 rounded-full bg-slate-200 dark:bg-slate-700 border border-slate-300/70 dark:border-slate-600 transition-colors duration-300 cursor-pointer flex-shrink-0"
                            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                            aria-label="Toggle Light/Dark Theme">
                        <!-- Sliding Knob -->
                        <span class="absolute top-0.5 left-0.5 w-5.5 h-5.5 rounded-full bg-white dark:bg-slate-900 shadow-md flex items-center justify-center transition-all duration-300 ease-out"
                              :class="darkMode ? 'translate-x-5.5' : 'translate-x-0'">
                            <i data-lucide="sun" class="w-3 h-3 text-amber-500 transition-opacity duration-200" :class="darkMode ? 'opacity-0 absolute' : 'opacity-100'"></i>
                            <i data-lucide="moon" class="w-3 h-3 text-indigo-400 transition-opacity duration-200" :class="darkMode ? 'opacity-100' : 'opacity-0 absolute'"></i>
                        </span>
                        <!-- Track Icons (Background Hints) -->
                        <span class="absolute right-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-amber-400/60 dark:opacity-0 transition-opacity duration-300"></span>
                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-indigo-400/60 opacity-0 dark:opacity-100 transition-opacity duration-300"></span>
                    </button>

                    <!-- Profile Dropdown Component -->
                    <div class="relative" x-data="{ profileOpen: false }" @keydown.escape.window="profileOpen = false">
                        
                        <!-- Profile Trigger Button -->
                        <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-white dark:bg-[#111A2E] hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-2xs transition-all cursor-pointer focus:outline-none" aria-expanded="false">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shadow-2xs flex-shrink-0">
                                <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-xs uppercase shadow-2xs flex-shrink-0" style="display: none;">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-xs uppercase shadow-2xs flex-shrink-0">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                                </div>
                            @endif
                            <div class="hidden md:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ auth()->user()->role }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': profileOpen }"></i>
                        </button>

                        <!-- Dropdown Menu Box -->
                        <div x-show="profileOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             @click.outside="profileOpen = false"
                             class="absolute right-0 mt-2 w-64 bg-white dark:bg-[#111A2E] rounded-2xl shadow-xl border border-slate-200/90 dark:border-slate-700 py-2 z-50 text-xs font-semibold space-y-1 transition-colors"
                             style="display: none;" x-cloak>
                            
                            <!-- Header Info -->
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                <div class="flex items-center gap-2.5">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 shadow-xs flex-shrink-0">
                                        <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center font-extrabold text-xs uppercase shadow-xs flex-shrink-0" style="display: none;">
                                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                                        </div>
                                    @else
                                        <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center font-extrabold text-xs uppercase shadow-xs flex-shrink-0">
                                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col text-left truncate">
                                        <span class="font-extrabold text-slate-900 dark:text-white truncate leading-tight">{{ auth()->user()->name }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                                <div class="mt-2.5 flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ auth()->user()->isAdmin() ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                        ROLE: {{ auth()->user()->role }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Limit: {{ auth()->user()->daily_message_limit ? auth()->user()->daily_message_limit . ' msg' : 'Unlimited' }}</span>
                                </div>
                            </div>

                            <!-- Menu Links -->
                            <div class="p-1 space-y-0.5">
                                @if(auth()->user()->isAdmin())
                                    @if(request()->routeIs('admin.*'))
                                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-blue-600"></i>
                                            <span>Beralih ke Panel User</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <i data-lucide="shield" class="w-4 h-4 text-blue-600"></i>
                                            <span>Dashboard Admin</span>
                                        </a>
                                    @endif
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    <i data-lucide="user-cog" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                                    <span>Profil & Workspace</span>
                                </a>
                            </div>

                            <!-- Logout Action -->
                            <div class="pt-1 border-t border-slate-100 dark:border-slate-800 p-1">
                                <button type="button" @click="profileOpen = false; $confirm({
                                    title: 'Konfirmasi Keluar',
                                    message: 'Apakah Anda yakin ingin mengakhiri sesi console ini?',
                                    confirmText: 'Keluar',
                                    cancelText: 'Batal',
                                    type: 'danger',
                                    onConfirm: () => document.getElementById('logout-form').submit()
                                })" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-bold">
                                    <i data-lucide="log-out" class="w-4 h-4 text-rose-600"></i>
                                    <span>Keluar Sesi</span>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

            </header>

            <!-- Scrollable Content Container -->
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 custom-scrollbar">
                
                <!-- Flash Notification Banner -->
                @if(session('success'))
                    <div class="mb-5 app-card p-3.5 bg-emerald-50/90 border-emerald-200 text-emerald-900 font-semibold flex items-center justify-between gap-3 text-xs sm:text-sm shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="p-1 text-emerald-700 hover:bg-emerald-100 rounded-lg cursor-pointer">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 app-card p-3.5 bg-rose-50/90 border-rose-200 text-rose-900 font-semibold flex items-start gap-2.5 text-xs sm:text-sm shadow-sm">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <div class="font-bold">Pemberitahuan:</div>
                            <ul class="list-disc list-inside mt-0.5 space-y-0.5 font-medium text-xs">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')

            </main>

        </div>

    </div>

    <!-- ================= MOBILE SLIDE-OUT DRAWER MENU ================= -->
    <div x-show="mobileOpen" class="fixed inset-0 z-50 md:hidden" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="mobileOpen = false"></div>

        <div class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-[#0D1526] border-r border-slate-200 dark:border-slate-800 p-5 flex flex-col justify-between shadow-2xl z-10 overflow-y-auto custom-scrollbar touch-scroll transition-colors">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-2xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </div>
                        <span class="font-extrabold text-sm text-navy dark:text-white tracking-tight">MENU UTAMA</span>
                    </div>
                    <button @click="mobileOpen = false" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <nav class="space-y-1 text-xs font-semibold">
                    @if(request()->routeIs('admin.*'))
                        <div class="px-2 text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Administrator Panel</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 font-extrabold">ADMIN</span>
                        </div>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Admin Overview</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                        <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="server" class="w-4 h-4"></i>
                            <span>Semua Device Sistem</span>
                        </a>
                        <a href="{{ route('admin.bot-server.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bot-server.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                            <span>Server Bot OTP</span>
                        </a>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 mt-3">
                            <a href="{{ route('dashboard') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold transition-all text-xs border border-slate-200/80 dark:border-slate-700">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                                    <span>Workspace User</span>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="px-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            Workspace
                        </div>

                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard Overview</span>
                        </a>

                        <a href="{{ route('devices.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="smartphone" class="w-4 h-4"></i>
                                <span>Device WhatsApp</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('devices.*') ? 'bg-white/20 text-white' : 'bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300' }}">Live</span>
                        </a>

                        <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('messages.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirim Pesan & Log</span>
                        </a>

                        <a href="{{ route('templates.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('templates.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="file-code-2" class="w-4 h-4"></i>
                            <span>Template Pesan</span>
                        </a>

                        <a href="{{ route('api-keys.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('api-keys.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                            <span>Integrasi & Callback</span>
                        </a>

                        <a href="{{ route('docs.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('docs.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i data-lucide="book-open" class="w-4 h-4"></i>
                            <span>Dokumentasi API</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('pages.support') }}" class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                    <span>Pusat Bantuan & FAQ</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= GLOBAL TOAST CONTAINER ================= -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0" x-data x-cloak>
        <template x-for="item in $store.toast.items" :key="item.id">
            <div class="pointer-events-auto app-card p-3.5 shadow-xl border flex items-center justify-between gap-3 text-xs font-semibold transition-all transform duration-200"
                 :class="{
                    'bg-emerald-600 text-white border-emerald-700': item.type === 'success',
                    'bg-rose-600 text-white border-rose-700': item.type === 'error',
                    'bg-blue-600 text-white border-blue-700': item.type === 'info',
                    'bg-amber-500 text-white border-amber-600': item.type === 'warning'
                 }">
                <div class="flex items-center gap-2">
                    <template x-if="item.type === 'success'"><i data-lucide="check-circle-2" class="w-4 h-4"></i></template>
                    <template x-if="item.type === 'error'"><i data-lucide="alert-circle" class="w-4 h-4"></i></template>
                    <template x-if="item.type === 'info'"><i data-lucide="info" class="w-4 h-4"></i></template>
                    <template x-if="item.type === 'warning'"><i data-lucide="alert-triangle" class="w-4 h-4"></i></template>
                    <span x-text="item.message"></span>
                </div>
                <button @click="$store.toast.remove(item.id)" class="p-0.5 hover:opacity-75 cursor-pointer">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- ================= GLOBAL CUSTOM CONFIRMATION MODAL ================= -->
    <div x-data x-show="$store.dialog.isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100 dark:border-slate-700" @click.away="$store.dialog.cancel()">
            
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold flex-shrink-0"
                     :class="{
                        'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-500/20': $store.dialog.type === 'danger',
                        'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200/80 dark:border-amber-500/20': $store.dialog.type === 'warning',
                        'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200/80 dark:border-blue-500/20': $store.dialog.type === 'primary' || $store.dialog.type === 'info'
                     }">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-base text-navy dark:text-white" x-text="$store.dialog.title"></h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed" x-text="$store.dialog.message"></p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" @click="$store.dialog.cancel()" class="app-btn app-btn-secondary text-xs py-1.5 px-3.5">
                    <span x-text="$store.dialog.cancelText"></span>
                </button>
                <button type="button" @click="$store.dialog.confirm()" 
                        class="app-btn text-xs py-1.5 px-4"
                        :class="{
                            'app-btn-danger': $store.dialog.type === 'danger',
                            'app-btn-primary': $store.dialog.type === 'primary' || $store.dialog.type === 'info' || $store.dialog.type === 'warning'
                        }">
                    <span x-text="$store.dialog.confirmText"></span>
                </button>
            </div>

        </div>
    </div>

</body>
</html>
