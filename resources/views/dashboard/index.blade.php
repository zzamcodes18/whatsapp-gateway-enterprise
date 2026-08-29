@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">WORKSPACE</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">LIVE TELEMETRY</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-slate-900">Dashboard Overview</h1>
            <p class="text-xs text-slate-500 font-medium">Ringkasan status koneksi perangkat dan lalu lintas pengiriman pesan gateway.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('devices.index') }}" class="app-btn app-btn-secondary text-xs py-2 px-3.5 flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Tambah Device</span>
            </a>
            <a href="{{ route('messages.index') }}" class="app-btn app-btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                <span>Kirim Pesan</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Connected Devices -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Perangkat Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-slate-900">{{ $connectedDevices }}</span>
                <span class="text-xs text-slate-400 font-semibold">/ {{ $totalDevices }} Unit</span>
            </div>
            <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1.5">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span>Engine Socket Ready</span>
            </div>
        </div>

        <!-- Card 2: Today Messages -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Pesan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-slate-900">{{ $todayMessages }}</span>
                <span class="text-xs text-slate-400 font-semibold">Terkirim</span>
            </div>
            <div class="text-[11px] font-medium text-slate-500">
                Total seumur hidup: <strong class="text-slate-800">{{ $totalMessages }}</strong>
            </div>
        </div>

        <!-- Card 3: Delivery Rate -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Success Rate</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-slate-900">{{ $deliveryRate }}%</span>
            </div>
            <div class="text-[11px] font-medium text-emerald-600">
                Tingkat keberhasilan kirim
            </div>
        </div>

        <!-- Card 4: API Keys -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>API Keys Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="key" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-slate-900">{{ $apiKeysCount }}</span>
                <span class="text-xs text-slate-400 font-semibold">Kunci</span>
            </div>
            <div class="text-[11px] font-medium text-slate-500">
                Token SHA-256 Hashed
            </div>
        </div>
    </div>

    <!-- Active Devices & Audit Stream -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Devices List (8 cols) -->
        <div class="lg:col-span-8 space-y-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-base text-slate-900">Perangkat WhatsApp Terdaftar</h2>
                    <span class="app-tag app-tag-slate text-[10px]">{{ $devices->count() }} UNIT</span>
                </div>
                <a href="{{ route('devices.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Kelola Semua &rarr;
                </a>
            </div>

            @if($devices->isEmpty())
                <div class="app-card p-8 text-center space-y-3 bg-white">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl mx-auto flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-sm text-slate-900">Belum Ada Perangkat Tertaut</h3>
                        <p class="text-xs text-slate-500 font-medium max-w-xs mx-auto">
                            Tautkan nomor WhatsApp Anda sekarang dengan scan QR atau 8-digit Pairing Code.
                        </p>
                    </div>
                    <a href="{{ route('devices.index') }}" class="app-btn app-btn-primary text-xs py-2 px-4 inline-flex items-center gap-1.5">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Perangkat Baru</span>
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($devices as $device)
                        <div class="app-card p-4 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs {{ $device->status === 'connected' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-xs text-slate-900">{{ $device->name }}</h4>
                                        <span class="app-tag text-[9px] {{ $device->status === 'connected' ? 'app-tag-emerald' : 'app-tag-amber' }}">
                                            {{ strtoupper($device->status) }}
                                        </span>
                                    </div>
                                    <div class="font-mono text-[11px] text-slate-500 font-medium mt-0.5">
                                        {{ $device->phone_number ? '+' . $device->phone_number : 'Belum ditautkan nomor' }} • Mode: <span class="uppercase font-semibold text-slate-700">{{ $device->connection_type }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <a href="{{ route('devices.show', $device) }}" class="app-btn app-btn-secondary text-[11px] py-1.5 px-3">
                                    Detail Sesi
                                </a>
                                @if($device->status === 'connected')
                                    <button type="button" @click="$confirm({
                                        title: 'Putuskan Koneksi Device',
                                        message: 'Apakah Anda yakin ingin memutuskan sesi WhatsApp pada device \'{{ $device->name }}\'?',
                                        confirmText: 'Disconnect',
                                        type: 'danger',
                                        onConfirm: () => document.getElementById('disconnect-dash-{{ $device->id }}').submit()
                                    })" class="app-btn app-btn-soft-danger text-[11px] py-1.5 px-3 cursor-pointer">
                                        Disconnect
                                    </button>
                                    <form id="disconnect-dash-{{ $device->id }}" method="POST" action="{{ route('devices.disconnect', $device) }}" class="hidden">
                                        @csrf
                                    </form>
                                @else
                                    <a href="{{ route('devices.show', $device) }}" class="app-btn app-btn-soft-blue text-[11px] py-1.5 px-3">
                                        Hubungkan
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Engine Spec & Recent Audit Stream (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Engine Card -->
            <div class="app-card p-5 bg-gradient-to-br from-slate-900 to-blue-950 text-white space-y-3 shadow-md border-blue-900/50">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <span class="font-mono text-xs font-bold text-sky-400">ENGINE CORE</span>
                    <span class="app-tag app-tag-emerald text-[9px]">ONLINE</span>
                </div>
                <div class="space-y-1.5 font-mono text-[11px]">
                    <div class="text-slate-400">Engine Core: <span class="text-white font-bold">Enterprise Protocol</span></div>
                    <div class="text-slate-400">Version: <span class="text-sky-300 font-bold">v1.0.0 Enterprise</span></div>
                    <div class="text-slate-400">Port Engine: <span class="text-emerald-400 font-bold">3000 (Internal)</span></div>
                </div>
            </div>

            <!-- Activity Logs Card -->
            <div class="app-card p-4 bg-white space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h3 class="font-bold text-xs text-slate-900">Aktivitas Terkini</h3>
                    <span class="text-[10px] font-mono font-semibold text-slate-400">AUDIT LOG</span>
                </div>
                
                <div class="space-y-2">
                    @forelse($recentLogs as $log)
                        <div class="p-2.5 bg-slate-50/80 border border-slate-100 rounded-xl text-[11px] space-y-1">
                            <div class="flex items-center justify-between font-semibold">
                                <span class="font-mono text-[9px] bg-white px-1.5 py-0.5 border border-slate-200 rounded text-slate-700">{{ $log->event }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 font-medium text-[11px] truncate">{{ $log->description }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 font-medium py-2 text-center">Belum ada catatan aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
