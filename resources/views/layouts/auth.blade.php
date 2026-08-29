<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Autentikasi · Whatsapp Gateway Enterprise')</title>
    <meta name="title" content="@yield('meta_title', 'Autentikasi · Whatsapp Gateway Enterprise')">
    <meta name="description" content="Masuk ke console Whatsapp Gateway Enterprise untuk mengelola multi-device dan integrasi REST API berkecepatan tinggi.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2563EB">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Autentikasi · Whatsapp Gateway Enterprise')">
    <meta property="og:description" content="Masuk ke console LAPAKOTP WhatsApp Gateway untuk mengelola multi-device dan integrasi REST API berkecepatan tinggi.">
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
<body class="h-full bg-white text-slate-800 font-sans antialiased selection:bg-blue-600 selection:text-white">

    <div class="relative w-full h-full min-h-screen">
        <main class="w-full h-full min-h-screen grid lg:grid-cols-[1fr_auto]">
            
            <!-- Left Section: Form Area -->
            <section class="h-full min-h-screen flex flex-col justify-between items-center p-6 sm:p-8 lg:p-12 bg-white">
                
                <!-- Top Brand Header -->
                <div class="w-full flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg tracking-tight text-navy">LAPAK<span class="text-blue-600">OTP</span></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">GATEWAY</span>
                        </div>
                    </a>

                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 flex items-center gap-1 transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Beranda</span>
                    </a>
                </div>

                <!-- Center Main Form Container -->
                <div class="w-full max-w-md my-auto py-8">
                    
                    <!-- Flash Notification -->
                    @if(session('status'))
                        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl font-semibold flex items-center gap-2.5 text-xs shadow-2xs">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl font-semibold flex items-start gap-2.5 text-xs shadow-2xs">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <div class="font-bold">Terjadi Kesalahan:</div>
                                <ul class="list-disc list-inside mt-0.5 space-y-0.5 font-medium text-[11px]">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <!-- Footer Text -->
                <div class="w-full text-center text-xs text-slate-400 font-medium pt-4">
                    <p>&copy; {{ date('Y') }} Whatsapp Gateway Enterprise. Hak cipta dilindungi.</p>
                </div>

            </section>

            <!-- Right Aside: Fly.io Luxury Visual & Quote Hero -->
            <aside class="relative hidden lg:flex lg:w-[28rem] xl:w-[34rem] min-h-screen p-12 xl:p-16 bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950 text-white flex-col justify-between overflow-hidden">
                
                <!-- Ambient Glowing Background Accents -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#3B82F6_1px,transparent_1px)] [background-size:24px_24px] opacity-15 pointer-events-none"></div>

                <!-- Top Pill Badge -->
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-blue-200 shadow-sm">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span>Baileys Engine v7.0 Ready</span>
                    </div>
                </div>

                <!-- Center Quote Inspired by Fly.io Layout -->
                <div class="relative z-10 space-y-6">
                    <blockquote class="text-2xl xl:text-3xl font-extrabold text-white leading-snug tracking-tight">
                        <p class="text-blue-200/90 font-medium text-lg mb-2">✦ High-Speed WhatsApp Gateway</p>
                        <p>Simplicity rules.</p>
                        <p>No complicated DevOps required.</p>
                        <p class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-indigo-200 to-white">Code, connect, accomplish.</p>
                    </blockquote>

                    <cite class="block not-italic text-sm text-slate-300 font-medium">
                        <span class="opacity-40">&mdash; </span>Infrastruktur Multi-Device & REST API Terpadu
                    </cite>

                    <!-- Feature Tags Grid -->
                    <div class="grid grid-cols-2 gap-3 pt-4 font-mono text-xs text-slate-300">
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xs flex items-center gap-2.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                            <span>OTP Realtime</span>
                        </div>
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xs flex items-center gap-2.5">
                            <i data-lucide="qr-code" class="w-4 h-4 text-blue-400"></i>
                            <span>QR & Pairing Code</span>
                        </div>
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xs flex items-center gap-2.5">
                            <i data-lucide="webhook" class="w-4 h-4 text-emerald-400"></i>
                            <span>Webhook Events</span>
                        </div>
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xs flex items-center gap-2.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-400"></i>
                            <span>SHA-256 API Keys</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Telemetry Status -->
                <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400 font-mono">
                    <span>UPTIME: 99.9%</span>
                    <span>LATENCY: &lt;150ms</span>
                </div>

            </aside>

        </main>
    </div>

</body>
</html>
