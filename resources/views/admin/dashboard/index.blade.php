@extends('layouts.app')

@section('title', 'Admin Overview · Whatsapp Gateway Enterprise')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">SYSTEM TELEMETRY</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Admin Control Center</h1>
            <p class="text-xs text-slate-500 font-medium">Monitoring seluruh pengguna, kuota harian, semua perangkat, dan server bot OTP.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.users.index') }}" class="btn-xl btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
                <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                <span>Kelola User</span>
            </a>
            <a href="{{ route('admin.bot-server.index') }}" class="app-btn app-btn-secondary text-xs py-2 px-3.5 flex items-center gap-1.5">
                <i data-lucide="bot" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Atur Bot Server</span>
            </a>
        </div>
    </div>

    <!-- Admin KPI Metric Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Users -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Total Pengguna</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-navy">{{ $totalUsers }}</span>
                <span class="text-xs text-slate-400 font-semibold">({{ $activeUsers }} Aktif)</span>
            </div>
            <div class="text-[11px] font-medium text-slate-500">
                User terdaftar di sistem
            </div>
        </div>

        <!-- Metric 2: Global Devices -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Total Perangkat</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="server" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-navy">{{ $connectedDevices }}</span>
                <span class="text-xs text-slate-400 font-semibold">/ {{ $totalDevices }} Total</span>
            </div>
            <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1.5">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span>Baileys Sockets Aktif</span>
            </div>
        </div>

        <!-- Metric 3: System Messages Today -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Pesan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="font-extrabold text-2xl sm:text-3xl text-navy">{{ $todayMessages }}</span>
                <span class="text-xs text-slate-400 font-semibold">Pesan</span>
            </div>
            <div class="text-[11px] font-medium text-slate-500">
                Total se-sistem: <strong class="text-slate-800">{{ $totalMessages }}</strong>
            </div>
        </div>

        <!-- Metric 4: Bot Server OTP Status -->
        <div class="app-card p-4 sm:p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Server Bot OTP</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                @if($botDevice && $botDevice->isConnected())
                    <span class="font-bold text-sm text-emerald-700">ONLINE TERHUBUNG</span>
                @elseif($botDevice)
                    <span class="font-bold text-sm text-amber-600">OFFLINE</span>
                @else
                    <span class="font-bold text-sm text-rose-600">BELUM DIATUR</span>
                @endif
            </div>
            <div class="text-[11px] font-medium text-slate-500 truncate">
                {{ $botDevice ? $botDevice->name : 'Pilih di menu Bot Server' }}
            </div>
        </div>
    </div>

    <!-- Main Admin Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Recent Users Table (7 cols) -->
        <div class="lg:col-span-7 space-y-3.5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-base text-navy">Pengguna Terbaru Terdaftar</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Kelola Semua Pengguna &rarr;
                </a>
            </div>

            <div class="app-table-wrapper">
                <table class="w-full text-left text-xs font-medium app-table min-w-[650px]">
                    <thead>
                        <tr>
                            <th class="p-3.5">Nama & Email</th>
                            <th class="p-3.5">Role</th>
                            <th class="p-3.5">Limit Device</th>
                            <th class="p-3.5">Limit Pesan/Hari</th>
                            <th class="p-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $usr)
                            <tr>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">{{ $usr->name }}</div>
                                    <div class="font-mono text-[11px] text-slate-500">{{ $usr->email }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="app-tag text-[9px] {{ $usr->isAdmin() ? 'app-tag-blue' : 'app-tag-slate' }}">
                                        {{ strtoupper($usr->role) }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono font-bold text-slate-800">{{ $usr->device_limit ?? 3 }} Unit</td>
                                <td class="p-3.5 font-mono font-bold text-slate-800">{{ $usr->daily_message_limit ? $usr->daily_message_limit . ' msg' : 'Unlimited' }}</td>
                                <td class="p-3.5">
                                    <span class="app-tag text-[9px] {{ $usr->is_active ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                        {{ $usr->is_active ? 'Aktif' : 'Suspended' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent System Audit Logs (5 cols) -->
        <div class="lg:col-span-5 space-y-3.5">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-base text-navy">Log Aktivitas Sistem</h2>
                <span class="font-mono text-[10px] font-semibold text-slate-400">REALTIME AUDIT</span>
            </div>

            <div class="app-card p-4 bg-white space-y-2.5">
                @forelse($recentLogs as $log)
                    <div class="p-3 bg-slate-50/80 border border-slate-100 rounded-xl text-[11px] space-y-1">
                        <div class="flex items-center justify-between font-semibold">
                            <span class="font-mono text-[9px] bg-white px-2 py-0.5 border border-slate-200 rounded text-slate-700">{{ $log->event }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-800 font-medium text-[11px]">{{ $log->description }}</p>
                        @if($log->user)
                            <div class="text-[10px] text-slate-500 font-mono">User: {{ $log->user->email }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-slate-400 font-medium py-3 text-center">Belum ada log tercatat.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
