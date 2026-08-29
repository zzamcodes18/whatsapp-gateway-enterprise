@extends('layouts.app')

@section('title', 'Pengaturan System & Website')

@section('content')
<div class="space-y-6" x-data="{ tab: 'general' }">

    <!-- Top Header Title & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i data-lucide="settings" class="w-6 h-6 text-blue-600"></i>
                <span>Pengaturan Website & System</span>
            </h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                Kelola identitas website, logo/icon branding, limit pendaftar baru, serta konfigurasi engine backend.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" form="settings-form" class="app-btn app-btn-primary text-xs py-2.5 px-4 flex items-center gap-2 shadow-sm">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- Main Card Container with Tabs -->
    <div class="app-card bg-white shadow-xs overflow-hidden border border-slate-200/80">
        
        <!-- Tab Navigation Buttons Header -->
        <div class="border-b border-slate-200 bg-slate-50/70 px-4 sm:px-6 pt-3 flex items-center gap-2 overflow-x-auto scrollbar-none">
            <button @click="tab = 'general'" type="button" 
                :class="tab === 'general' ? 'border-blue-600 text-blue-600 font-bold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="globe" class="w-4 h-4"></i>
                <span>Identitas & SEO</span>
            </button>

            <button @click="tab = 'branding'" type="button" 
                :class="tab === 'branding' ? 'border-blue-600 text-blue-600 font-bold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Logo & Branding</span>
            </button>

            <button @click="tab = 'registration'" type="button" 
                :class="tab === 'registration' ? 'border-blue-600 text-blue-600 font-bold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Limit & Pendaftaran</span>
            </button>

            <button @click="tab = 'engine'" type="button" 
                :class="tab === 'engine' ? 'border-blue-600 text-blue-600 font-bold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="cpu" class="w-4 h-4"></i>
                <span>Engine & Microservice</span>
            </button>
        </div>

        <!-- Setting Form Content -->
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
            @csrf

            <!-- TAB 1: IDENTITAS & SEO WEBSITE -->
            <div x-show="tab === 'general'" class="space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Informasi & Meta Data Website</h3>
                    <p class="text-xs text-slate-500">Sesuaikan nama aplikasi, deskripsi SEO, dan kontak bantuan pengguna.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="site_name" class="app-label">Nama Website / Platform</label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Whatsapp Gateway Enterprise') }}" class="app-input" placeholder="Whatsapp Gateway Enterprise">
                    </div>

                    <div class="space-y-1.5">
                        <label for="support_email" class="app-label">Email Support / Admin</label>
                        <input type="email" id="support_email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? 'support@wagateway.com') }}" class="app-input" placeholder="admin@domain.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="support_whatsapp" class="app-label">Nomor WhatsApp Customer Service</label>
                        <input type="text" id="support_whatsapp" name="support_whatsapp" value="{{ old('support_whatsapp', $settings['support_whatsapp'] ?? '') }}" class="app-input" placeholder="628123456789">
                        <p class="text-[11px] text-slate-400">Format nomor menggunakan kode negara tanpa tanda plus (+), contoh: 628123456789.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="site_keywords" class="app-label">SEO Keywords</label>
                        <input type="text" id="site_keywords" name="site_keywords" value="{{ old('site_keywords', $settings['site_keywords'] ?? 'whatsapp gateway, wa api, whatsapp otp, baileys, REST API') }}" class="app-input" placeholder="whatsapp gateway, wa api, REST API">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="site_description" class="app-label">Deskripsi Website (Meta Description)</label>
                    <textarea id="site_description" name="site_description" rows="3" class="app-input" placeholder="Deskripsi singkat aplikasi untuk tampilan SEO Google & Open Graph...">{{ old('site_description', $settings['site_description'] ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Baileys Core. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.') }}</textarea>
                </div>
            </div>

            <!-- TAB 2: BRANDING & MEDIA (LOGO & FAVICON) -->
            <div x-show="tab === 'branding'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Logo & Favicon Website</h3>
                    <p class="text-xs text-slate-500">Unggah logo dan favicon khusus untuk mengubah tampilan branding aplikasi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Upload Logo Utama -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-800">Logo Utama Application</h4>
                                <p class="text-[11px] text-slate-500">Format PNG, SVG, JPG, WEBP (Maks 2MB)</p>
                            </div>
                            <i data-lucide="image" class="w-5 h-5 text-blue-600"></i>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl border border-slate-200 bg-white p-2 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                                @if(!empty($settings['site_logo']))
                                    <img src="{{ asset($settings['site_logo']) }}" alt="Logo Saat Ini" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-xs">
                                        WA
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <label for="site_logo" class="app-label">Pilih File Logo Baru</label>
                                <input type="file" id="site_logo" name="site_logo" accept="image/*" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Upload Favicon -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-800">Icon Browser (Favicon)</h4>
                                <p class="text-[11px] text-slate-500">Format ICO, PNG, SVG (Maks 1MB)</p>
                            </div>
                            <i data-lucide="bookmark" class="w-5 h-5 text-indigo-600"></i>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl border border-slate-200 bg-white p-2 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                                @if(!empty($settings['site_favicon']))
                                    <img src="{{ asset($settings['site_favicon']) }}" alt="Favicon Saat Ini" class="w-8 h-8 object-contain">
                                @else
                                    <i data-lucide="message-square" class="w-7 h-7 text-blue-600"></i>
                                @endif
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <label for="site_favicon" class="app-label">Pilih File Favicon Baru</label>
                                <input type="file" id="site_favicon" name="site_favicon" accept="image/*,.ico" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- TAB 3: LIMIT & PENDAFTARAN USER BARU -->
            <div x-show="tab === 'registration'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Kebijakan Registrasi & Limit Default</h3>
                    <p class="text-xs text-slate-500">Atur status pendaftaran akun publik dan jatah limit awal bagi pengguna baru.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <div class="space-y-1.5">
                        <label for="allow_registration" class="app-label">Status Pendaftaran User Baru</label>
                        <select id="allow_registration" name="allow_registration" class="app-input">
                            <option value="true" {{ old('allow_registration', $settings['allow_registration'] ?? 'true') === 'true' ? 'selected' : '' }}>✓ Diizinkan (Pendaftaran Terbuka)</option>
                            <option value="false" {{ old('allow_registration', $settings['allow_registration'] ?? 'true') === 'false' ? 'selected' : '' }}>✕ Ditutup (Hanya Admin yang Bisa Tambah User)</option>
                        </select>
                        <p class="text-[11px] text-slate-400">Jika ditutup, halaman register tidak dapat diakses publik.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="default_device_limit" class="app-label">Default Limit Device / Perangkat</label>
                        <input type="number" min="0" id="default_device_limit" name="default_device_limit" value="{{ old('default_device_limit', $settings['default_device_limit'] ?? 3) }}" class="app-input" placeholder="3">
                        <p class="text-[11px] text-slate-400">Jumlah perangkat WA yang boleh ditambahkan user baru. Isikan <strong>0 untuk Unlimited</strong>.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="default_daily_message_limit" class="app-label">Default Kuota Pesan Harian</label>
                        <input type="number" min="0" id="default_daily_message_limit" name="default_daily_message_limit" value="{{ old('default_daily_message_limit', $settings['default_daily_message_limit'] ?? 500) }}" class="app-input" placeholder="500">
                        <p class="text-[11px] text-slate-400">Batas pesan per hari untuk user baru. Isikan <strong>0 untuk Unlimited</strong>.</p>
                    </div>

                </div>
            </div>

            <!-- TAB 4: ENGINE & MICROSERVICE -->
            <div x-show="tab === 'engine'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">Integrasi Microservice Baileys Node.js</h3>
                    <p class="text-xs text-slate-500">Pengaturan alamat internal microservice WhatsApp engine (port 3000).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="wa_engine_url" class="app-label">URL Backend Engine</label>
                        <input type="text" id="wa_engine_url" name="wa_engine_url" value="{{ old('wa_engine_url', $settings['wa_engine_url'] ?? 'http://127.0.0.1:3000') }}" class="app-input font-mono text-xs" placeholder="http://127.0.0.1:3000">
                        <p class="text-[11px] text-slate-400">Alamat internal server Node.js Baileys yang berjalan pada port 3000.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="wa_engine_secret" class="app-label">Engine API Secret Key</label>
                        <input type="password" id="wa_engine_secret" name="wa_engine_secret" value="{{ old('wa_engine_secret', $settings['wa_engine_secret'] ?? 'wagateway_secret_key_2026') }}" class="app-input font-mono text-xs" placeholder="wagateway_secret_key_2026">
                        <p class="text-[11px] text-slate-400">Secret key validasi autentikasi internal antara Laravel dan Node.js.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit" class="app-btn app-btn-primary text-xs py-2.5 px-5 flex items-center gap-2 shadow-sm">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Simpan Semua Konfigurasi</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
