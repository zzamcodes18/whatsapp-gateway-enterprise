@extends('layouts.app')

@section('title', $device->name . ' · Detail Perangkat')

@section('content')
<div x-data="deviceDetail({
    status: '{{ $device->status }}',
    features: {
        always_online: {{ $device->always_online ? 'true' : 'false' }},
        typing_indicator: {{ $device->typing_indicator ? 'true' : 'false' }},
        auto_read: {{ $device->auto_read ? 'true' : 'false' }},
        block_calls: {{ $device->block_calls ? 'true' : 'false' }}
    },
    statusUrl: '{{ route('devices.status', $device) }}',
    featuresUrl: '{{ route('devices.features', $device) }}',
    consoleUrl: '{{ route('devices.console-logs', $device) }}'
})" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-5">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <a href="{{ route('devices.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Daftar</span>
                </a>
                <span class="app-tag text-[10px] {{ $device->getStatusBadgeClass() }}" x-text="statusLabel"></span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl text-navy flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                </span>
                {{ $device->name }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono font-medium">
                {{ $device->phone_number ? '+' . $device->phone_number : 'Belum ditautkan nomor' }} • {{ substr($device->session_id, 0, 8) }}…
            </p>
        </div>

        <!-- Session Controls -->
        <div class="flex flex-wrap items-center gap-2">
            @if(in_array($device->status, ['connected', 'connecting', 'qr_ready', 'pairing_ready']))
                <form method="POST" action="{{ route('devices.stop', $device) }}">
                    @csrf
                    <button type="submit" class="app-btn app-btn-secondary text-xs py-2 px-4 flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="pause" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Stop Sesi</span>
                    </button>
                </form>
            @elseif($device->status === 'stopped')
                <form method="POST" action="{{ route('devices.start', $device) }}">
                    @csrf
                    <button type="submit" class="app-btn text-xs py-2 px-4 flex items-center gap-1.5 cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                        <span>Start Sesi</span>
                    </button>
                </form>
            @endif

            @if(in_array($device->status, ['connected', 'stopped', 'connecting', 'qr_ready', 'pairing_ready']))
                <button type="button" @click="$confirm({
                    title: 'Putuskan & Hapus Sesi',
                    message: 'Sesi WhatsApp akan diputuskan dan kredensial DIHAPUS permanen. Device harus di-pairing ulang dengan QR/Pairing Code. Lanjutkan?',
                    confirmText: 'Ya, Hapus Sesi',
                    type: 'danger',
                    onConfirm: () => document.getElementById('disconnect-show-form').submit()
                })" class="app-btn app-btn-danger text-xs py-2 px-4 flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="power" class="w-3.5 h-3.5"></i>
                    <span>Disconnect / Hapus Sesi</span>
                </button>
                <form id="disconnect-show-form" method="POST" action="{{ route('devices.disconnect', $device) }}" class="hidden">
                    @csrf
                </form>
            @endif
        </div>
    </div>

    <!-- Diagnostics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="app-card p-5 bg-white dark:bg-[#111A2E] space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                <span class="font-bold text-xs text-navy">Informasi Device</span>
                <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Status:</span>
                    <strong class="text-slate-900 dark:text-white" x-text="statusLabel"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Metode:</span>
                    <strong class="text-slate-900 dark:text-white uppercase">{{ $device->connection_type === 'qr' ? 'QR Code' : 'Pairing Code' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Tersambung:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->connected_at ? $device->connected_at->format('d M Y H:i') : '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="app-card p-5 bg-white dark:bg-[#111A2E] space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                <span class="font-bold text-xs text-navy">Engine Telemetry</span>
                <i data-lucide="cpu" class="w-4 h-4 text-indigo-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Engine:</span>
                    <strong class="text-slate-900 dark:text-white">Enterprise WA Core v1.0</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Platform:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->metadata['platform'] ?? 'Chrome Linux' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Pushname:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->metadata['pushname'] ?? ($device->metadata['name'] ?? '-') }}</strong>
                </div>
            </div>
        </div>

        <div class="app-card p-5 bg-white dark:bg-[#111A2E] space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                <span class="font-bold text-xs text-navy">Statistik Pesan</span>
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Total:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->messages()->count() }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Outbound:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->messages()->where('direction', 'outbound')->count() }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Inbound:</span>
                    <strong class="text-slate-900 dark:text-white">{{ $device->messages()->where('direction', 'inbound')->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Active QR or Pairing Screen if not connected & not stopped -->
    @if(!in_array($device->status, ['connected', 'stopped']))
        <div class="app-card p-6 bg-blue-50/40 dark:bg-blue-500/5 border-blue-100 dark:border-blue-500/20 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></span>
                    <h3 class="font-bold text-sm text-navy">Menghubungkan Perangkat</h3>
                </div>
                <span class="app-tag app-tag-amber text-[9px]">WAITING AUTH</span>
            </div>

            @if($device->pairing_code)
                <div class="p-6 bg-white dark:bg-[#0D1526] border border-blue-100 dark:border-blue-500/20 rounded-2xl text-center space-y-2.5">
                    <div class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">MASUKKAN KODE INI DI APLIKASI WHATSAPP HP</div>
                    <div class="inline-block px-8 py-3 bg-blue-600 text-white rounded-xl font-mono font-extrabold text-2xl sm:text-3xl tracking-widest shadow-md">
                        {{ $device->pairing_code }}
                    </div>
                </div>
            @elseif($device->qr_code)
                <div class="p-6 bg-white dark:bg-[#0D1526] border border-blue-100 dark:border-blue-500/20 rounded-2xl text-center space-y-2.5">
                    <div class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">SCAN QR DENGAN WHATSAPP HP</div>
                    <div class="w-52 h-52 mx-auto bg-white border border-slate-200 rounded-xl p-2 shadow-sm">
                        <img src="{{ $device->qr_code }}" alt="QR Code" class="w-full h-full object-contain">
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                    Sesi sedang diinisialisasi. Kode/QR akan muncul otomatis dalam beberapa detik — halaman ini memantau status secara realtime.
                </p>
            @endif
        </div>
    @endif

    <!-- ================= TABS ================= -->
    <div class="space-y-4">
        <!-- Tab Bar -->
        <div class="flex items-center gap-1.5 border-b border-slate-200 dark:border-slate-800 overflow-x-auto">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-300'" class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer whitespace-nowrap">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                <span>Overview</span>
            </button>
            <button @click="activeTab = 'features'" :class="activeTab === 'features' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-300'" class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer whitespace-nowrap">
                <i data-lucide="settings-2" class="w-3.5 h-3.5"></i>
                <span>Fitur</span>
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-extrabold" :class="activeFeatureCount > 0 ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'" x-text="activeFeatureCount + '/4'"></span>
            </button>
            <button @click="activeTab = 'console'" :class="activeTab === 'console' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-300'" class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer whitespace-nowrap">
                <i data-lucide="terminal" class="w-3.5 h-3.5"></i>
                <span>Console</span>
                <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="autoRefresh ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'"></span>
            </button>
            <button @click="activeTab = 'messages'" :class="activeTab === 'messages' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-300'" class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold border-b-2 transition-all cursor-pointer whitespace-nowrap">
                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                <span>Pesan</span>
            </button>
        </div>

        <!-- ============ TAB: OVERVIEW ============ -->
        <div x-show="activeTab === 'overview'" x-cloak class="space-y-4">
            <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-navy flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i>
                        Status Realtime
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-mono text-slate-400" x-text="lastSyncText"></span>
                        <button @click="refreshStatus()" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all cursor-pointer" title="Refresh status">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="refreshing ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Koneksi</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="status === 'connected' ? 'bg-emerald-500 animate-pulse' : (status === 'stopped' ? 'bg-slate-400' : 'bg-amber-500 animate-pulse')"></span>
                            <strong class="text-xs text-slate-900 dark:text-white" x-text="statusLabel"></strong>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fitur Aktif</span>
                        <strong class="text-xs text-slate-900 dark:text-white"><span x-text="activeFeatureCount"></span> / 4</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pesan</span>
                        <strong class="text-xs text-slate-900 dark:text-white">{{ number_format($device->messages()->count()) }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tersambung Sejak</span>
                        <strong class="text-xs text-slate-900 dark:text-white">{{ $device->connected_at ? $device->connected_at->diffForHumans() : '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Recent Messages (compact) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-navy">Riwayat Pesan Terakhir</h3>
                    <a href="{{ route('messages.index', ['device_id' => $device->id]) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        Buka Semua &rarr;
                    </a>
                </div>

                @if($recentMessages->isEmpty())
                    <div class="app-card p-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium bg-white dark:bg-[#111A2E]">
                        Belum ada pesan terkirim melalui perangkat ini.
                    </div>
                @else
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs font-medium app-table min-w-[650px]">
                            <thead>
                                <tr>
                                    <th class="p-3.5">Tujuan / Dari</th>
                                    <th class="p-3.5">Tipe</th>
                                    <th class="p-3.5">Pesan</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMessages as $msg)
                                    <tr>
                                        <td class="p-3.5 font-mono font-bold text-slate-900 dark:text-white">{{ $msg->clean_phone }}</td>
                                        <td class="p-3.5 font-mono uppercase text-[10px]">{{ $msg->message_type }}</td>
                                        <td class="p-3.5 max-w-xs truncate text-slate-700 dark:text-slate-300">{{ $msg->message_content }}</td>
                                        <td class="p-3.5">
                                            <span class="app-tag text-[9px] {{ in_array($msg->status, ['sent', 'delivered', 'read']) ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                                {{ strtoupper($msg->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 text-slate-400 dark:text-slate-500 font-mono text-[11px] whitespace-nowrap">{{ $msg->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- ============ TAB: FITUR ============ -->
        <div x-show="activeTab === 'features'" x-cloak class="space-y-4">
            <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-bold text-sm text-navy flex items-center gap-2">
                            <i data-lucide="settings-2" class="w-4 h-4 text-blue-600"></i>
                            Fitur Device
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Aktifkan perilaku khusus sesi WhatsApp. Perubahan langsung diterapkan tanpa restart.</p>
                    </div>
                    <span class="app-tag text-[9px]" :class="status === 'connected' ? 'app-tag-emerald' : 'app-tag-amber'" x-text="status === 'connected' ? 'LIVE SYNC' : 'TERSIMPAN'"></span>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <!-- Always Online -->
                    <div class="p-4 rounded-xl border transition-all" :class="features.always_online ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-slate-200 dark:border-slate-800'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="wifi" class="w-4 h-4" :class="features.always_online ? 'text-emerald-600' : 'text-slate-400'"></i>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Always Online</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Device selalu terlihat online 24 jam meskipun tidak aktif.</p>
                            </div>
                            <button type="button" @click="toggleFeature('always_online')" class="relative w-10 h-6 rounded-full transition-all cursor-pointer flex-shrink-0" :class="features.always_online ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all" :class="features.always_online ? 'translate-x-4' : ''"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div class="p-4 rounded-xl border transition-all" :class="features.typing_indicator ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-slate-200 dark:border-slate-800'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="pen-line" class="w-4 h-4" :class="features.typing_indicator ? 'text-emerald-600' : 'text-slate-400'"></i>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Typing Indicator</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Tampilkan indikator "sedang menulis…" saat ada pesan masuk.</p>
                            </div>
                            <button type="button" @click="toggleFeature('typing_indicator')" class="relative w-10 h-6 rounded-full transition-all cursor-pointer flex-shrink-0" :class="features.typing_indicator ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all" :class="features.typing_indicator ? 'translate-x-4' : ''"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Auto Read -->
                    <div class="p-4 rounded-xl border transition-all" :class="features.auto_read ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-slate-200 dark:border-slate-800'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-check" class="w-4 h-4" :class="features.auto_read ? 'text-emerald-600' : 'text-slate-400'"></i>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Auto Read</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Pesan masuk otomatis ditandai sudah dibaca (centang biru).</p>
                            </div>
                            <button type="button" @click="toggleFeature('auto_read')" class="relative w-10 h-6 rounded-full transition-all cursor-pointer flex-shrink-0" :class="features.auto_read ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all" :class="features.auto_read ? 'translate-x-4' : ''"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Block Calls -->
                    <div class="p-4 rounded-xl border transition-all" :class="features.block_calls ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/5' : 'border-slate-200 dark:border-slate-800'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="phone-off" class="w-4 h-4" :class="features.block_calls ? 'text-emerald-600' : 'text-slate-400'"></i>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Block Call / Video Call</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Tolak otomatis semua panggilan voice & video yang masuk.</p>
                            </div>
                            <button type="button" @click="toggleFeature('block_calls')" class="relative w-10 h-6 rounded-full transition-all cursor-pointer flex-shrink-0" :class="features.block_calls ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all" :class="features.block_calls ? 'translate-x-4' : ''"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Toast notification -->
                <div x-show="featureToast" x-cloak x-transition class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold" :class="featureToastType === 'success' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400'">
                    <i data-lucide="check-circle-2" x-show="featureToastType === 'success'" class="w-4 h-4"></i>
                    <i data-lucide="alert-circle" x-show="featureToastType === 'error'" class="w-4 h-4"></i>
                    <span x-text="featureToastMsg"></span>
                </div>
            </div>
        </div>

        <!-- ============ TAB: CONSOLE ============ -->
        <div x-show="activeTab === 'console'" x-cloak class="space-y-4">
            <div class="app-card overflow-hidden bg-slate-950 border border-slate-800 shadow-2xs">
                <!-- Console Header -->
                <div class="flex items-center justify-between px-4 py-3 bg-slate-900 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <span class="ml-2 text-[11px] font-mono font-bold text-slate-400">engine://console/{{ substr($device->session_id, 0, 8) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="autoRefresh = !autoRefresh" class="px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono transition-all cursor-pointer" :class="autoRefresh ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:text-white'">
                            <span x-text="autoRefresh ? '● LIVE' : '○ PAUSED'"></span>
                        </button>
                        <button @click="loadConsoleLogs(true)" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all cursor-pointer" title="Refresh logs">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="consoleLoading ? 'animate-spin' : ''"></i>
                        </button>
                        <button @click="consoleLogs = []" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all cursor-pointer" title="Bersihkan tampilan">
                            <i data-lucide="eraser" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Console Body -->
                <div class="p-4 h-80 overflow-y-auto font-mono text-[11px] leading-relaxed space-y-0.5" x-ref="consoleBody">
                    <div x-show="consoleLogs.length === 0 && !consoleLoading" class="text-slate-600 text-center py-12">
                        <i data-lucide="terminal" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        <p>Menunggu log aktivitas device…</p>
                    </div>
                    <template x-for="(log, i) in consoleLogs" :key="i">
                        <div class="flex gap-2.5 items-start">
                            <span class="text-slate-600 flex-shrink-0" x-text="formatLogTime(log.timestamp)"></span>
                            <span class="flex-shrink-0 font-bold" :class="{
                                'text-emerald-400': log.level === 'success',
                                'text-blue-400': log.level === 'info',
                                'text-amber-400': log.level === 'warn',
                                'text-rose-400': log.level === 'error'
                            }" x-text="log.level.toUpperCase().padEnd(7)"></span>
                            <span class="text-slate-300 break-all" x-text="log.message"></span>
                        </div>
                    </template>
                </div>

                <!-- Console Footer -->
                <div class="flex items-center justify-between px-4 py-2 bg-slate-900 border-t border-slate-800">
                    <span class="text-[10px] font-mono text-slate-500" x-text="consoleLogs.length + ' baris log'"></span>
                    <span class="text-[10px] font-mono text-slate-500" x-text="autoRefresh ? 'auto-refresh: 5s' : 'auto-refresh: off'"></span>
                </div>
            </div>
        </div>

        <!-- ============ TAB: PESAN ============ -->
        <div x-show="activeTab === 'messages'" x-cloak class="space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-navy">Riwayat Pesan Terakhir</h3>
                    <a href="{{ route('messages.index', ['device_id' => $device->id]) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        Buka Semua &rarr;
                    </a>
                </div>

                @if($recentMessages->isEmpty())
                    <div class="app-card p-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium bg-white dark:bg-[#111A2E]">
                        Belum ada pesan terkirim melalui perangkat ini.
                    </div>
                @else
                    <div class="app-table-wrapper">
                        <table class="w-full text-left text-xs font-medium app-table min-w-[650px]">
                            <thead>
                                <tr>
                                    <th class="p-3.5">Tujuan / Dari</th>
                                    <th class="p-3.5">Tipe</th>
                                    <th class="p-3.5">Pesan</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMessages as $msg)
                                    <tr>
                                        <td class="p-3.5 font-mono font-bold text-slate-900 dark:text-white">{{ $msg->clean_phone }}</td>
                                        <td class="p-3.5 font-mono uppercase text-[10px]">{{ $msg->message_type }}</td>
                                        <td class="p-3.5 max-w-xs truncate text-slate-700 dark:text-slate-300">{{ $msg->message_content }}</td>
                                        <td class="p-3.5">
                                            <span class="app-tag text-[9px] {{ in_array($msg->status, ['sent', 'delivered', 'read']) ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                                {{ strtoupper($msg->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 text-slate-400 dark:text-slate-500 font-mono text-[11px] whitespace-nowrap">{{ $msg->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
function deviceDetail(config) {
    return {
        activeTab: 'overview',
        status: config.status,
        features: config.features,
        refreshing: false,
        lastSyncText: '',

        // Console state
        consoleLogs: [],
        consoleLoading: false,
        autoRefresh: true,
        consoleTimer: null,

        // Toast
        featureToast: false,
        featureToastMsg: '',
        featureToastType: 'success',
        toastTimer: null,

        init() {
            this.refreshStatus();
            setInterval(() => this.refreshStatus(), 10000);

            this.$watch('activeTab', (tab) => {
                if (tab === 'console') {
                    this.startConsolePolling();
                } else {
                    this.stopConsolePolling();
                }
            });

            this.$watch('autoRefresh', (val) => {
                if (val && this.activeTab === 'console') {
                    this.startConsolePolling();
                } else {
                    this.stopConsolePolling();
                }
            });
        },

        get statusLabel() {
            const map = {
                connected: 'Terhubung',
                connecting: 'Menghubungkan',
                qr_ready: 'Menunggu QR Scan',
                pairing_ready: 'Menunggu Pairing Code',
                stopped: 'Dihentikan',
                disconnected: 'Terputus',
            };
            return map[this.status] || this.status;
        },

        get activeFeatureCount() {
            return Object.values(this.features).filter(Boolean).length;
        },

        async refreshStatus() {
            this.refreshing = true;
            try {
                const res = await fetch(config.statusUrl, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.success && data.device) {
                    this.status = data.device.status;
                }
                this.lastSyncText = 'sync ' + new Date().toLocaleTimeString('id-ID');
            } catch (e) {
                this.lastSyncText = 'sync gagal';
            } finally {
                this.refreshing = false;
            }
        },

        async toggleFeature(key) {
            this.features[key] = !this.features[key];
            try {
                const res = await fetch(config.featuresUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ [key]: this.features[key] }),
                });
                const data = await res.json();

                if (!data.success) {
                    this.features[key] = !this.features[key];
                    this.showToast('error', data.message || 'Gagal memperbarui fitur.');
                } else {
                    const labels = {
                        always_online: 'Always Online',
                        typing_indicator: 'Typing Indicator',
                        auto_read: 'Auto Read',
                        block_calls: 'Block Calls',
                    };
                    this.showToast('success', labels[key] + (this.features[key] ? ' diaktifkan' : ' dinonaktifkan') + '.');
                }
            } catch (e) {
                this.features[key] = !this.features[key];
                this.showToast('error', 'Terjadi kesalahan jaringan.');
            }
        },

        async loadConsoleLogs(manual = false) {
            if (manual) this.consoleLoading = true;
            try {
                const res = await fetch(config.consoleUrl + '?limit=200', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.success) {
                    this.consoleLogs = data.logs || [];
                    this.$nextTick(() => {
                        if (this.$refs.consoleBody) {
                            this.$refs.consoleBody.scrollTop = this.$refs.consoleBody.scrollHeight;
                        }
                    });
                }
            } catch (e) {
                // silent fail
            } finally {
                this.consoleLoading = false;
            }
        },

        startConsolePolling() {
            this.loadConsoleLogs();
            if (!this.consoleTimer) {
                this.consoleTimer = setInterval(() => {
                    if (this.autoRefresh) this.loadConsoleLogs();
                }, 5000);
            }
        },

        stopConsolePolling() {
            if (this.consoleTimer) {
                clearInterval(this.consoleTimer);
                this.consoleTimer = null;
            }
        },

        formatLogTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleTimeString('id-ID', { hour12: false });
        },

        showToast(type, msg) {
            this.featureToastType = type;
            this.featureToastMsg = msg;
            this.featureToast = true;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => this.featureToast = false, 3000);
        },
    };
}
</script>
@endsection
