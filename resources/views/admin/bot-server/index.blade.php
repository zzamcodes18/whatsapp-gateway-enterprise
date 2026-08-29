@extends('layouts.app')

@section('title', 'Server Bot OTP · Admin Gateway')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">SYSTEM BOT ROUTER</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Konfigurasi Server Bot OTP</h1>
            <p class="text-xs text-slate-500 font-medium">Tentukan perangkat WhatsApp resmi yang digunakan sistem untuk mengirim kode OTP dan notifikasi otomatis.</p>
        </div>
    </div>

    <!-- Active Bot Server Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Settings Form (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="app-card p-6 bg-white space-y-4 shadow-2xs">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-base text-navy">Atur Perangkat Bot OTP</h2>
                        <p class="text-xs text-slate-500 font-medium">Pilih salah satu koneksi perangkat aktif untuk dijadikan gateway pesan sistem.</p>
                    </div>
                    <span class="app-tag app-tag-blue text-[9px]">SYSTEM SENDER</span>
                </div>

                <form method="POST" action="{{ route('admin.bot-server.assign') }}" class="space-y-4">
                    @csrf

                    <!-- Device Selector -->
                    <div class="space-y-1.5">
                        <label for="device_id" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Pilih Perangkat WhatsApp Aktif</label>
                        <select name="device_id" id="device_id" class="input-text text-xs font-semibold cursor-pointer">
                            <option value="">-- Tidak Ada (Nonaktifkan Bot Server) --</option>
                            @foreach($connectedDevices as $dev)
                                <option value="{{ $dev->id }}" {{ ($botDevice && $botDevice->id === $dev->id) ? 'selected' : '' }}>
                                    {{ $dev->name }} (+{{ $dev->phone_number }}) - Pemilik: {{ $dev->user->name ?? 'User' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500">Hanya perangkat dengan status <strong>CONNECTED</strong> yang dapat dipilih.</p>
                    </div>

                    <!-- OTP Template -->
                    <div class="space-y-1.5">
                        <label for="otp_template" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Format Template Pesan OTP</label>
                        <textarea name="otp_template" id="otp_template" rows="3" required class="input-text text-xs font-medium">{{ old('otp_template', $otpTemplate) }}</textarea>
                        <p class="text-[11px] text-slate-500">Gunakan placeholder <code>{otp}</code> untuk disisipkan kode OTP secara dinamis.</p>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="btn-xl btn-primary py-2.5 px-5 text-xs flex items-center gap-2 cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Konfigurasi Bot</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Test Send OTP Form & Current Status (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Bot Status Card -->
            <div class="app-card p-5 bg-white space-y-3 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="font-bold text-xs text-navy">Status Perangkat Bot</span>
                    <i data-lucide="bot" class="w-4 h-4 text-blue-600"></i>
                </div>

                @if($botDevice)
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nama:</span>
                            <strong class="text-slate-900">{{ $botDevice->name }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nomor:</span>
                            <strong class="text-slate-900">+{{ $botDevice->phone_number }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Status:</span>
                            <span class="app-tag text-[9px] {{ $botDevice->status === 'connected' ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                {{ strtoupper($botDevice->status) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-amber-700 font-medium py-1">
                        Belum ada perangkat yang ditetapkan sebagai Bot Server OTP.
                    </p>
                @endif
            </div>

            <!-- Test OTP Sender Box -->
            <div class="app-card p-5 bg-gradient-to-br from-blue-50/70 to-indigo-50/40 border-blue-100 space-y-3.5 shadow-2xs">
                <div class="flex items-center gap-2 font-bold text-xs text-navy">
                    <i data-lucide="send" class="w-4 h-4 text-blue-600"></i>
                    <span>Uji Kirim Pesan OTP</span>
                </div>

                <form method="POST" action="{{ route('admin.bot-server.test-otp') }}" class="space-y-3">
                    @csrf

                    <div class="space-y-1">
                        <label for="test_phone" class="block font-bold text-[11px] uppercase tracking-wider text-slate-600">Nomor Tujuan HP</label>
                        <input type="text" name="phone" id="test_phone" required placeholder="08123456789 atau 628123456789" class="input-text text-xs font-mono font-bold">
                    </div>

                    <div class="space-y-1">
                        <label for="custom_code" class="block font-bold text-[11px] uppercase tracking-wider text-slate-600">Kode OTP Uji Coba (Opsional)</label>
                        <input type="text" name="custom_code" id="custom_code" placeholder="Contoh: 894201 (Kosongkan utk acak)" class="input-text text-xs font-mono font-bold">
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full btn-xl btn-primary py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                            <span>Kirim Test OTP Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
