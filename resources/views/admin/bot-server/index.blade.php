@extends('layouts.app')

@section('title', 'Server Bot OTP')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">SYSTEM BOT ROUTER</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Konfigurasi Server Bot OTP</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tentukan perangkat WhatsApp resmi yang digunakan sistem untuk mengirim kode OTP dan notifikasi otomatis.</p>
        </div>
    </div>

    <!-- Active Bot Server Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Settings Form (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4 shadow-2xs">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-base text-navy">Atur Perangkat Bot OTP</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pilih salah satu koneksi perangkat aktif untuk dijadikan gateway pesan sistem.</p>
                    </div>
                    <span class="app-tag app-tag-blue text-[9px]">SYSTEM SENDER</span>
                </div>

                <form method="POST" action="{{ route('admin.bot-server.assign') }}" class="space-y-4">
                    @csrf

                    <!-- Device Selector -->
                    <div class="space-y-1.5">
                        <label for="device_id" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Pilih Perangkat WhatsApp Aktif</label>
                        <select name="device_id" id="device_id" class="input-text text-xs font-semibold cursor-pointer">
                            <option value="">-- Tidak Ada (Nonaktifkan Bot Server) --</option>
                            @foreach($connectedDevices as $dev)
                                <option value="{{ $dev->id }}" {{ ($botDevice && $botDevice->id === $dev->id) ? 'selected' : '' }}>
                                    {{ $dev->name }} (+{{ $dev->phone_number }}) - Pemilik: {{ $dev->user->name ?? 'User' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Hanya perangkat dengan status <strong>CONNECTED</strong> yang dapat dipilih.</p>
                    </div>

                    <!-- OTP Register Toggle Switch -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/80 dark:border-slate-700 flex items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <label for="enable_register_otp" class="font-bold text-xs text-slate-800 dark:text-slate-200 cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                <span>Verifikasi OTP WhatsApp Saat Registrasi</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Jika aktif, pendaftar baru wajib memverifikasi 6 digit kode OTP WhatsApp sebelum akun dibuat.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="enable_register_otp" id="enable_register_otp" value="1" {{ $enableRegisterOtp ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 dark:after:border-slate-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <!-- OTP Template -->
                    <div class="space-y-1.5">
                        <label for="otp_template" class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Format Template Pesan OTP</label>
                        <textarea name="otp_template" id="otp_template" rows="3" required class="input-text text-xs font-medium">{{ old('otp_template', $otpTemplate) }}</textarea>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Gunakan placeholder <code>{otp}</code> untuk disisipkan kode OTP secara dinamis.</p>
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
            <div class="app-card p-5 bg-white dark:bg-[#111A2E] space-y-3 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <span class="font-bold text-xs text-navy">Status Perangkat Bot</span>
                    <i data-lucide="bot" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                </div>

                @if($botDevice)
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Nama:</span>
                            <strong class="text-slate-900 dark:text-white">{{ $botDevice->name }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Nomor:</span>
                            <strong class="text-slate-900 dark:text-white">+{{ $botDevice->phone_number }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-slate-400">Status:</span>
                            <span class="app-tag text-[9px] {{ $botDevice->status === 'connected' ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                {{ strtoupper($botDevice->status) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-medium py-1">
                        Belum ada perangkat yang ditetapkan sebagai Bot Server OTP.
                    </p>
                @endif
            </div>

            <!-- Test OTP Sender Box -->
            <div class="app-card p-5 bg-gradient-to-br from-blue-50/70 to-indigo-50/40 dark:from-blue-500/10 dark:to-indigo-500/5 border-blue-100 dark:border-blue-500/20 space-y-3.5 shadow-2xs">
                <div class="flex items-center gap-2 font-bold text-xs text-navy">
                    <i data-lucide="send" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    <span>Uji Kirim Pesan OTP</span>
                </div>

                <form method="POST" action="{{ route('admin.bot-server.test-otp') }}" class="space-y-3">
                    @csrf

                    <div class="space-y-1">
                        <label for="test_phone" class="block font-bold text-[11px] uppercase tracking-wider text-slate-600 dark:text-slate-400">Nomor Tujuan HP</label>
                        <input type="text" name="phone" id="test_phone" required placeholder="08123456789 atau 628123456789" class="input-text text-xs font-mono font-bold">
                    </div>

                    <div class="space-y-1">
                        <label for="custom_code" class="block font-bold text-[11px] uppercase tracking-wider text-slate-600 dark:text-slate-400">Kode OTP Uji Coba (Opsional)</label>
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
