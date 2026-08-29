@extends('layouts.app')

@section('title', $device->name . ' · Detail Perangkat')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('devices.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Daftar</span>
                </a>
                <span class="app-tag text-[10px] {{ $device->status === 'connected' ? 'app-tag-emerald' : 'app-tag-amber' }}">
                    {{ strtoupper($device->status) }}
                </span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">{{ $device->name }}</h1>
            <p class="text-xs text-slate-500 font-mono font-medium">
                {{ $device->phone_number ? '+' . $device->phone_number : 'Belum ditautkan nomor' }} • Session UUID: {{ $device->session_id }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('devices.restart', $device) }}">
                @csrf
                <button type="submit" class="app-btn app-btn-secondary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-600"></i>
                    <span>Restart Sesi</span>
                </button>
            </form>

            @if($device->status === 'connected')
                <button type="button" @click="$confirm({
                    title: 'Putuskan WhatsApp',
                    message: 'Apakah Anda yakin ingin memutuskan sesi WhatsApp pada device ini?',
                    confirmText: 'Disconnect',
                    type: 'danger',
                    onConfirm: () => document.getElementById('disconnect-show-form').submit()
                })" class="app-btn app-btn-danger text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="power" class="w-3.5 h-3.5"></i>
                    <span>Disconnect</span>
                </button>
                <form id="disconnect-show-form" method="POST" action="{{ route('devices.disconnect', $device) }}" class="hidden">
                    @csrf
                </form>
            @endif
        </div>
    </div>

    <!-- Diagnostics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="app-card p-5 bg-white space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-bold text-xs text-navy">Informasi Device</span>
                <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Status:</span>
                    <strong class="text-slate-900 uppercase">{{ $device->status }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode:</span>
                    <strong class="text-slate-900 uppercase">{{ $device->connection_type }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tersambung:</span>
                    <strong class="text-slate-900">{{ $device->connected_at ? $device->connected_at->format('d M Y H:i') : '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="app-card p-5 bg-white space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-bold text-xs text-navy">Engine Telemetry</span>
                <i data-lucide="cpu" class="w-4 h-4 text-indigo-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Engine:</span>
                    <strong class="text-slate-900">Enterprise WA Core v1.0</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Platform:</span>
                    <strong class="text-slate-900">{{ $device->metadata['platform'] ?? 'Chrome Linux' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pushname:</span>
                    <strong class="text-slate-900">{{ $device->metadata['pushname'] ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="app-card p-5 bg-white space-y-3 shadow-2xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-bold text-xs text-navy">Statistik Pesan</span>
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="space-y-1.5 font-mono text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total:</span>
                    <strong class="text-slate-900">{{ $device->messages()->count() }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Outbound:</span>
                    <strong class="text-slate-900">{{ $device->messages()->where('direction', 'outbound')->count() }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Inbound:</span>
                    <strong class="text-slate-900">{{ $device->messages()->where('direction', 'inbound')->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Active QR or Pairing Screen if not connected -->
    @if($device->status !== 'connected')
        <div class="app-card p-6 bg-blue-50/40 border-blue-100 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></span>
                    <h3 class="font-bold text-sm text-navy">Menghubungkan Perangkat</h3>
                </div>
                <span class="app-tag app-tag-amber text-[9px]">WAITING AUTH</span>
            </div>

            @if($device->pairing_code)
                <div class="p-6 bg-white border border-blue-100 rounded-2xl text-center space-y-2.5">
                    <div class="text-xs font-mono font-bold text-slate-500">MASUKKAN KODE INI DI APLIKASI WHATSAPP HP</div>
                    <div class="inline-block px-8 py-3 bg-blue-600 text-white rounded-xl font-mono font-extrabold text-2xl sm:text-3xl tracking-widest shadow-md">
                        {{ $device->pairing_code }}
                    </div>
                </div>
            @elseif($device->qr_code)
                <div class="p-6 bg-white border border-blue-100 rounded-2xl text-center space-y-2.5">
                    <div class="text-xs font-mono font-bold text-slate-500">SCAN QR DENGAN WHATSAPP HP</div>
                    <div class="w-52 h-52 mx-auto bg-white border border-slate-200 rounded-xl p-2 shadow-sm">
                        <img src="{{ $device->qr_code }}" alt="QR Code" class="w-full h-full object-contain">
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-600 font-medium">
                    Sesi sedang diinisialisasi. Silakan klik tombol <strong>Restart Sesi</strong> jika kode belum muncul.
                </p>
            @endif
        </div>
    @endif

    <!-- Recent Messages -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-base text-navy">Riwayat Pesan Terakhir</h3>
            <a href="{{ route('messages.index', ['device_id' => $device->id]) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Buka Semua &rarr;
            </a>
        </div>

        @if($recentMessages->isEmpty())
            <div class="app-card p-8 text-center text-xs text-slate-400 font-medium bg-white">
                Belum ada pesan terkirim melalui perangkat ini.
            </div>
        @else
            <!-- Responsive Table Wrapper with Horizontal Scroll -->
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
                                <td class="p-3.5 font-mono font-bold text-slate-900">{{ $msg->clean_phone }}</td>
                                <td class="p-3.5 font-mono uppercase text-[10px]">{{ $msg->message_type }}</td>
                                <td class="p-3.5 max-w-xs truncate text-slate-700">{{ $msg->message_content }}</td>
                                <td class="p-3.5">
                                    <span class="app-tag text-[9px] {{ in_array($msg->status, ['sent', 'delivered', 'read']) ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                        {{ strtoupper($msg->status) }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-400 font-mono text-[11px] whitespace-nowrap">{{ $msg->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
