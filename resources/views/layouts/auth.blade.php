<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Autentikasi') · {{ $siteName }}</title>
    <meta name="title" content="@yield('meta_title', 'Autentikasi · ' . $siteName)">
    <meta name="description" content="{{ $siteDescription ?? 'Masuk ke console Whatsapp Gateway Enterprise untuk mengelola multi-device dan integrasi REST API berkecepatan tinggi.' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2563EB">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Autentikasi · ' . ($siteName ?? 'Whatsapp Gateway Enterprise'))">
    <meta property="og:description" content="{{ $siteDescription ?? 'Masuk ke console Whatsapp Gateway Enterprise untuk mengelola multi-device dan integrasi REST API berkecepatan tinggi.' }}">
    <meta property="og:image" content="{{ asset('og-image.svg') }}">
    <meta property="og:image:type" content="image/svg+xml">

    <!-- Favicon -->
    @if(!empty($siteFavicon))
        <link rel="shortcut icon" href="{{ $siteFavicon }}?v={{ substr(md5($siteFavicon), 0, 6) }}">
        <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}?v={{ substr(md5($siteFavicon), 0, 6) }}">
        <link rel="icon" type="image/png" href="{{ $siteFavicon }}?v={{ substr(md5($siteFavicon), 0, 6) }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}?v={{ substr(md5($siteFavicon), 0, 6) }}">
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
<body class="h-full bg-white text-slate-800 font-sans antialiased selection:bg-blue-600 selection:text-white">

    <div class="relative w-full h-full min-h-screen">
        <main class="w-full h-full min-h-screen grid lg:grid-cols-[1fr_auto]">
            
            <!-- Left Section: Form Area -->
            <section class="h-full min-h-screen flex flex-col justify-between items-center p-6 sm:p-8 lg:p-12 bg-white">
                
                <!-- Top Brand Header -->
                <div class="w-full flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        @if(!empty($siteLogo))
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 shadow-xs p-1 flex items-center justify-center flex-shrink-0 group-hover:scale-105 group-hover:border-blue-300 transition-all">
                                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="max-h-full max-w-full object-contain rounded-lg">
                            </div>
                        @else
                            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/30 group-hover:scale-105 transition-transform flex-shrink-0">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            </div>
                        @endif
                        <span class="font-extrabold text-base tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">{{ $siteName }}</span>
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
                    <p>&copy; {{ date('Y') }} {{ $siteName }}. Hak cipta dilindungi.</p>
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
                        <span>Enterprise Engine v1.0 Ready</span>
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
