<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Console · Whatsapp Gateway Enterprise')</title>
    <meta name="title" content="@yield('meta_title', 'Console · Whatsapp Gateway Enterprise')">
    <meta name="description" content="Console manajemen gateway WhatsApp terpadu. Pantau nomor terhubung, kirim pesan REST API, dan kelola API Key.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2563EB">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Console · Whatsapp Gateway Enterprise">
    <meta property="og:description" content="Console manajemen gateway WhatsApp terpadu.">
    <meta property="og:image" content="{{ asset('og-image.svg') }}">
    <meta property="og:image:type" content="image/svg+xml">

    <!-- SVG Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F8FAFC] text-slate-900 font-sans antialiased overflow-hidden selection:bg-blue-600 selection:text-white" x-data="{ mobileOpen: false }">

    <!-- Full Screen App Shell Container -->
    <div class="h-screen w-full flex overflow-hidden">

        <!-- ================= FULL SCREEN DESKTOP SIDEBAR ================= -->
        <aside class="hidden md:flex w-64 lg:w-72 h-screen flex-col justify-between bg-white border-r border-slate-200/80 shadow-xs z-30 flex-shrink-0 select-none overflow-y-auto custom-scrollbar">
            
            <!-- Top: Brand Header & Navigation Menu -->
            <div class="p-5 space-y-6">
                
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-lg tracking-tight text-navy">LAPAK<span class="text-blue-600">OTP</span></span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">GATEWAY</span>
                    </div>
                </a>

                <!-- Nav Menu Items -->
                <nav class="space-y-1 text-xs font-semibold">
                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Workspace
                    </div>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Dashboard Overview</span>
                    </a>

                    <a href="{{ route('devices.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="smartphone" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Perangkat WA</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('devices.*') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700 border border-blue-200/50' }}">v7.0</span>
                    </a>

                    <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('messages.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                        <i data-lucide="send" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Kirim & Log Pesan</span>
                    </a>

                    <a href="{{ route('api-keys.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('api-keys.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                        <i data-lucide="key" class="w-4 h-4 flex-shrink-0"></i>
                        <span>API Keys & Docs</span>
                    </a>

                    <a href="{{ route('webhooks.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('webhooks.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                        <i data-lucide="webhook" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Webhook Callbacks</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <div class="pt-4 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-t border-slate-100 mt-3">
                            Administrator Panel
                        </div>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                            <i data-lucide="shield" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Admin Overview</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Kelola Pengguna</span>
                        </a>

                        <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                            <i data-lucide="server" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Semua Device Sistem</span>
                        </a>

                        <a href="{{ route('admin.bot-server.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bot-server.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25 font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-slate-900' }}">
                            <i data-lucide="bot" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Server Bot OTP</span>
                        </a>
                    @endif
                </nav>

            </div>

            <!-- Bottom: Quota Widget & User Card -->
            <div class="p-4 border-t border-slate-200/80 space-y-3 bg-slate-50/50">
                
                <!-- Quota Widget -->
                <div class="p-3 bg-white border border-slate-200/80 rounded-xl space-y-2 text-xs shadow-2xs">
                    <div class="flex items-center justify-between font-bold text-slate-800 text-[11px]">
                        <span>Kuota Pesan Hari Ini</span>
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600"></i>
                    </div>
                    <div class="font-mono text-xs font-bold text-slate-900">
                        {{ auth()->user()->messages_sent_today }} / {{ auth()->user()->daily_message_limit ? auth()->user()->daily_message_limit . ' msg' : 'Unlimited' }}
                    </div>
                    @php
                        $pct = auth()->user()->daily_message_limit ? min(100, round((auth()->user()->messages_sent_today / auth()->user()->daily_message_limit) * 100)) : 0;
                    @endphp
                    <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                <!-- User Profile & Logout -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                    <div class="flex items-center gap-2.5 truncate">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 shadow-2xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="flex flex-col text-left truncate">
                            <span class="text-xs font-bold text-slate-900 truncate leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">{{ auth()->user()->role }}</span>
                        </div>
                    </div>

                    <button type="button" @click="$confirm({
                        title: 'Konfirmasi Keluar',
                        message: 'Apakah Anda yakin ingin mengakhiri sesi console ini?',
                        confirmText: 'Keluar',
                        cancelText: 'Batal',
                        type: 'danger',
                        onConfirm: () => document.getElementById('logout-form').submit()
                    })" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer flex-shrink-0" title="Logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>

            </div>

        </aside>

        <!-- ================= MAIN CONTENT VIEWPORT ================= -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
            
            <!-- Top Viewport Navbar -->
            <header class="h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-shrink-0 z-20 shadow-xs">
                
                <!-- Left: Mobile Menu Toggle & Title -->
                <div class="flex items-center gap-3">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="md:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors" aria-label="Toggle Menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="hidden sm:inline-flex text-xs font-semibold text-slate-400">Console /</span>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 truncate">
                            @yield('title', 'Whatsapp Gateway Enterprise')
                        </h2>
                    </div>
                </div>

                <!-- Right: Telemetry Engine Badge -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/70 rounded-full text-xs font-semibold shadow-2xs">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="hidden sm:inline">Engine v7.0 Ready</span>
                        <span class="sm:hidden">Online</span>
                    </div>

                    <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" title="Lihat Website Landing">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
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
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="mobileOpen = false"></div>

        <div class="fixed inset-y-0 left-0 w-72 bg-white border-r border-slate-200 p-5 flex flex-col justify-between shadow-2xl z-10 overflow-y-auto custom-scrollbar">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-2xs">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <span class="font-extrabold text-sm text-navy tracking-tight">MENU UTAMA</span>
                    </div>
                    <button @click="mobileOpen = false" class="p-1.5 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <nav class="space-y-1 text-xs font-semibold">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard Overview</span>
                    </a>

                    <a href="{{ route('devices.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="smartphone" class="w-4 h-4"></i>
                            <span>Device WhatsApp</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ request()->routeIs('devices.*') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700' }}">Live</span>
                    </a>

                    <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('messages.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pesan & Log</span>
                    </a>

                    <a href="{{ route('api-keys.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('api-keys.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i data-lucide="key" class="w-4 h-4"></i>
                        <span>API Keys & Docs</span>
                    </a>

                    <a href="{{ route('webhooks.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('webhooks.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                        <i data-lucide="webhook" class="w-4 h-4"></i>
                        <span>Webhook Callback</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <div class="pt-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            Administrator Panel
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Admin Overview</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                        <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.devices.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="server" class="w-4 h-4"></i>
                            <span>Semua Device Sistem</span>
                        </a>
                        <a href="{{ route('admin.bot-server.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bot-server.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                            <span>Server Bot OTP</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <a href="{{ route('pages.support') }}" class="flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600">
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
        <div class="app-card bg-white max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="$store.dialog.cancel()">
            
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold flex-shrink-0"
                     :class="{
                        'bg-rose-50 text-rose-600 border border-rose-200/80': $store.dialog.type === 'danger',
                        'bg-amber-50 text-amber-600 border border-amber-200/80': $store.dialog.type === 'warning',
                        'bg-blue-50 text-blue-600 border border-blue-200/80': $store.dialog.type === 'primary' || $store.dialog.type === 'info'
                     }">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-base text-navy" x-text="$store.dialog.title"></h3>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed" x-text="$store.dialog.message"></p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
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
