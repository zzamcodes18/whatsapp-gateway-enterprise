@extends('layouts.app')

@section('title', 'Profil & Keamanan Akun')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto pb-12">
    
    <!-- ================= TOP HERO PROFILE BANNER ================= -->
    <div class="app-card p-6 bg-white dark:bg-[#111A2E] overflow-hidden relative">
        <!-- Accent Glow background -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            
            <!-- User Avatar & Identity -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                
                <!-- Avatar Circle with Interactive Direct Upload Form -->
                <div x-data="{ uploading: false, imgError: false }" class="relative group flex-shrink-0">
                    <form x-ref="avatarForm" action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="file" x-ref="avatarInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" 
                               @change="if($event.target.files.length) { uploading = true; $refs.avatarForm.submit(); }">
                        <input type="hidden" name="remove_avatar" id="direct_remove_avatar" value="0">
                    </form>

                    <div class="relative cursor-pointer" @click="$refs.avatarInput.click()">
                        @if($user->avatar)
                            <img x-show="!imgError" src="{{ $user->avatar }}" x-on:error="imgError = true" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover shadow-md border-2 border-white ring-2 ring-blue-100 group-hover:brightness-90 transition-all">
                            <div x-show="imgError" style="display: none;" class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-2xl flex items-center justify-center font-extrabold text-2xl uppercase shadow-md border-2 border-white ring-2 ring-blue-100 group-hover:brightness-90 transition-all">
                                {{ substr($user->name ?? 'U', 0, 2) }}
                            </div>
                        @else
                            <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-2xl flex items-center justify-center font-extrabold text-2xl uppercase shadow-md border-2 border-white ring-2 ring-blue-100 group-hover:brightness-90 transition-all">
                                {{ substr($user->name ?? 'U', 0, 2) }}
                            </div>
                        @endif

                        <!-- Camera Edit Icon Badge -->
                        <button type="button" @click.stop="$refs.avatarInput.click()" class="absolute -bottom-1 -right-1 w-7 h-7 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-md hover:bg-blue-700 hover:scale-110 transition-all cursor-pointer" title="Ubah Foto Profil">
                            <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                        </button>

                        @if($user->avatar)
                            <!-- Quick Delete Avatar Badge -->
                            <button type="button" @click.stop="if(confirm('Hapus foto profil saat ini?')) { document.getElementById('direct_remove_avatar').value = '1'; $refs.avatarForm.submit(); }" class="absolute -top-1 -right-1 w-6 h-6 bg-rose-600 text-white rounded-lg flex items-center justify-center shadow-md hover:bg-rose-700 hover:scale-110 transition-all opacity-0 group-hover:opacity-100 cursor-pointer" title="Hapus Foto Profil">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- User Information Text -->
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $user->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $user->isAdmin() ? 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20' : 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' }}">
                            {{ $user->role }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            ID #{{ $user->id }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center justify-center sm:justify-start gap-2">
                        <span class="flex items-center gap-1">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                            <span>{{ $user->email }}</span>
                        </span>
                        @if($user->phone_number)
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                                <span>+{{ $user->phone_number }}</span>
                            </span>
                        @endif
                    </p>

                    <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-3 text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                        <span>Bergabung: <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</strong></span>
                        <span>•</span>
                        <span>Login Terakhir: <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Baru saja' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Quick Telemetry Badges -->
            <div class="w-full md:w-auto grid grid-cols-2 gap-3 flex-shrink-0 text-xs">
                <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Device Terhubung</span>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
                        <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $user->devices_count ?? 0 }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">/ {{ $user->device_limit ? $user->device_limit . ' dev' : '∞' }}</span>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pesan Hari Ini</span>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="send" class="w-4 h-4 text-emerald-600"></i>
                        <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $user->messages_sent_today ?? 0 }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">/ {{ $user->daily_message_limit ? $user->daily_message_limit : '∞' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MAIN SETTINGS SECTIONS GRID ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. INFORMASI PROFIL FORM -->
        <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Informasi Diri & Kontak</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Perbarui nama lengkap, email, dan kontak WhatsApp Anda.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update-information') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <span>Nama Lengkap</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="input-text text-xs" placeholder="Masukkan nama lengkap Anda">
                    @error('name')
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <span>Alamat Email</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="input-text text-xs" placeholder="email@domain.com">
                    @error('email')
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor WhatsApp / Telepon -->
                <div class="space-y-1.5">
                    <label for="phone_number" class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>Nomor WhatsApp / Telepon</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400 dark:text-slate-500 select-none">+</span>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="input-text text-xs pl-7" placeholder="6281234567890 (Contoh format internasional tanpa tanda +)">
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Digunakan untuk notifikasi sistem dan autentikasi WhatsApp.</p>
                    @error('phone_number')
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-4 cursor-pointer">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        <span>Simpan Informasi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. KEAMANAN & GANTI PASSWORD -->
        <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Ubah Kata Sandi (Password)</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Pastikan kata sandi baru Anda rumit dan tidak dipakai di tempat lain.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-4" x-data="{ showPass: false }">
                @csrf
                @method('PUT')

                <!-- Password Saat Ini -->
                <div class="space-y-1.5">
                    <label for="current_password" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <span>Password Saat Ini</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" id="current_password" name="current_password" required class="input-text text-xs pr-9" placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white">
                            <i data-lucide="eye" class="w-3.5 h-3.5" x-show="!showPass"></i>
                            <i data-lucide="eye-off" class="w-3.5 h-3.5" x-show="showPass"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <span>Password Baru</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" required class="input-text text-xs" placeholder="Minimal 8 karakter (kombinasi huruf besar, kecil & angka)">
                    @error('password')
                        <p class="text-[11px] text-rose-600 font-medium mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <span>Konfirmasi Password Baru</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input :type="showPass ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required class="input-text text-xs" placeholder="Ulangi password baru">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-4 cursor-pointer">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        <span>Perbarui Password</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. TELEMETRI & STATISTIK KUOTA AKUN -->
        <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Batas Kuota & Spesifikasi Akun</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Informasi kapasitas harian dan limit akun Anda saat ini.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4 text-xs">
                
                <!-- Limit Pesan Harian Progress -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700 dark:text-slate-300">Kuota Pesan Harian</span>
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400">
                            {{ $user->messages_sent_today }} / {{ $user->daily_message_limit ? $user->daily_message_limit : 'Unlimited' }}
                        </span>
                    </div>

                    @php
                        $dailyLimit = $user->daily_message_limit ?? 0;
                        $percent = $dailyLimit > 0 ? min(100, round(($user->messages_sent_today / $dailyLimit) * 100)) : 0;
                    @endphp
                    
                    @if($dailyLimit > 0)
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percent }}%;"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Batas kuota di-reset otomatis setiap hari pada pukul 00:00 WIB.</p>
                    @else
                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            <span>Akun Anda memiliki akses kirim pesan tanpa batas (Unlimited).</span>
                        </div>
                    @endif
                </div>

                <!-- Limit Perangkat WhatsApp -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-300 block">Kapasitas Perangkat (Devices)</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Jumlah nomor WA yang dapat dihubungkan bersamaan.</span>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-sm text-slate-900 dark:text-white block">{{ $user->devices_count ?? 0 }} Device</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Batas: {{ $user->device_limit ? $user->device_limit : 'Unlimited' }}</span>
                    </div>
                </div>

                <!-- Detail Login Terakhir & Keamanan IP -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl space-y-1 font-mono text-[11px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Alamat IP Terakhir:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->last_login_ip ?? '127.0.0.1' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Waktu Login Terakhir:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i:s') : 'Baru saja' }}</span>
                    </div>
                </div>

                <!-- Support Note -->
                <div class="p-3 bg-blue-50/60 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl flex items-start gap-2.5 text-[11px] text-blue-900 dark:text-blue-300 font-medium">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                    <span>Membutuhkan peningkatan limit pesan atau kuota device? Hubungi administrator atau tim dukungan kami.</span>
                </div>

            </div>
        </div>

        <!-- 4. HUBUNGKAN AKUN (GOOGLE & GITHUB) -->
        @php
            $enableGoogle = \App\Models\SystemSetting::get('enable_google_login', 'false') === 'true';
            $enableGithub = \App\Models\SystemSetting::get('enable_github_login', 'false') === 'true';
        @endphp

        <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center font-bold shadow-2xs">
                        <i data-lucide="link-2" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Akun Terhubung (OAuth SSO)</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Tautkan akun Google atau GitHub Anda untuk akses login cepat dan praktis.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3.5">
                <!-- Google Card -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center font-bold text-sm shadow-2xs">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                                <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                                <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12.3 0 15s.7 5.3 1.9 7.7l3.7-2.9z"/>
                                <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16c1.8 3.7 5.6 7 10.1 7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Google Account</h4>
                            @if($user->google_id)
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> Terhubung
                                </p>
                            @else
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Belum Terhubung</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        @if($user->google_id)
                            <form action="{{ route('profile.unlink-social', 'google') }}" method="POST" onsubmit="return confirm('Lepaskan tautan akun Google Anda?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition-colors cursor-pointer">
                                    Lepas Tautan
                                </button>
                            </form>
                        @else
                            @if($enableGoogle)
                                <a href="{{ route('auth.google') }}" class="px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-lg transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                    <span>Hubungkan</span>
                                </a>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 rounded-lg">Nonaktif</span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- GitHub Card -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">GitHub Account</h4>
                            @if($user->github_id)
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> Terhubung
                                </p>
                            @else
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Belum Terhubung</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        @if($user->github_id)
                            <form action="{{ route('profile.unlink-social', 'github') }}" method="POST" onsubmit="return confirm('Lepaskan tautan akun GitHub Anda?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 rounded-lg transition-colors cursor-pointer">
                                    Lepas Tautan
                                </button>
                            </form>
                        @else
                            @if($enableGithub)
                                <a href="{{ route('auth.github') }}" class="px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-lg transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                    <span>Hubungkan</span>
                                </a>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 rounded-lg">Nonaktif</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
