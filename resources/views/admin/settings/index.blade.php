@extends('layouts.app')

@section('title', 'Pengaturan System & Website')

@section('content')
<div class="space-y-6" x-data="{ tab: 'general' }">

    <!-- Top Header Title & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <i data-lucide="settings" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                <span>Pengaturan Website & System</span>
            </h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
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
    <div class="app-card bg-white dark:bg-[#111A2E] shadow-xs overflow-hidden border border-slate-200/80 dark:border-slate-800">
        
        <!-- Tab Navigation Buttons Header -->
        <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 px-4 sm:px-6 pt-3 flex items-center gap-2 overflow-x-auto scrollbar-none">
            <button @click="tab = 'general'" type="button" 
                :class="tab === 'general' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="globe" class="w-4 h-4"></i>
                <span>Identitas & SEO</span>
            </button>

            <button @click="tab = 'branding'" type="button" 
                :class="tab === 'branding' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Logo & Branding</span>
            </button>

            <button @click="tab = 'registration'" type="button" 
                :class="tab === 'registration' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Limit & Pendaftaran</span>
            </button>

            <button @click="tab = 'engine'" type="button" 
                :class="tab === 'engine' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="cpu" class="w-4 h-4"></i>
                <span>Engine & Microservice</span>
            </button>

            <button @click="tab = 'oauth'" type="button" 
                :class="tab === 'oauth' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="share-2" class="w-4 h-4"></i>
                <span>OAuth & SSO Login</span>
            </button>
        </div>

        <!-- Setting Form Content -->
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
            @csrf

            <!-- TAB 1: IDENTITAS & SEO WEBSITE -->
            <div x-show="tab === 'general'" class="space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Informasi & Meta Data Website</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sesuaikan nama aplikasi, deskripsi SEO, dan kontak bantuan pengguna.</p>
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
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Format nomor menggunakan kode negara tanpa tanda plus (+), contoh: 628123456789.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="site_keywords" class="app-label">SEO Keywords</label>
                        <input type="text" id="site_keywords" name="site_keywords" value="{{ old('site_keywords', $settings['site_keywords'] ?? 'whatsapp gateway, wa api, whatsapp otp, REST API') }}" class="app-input" placeholder="whatsapp gateway, wa api, REST API">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="site_description" class="app-label">Deskripsi Website (Meta Description)</label>
                    <textarea id="site_description" name="site_description" rows="3" class="app-input" placeholder="Deskripsi singkat aplikasi untuk tampilan SEO Google & Open Graph...">{{ old('site_description', $settings['site_description'] ?? 'Layanan Whatsapp Gateway Enterprise berkecepatan tinggi dengan Enterprise Engine Core. Mendukung integrasi OTP, notifikasi tagihan, webhook realtime, Scan QR Code, dan Pairing Code 8-digit.') }}</textarea>
                </div>
            </div>

            <!-- TAB 2: BRANDING & MEDIA (LOGO & FAVICON) -->
            <div x-show="tab === 'branding'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Logo & Favicon Website</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Unggah logo dan favicon khusus atau masukkan URL gambar untuk mengubah tampilan branding aplikasi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Upload Logo Utama -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Logo Utama Application</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Upload file logo dari komputer/HP (disimpan langsung ke database)</p>
                            </div>
                            <i data-lucide="image" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0D1526] p-2 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                                @if(!empty($settings['site_logo']))
                                    <img src="{{ (str_starts_with($settings['site_logo'], 'http') || str_starts_with($settings['site_logo'], 'data:')) ? $settings['site_logo'] : asset(ltrim($settings['site_logo'], '/')) }}" alt="Logo Saat Ini" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-xs">
                                        WA
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 space-y-1">
                                <label for="site_logo" class="app-label">Pilih File Logo Baru (Otomatis Kompres)</label>
                                <input type="file" id="site_logo" name="site_logo" accept="image/*" @change="compressImageOnUpload($event, 500)" class="text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-500/10 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Upload Favicon -->
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Icon Browser (Favicon)</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Upload file icon/favicon dari komputer/HP (disimpan langsung ke database)</p>
                            </div>
                            <i data-lucide="bookmark" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0D1526] p-2 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                                @if(!empty($settings['site_favicon']))
                                    <img src="{{ (str_starts_with($settings['site_favicon'], 'http') || str_starts_with($settings['site_favicon'], 'data:')) ? $settings['site_favicon'] : asset(ltrim($settings['site_favicon'], '/')) }}" alt="Favicon Saat Ini" class="w-8 h-8 object-contain">
                                @else
                                    <i data-lucide="message-square" class="w-7 h-7 text-blue-600"></i>
                                @endif
                            </div>

                            <div class="flex-1 space-y-1">
                                <label for="site_favicon" class="app-label">Pilih File Favicon Baru (Otomatis Kompres)</label>
                                <input type="file" id="site_favicon" name="site_favicon" accept="image/*,.ico" @change="compressImageOnUpload($event, 128)" class="text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-500/10 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/20 cursor-pointer">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- TAB 3: LIMIT & PENDAFTARAN USER BARU -->
            <div x-show="tab === 'registration'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Kebijakan Registrasi & Limit Default</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur status pendaftaran akun publik dan jatah limit awal bagi pengguna baru.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <div x-data="{ allowReg: {{ old('allow_registration', $settings['allow_registration'] ?? 'true') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                        <label class="app-label">Status Pendaftaran User Baru</label>
                        <input type="hidden" name="allow_registration" :value="allowReg ? 'true' : 'false'">
                        
                        <div class="flex items-center gap-3 pt-1">
                            <button type="button" 
                                    @click="allowReg = !allowReg" 
                                    :class="allowReg ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                <span :class="allowReg ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <span class="text-xs font-bold transition-colors" :class="allowReg ? 'text-blue-700 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400'" x-text="allowReg ? '✓ Pendaftaran Terbuka' : '✕ Ditutup (Nonaktif)'"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Jika ditutup, halaman register tidak dapat diakses publik.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="default_device_limit" class="app-label">Default Limit Device / Perangkat</label>
                        <input type="number" min="0" id="default_device_limit" name="default_device_limit" value="{{ old('default_device_limit', $settings['default_device_limit'] ?? 3) }}" class="app-input" placeholder="3">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Jumlah perangkat WA yang boleh ditambahkan user baru. Isikan <strong>0 untuk Unlimited</strong>.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="default_daily_message_limit" class="app-label">Default Kuota Pesan Harian</label>
                        <input type="number" min="0" id="default_daily_message_limit" name="default_daily_message_limit" value="{{ old('default_daily_message_limit', $settings['default_daily_message_limit'] ?? 500) }}" class="app-input" placeholder="500">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Batas pesan per hari untuk user baru. Isikan <strong>0 untuk Unlimited</strong>.</p>
                    </div>

                </div>
            </div>

            <!-- TAB 4: ENGINE & MICROSERVICE -->
            <div x-show="tab === 'engine'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Integrasi Microservice Engine Node.js</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pengaturan alamat internal microservice WhatsApp engine (port 3000).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="wa_engine_url" class="app-label">URL Backend Engine</label>
                        <input type="text" id="wa_engine_url" name="wa_engine_url" value="{{ old('wa_engine_url', $settings['wa_engine_url'] ?? 'http://127.0.0.1:3000') }}" class="app-input font-mono text-xs" placeholder="http://127.0.0.1:3000">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Alamat internal server Node.js Engine yang berjalan pada port 3000.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="wa_engine_secret" class="app-label">Engine API Secret Key</label>
                        <input type="password" id="wa_engine_secret" name="wa_engine_secret" value="{{ old('wa_engine_secret', $settings['wa_engine_secret'] ?? 'wagateway_secret_key_2026') }}" class="app-input font-mono text-xs" placeholder="wagateway_secret_key_2026">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Secret key validasi autentikasi internal antara Laravel dan Node.js.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 5: OAUTH & SINGLE SIGN-ON (GOOGLE & GITHUB) -->
            <div x-show="tab === 'oauth'" class="space-y-6" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Pengaturan Single Sign-On (SSO)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aktifkan atau nonaktifkan fitur SSO Google dan GitHub untuk pengguna.</p>
                </div>

                <!-- Google Auth Settings -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center font-bold text-xs">
                                G
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Google OAuth 2.0</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Login & Register menggunakan akun Google</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div x-data="{ enableGoogle: {{ old('enable_google_login', $settings['enable_google_login'] ?? 'false') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                            <label class="app-label">Status Google Login</label>
                            <input type="hidden" name="enable_google_login" :value="enableGoogle ? 'true' : 'false'">
                            
                            <div class="flex items-center gap-3 pt-1">
                                <button type="button" 
                                        @click="enableGoogle = !enableGoogle" 
                                        :class="enableGoogle ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                    <span :class="enableGoogle ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span class="text-xs font-bold transition-colors" :class="enableGoogle ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="enableGoogle ? '✓ Aktif' : '✕ Nonaktif'"></span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="google_client_id" class="app-label">Google Client ID</label>
                            <input type="text" id="google_client_id" name="google_client_id" value="{{ old('google_client_id', $settings['google_client_id'] ?? '') }}" class="app-input font-mono text-xs" placeholder="xxxxxx.apps.googleusercontent.com">
                        </div>

                        <div class="space-y-1.5">
                            <label for="google_client_secret" class="app-label">Google Client Secret</label>
                            <input type="password" id="google_client_secret" name="google_client_secret" value="{{ old('google_client_secret', $settings['google_client_secret'] ?? '') }}" class="app-input font-mono text-xs" placeholder="GOCSPX-xxxxxx">
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-[#0D1526] rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Authorized Redirect URI (Google Cloud Console):</p>
                        <code class="block font-mono bg-slate-100 dark:bg-slate-800 p-2 rounded text-[11px] text-blue-700 dark:text-blue-400 select-all">{{ url('/auth/google/callback') }}</code>
                    </div>
                </div>

                <!-- GitHub Auth Settings -->
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                                GH
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">GitHub OAuth App</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Login & Register menggunakan akun GitHub</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div x-data="{ enableGithub: {{ old('enable_github_login', $settings['enable_github_login'] ?? 'false') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                            <label class="app-label">Status GitHub Login</label>
                            <input type="hidden" name="enable_github_login" :value="enableGithub ? 'true' : 'false'">
                            
                            <div class="flex items-center gap-3 pt-1">
                                <button type="button" 
                                        @click="enableGithub = !enableGithub" 
                                        :class="enableGithub ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                    <span :class="enableGithub ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span class="text-xs font-bold transition-colors" :class="enableGithub ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="enableGithub ? '✓ Aktif' : '✕ Nonaktif'"></span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="github_client_id" class="app-label">GitHub Client ID</label>
                            <input type="text" id="github_client_id" name="github_client_id" value="{{ old('github_client_id', $settings['github_client_id'] ?? '') }}" class="app-input font-mono text-xs" placeholder="Iv1.xxxxxx">
                        </div>

                        <div class="space-y-1.5">
                            <label for="github_client_secret" class="app-label">GitHub Client Secret</label>
                            <input type="password" id="github_client_secret" name="github_client_secret" value="{{ old('github_client_secret', $settings['github_client_secret'] ?? '') }}" class="app-input font-mono text-xs" placeholder="xxxxxx">
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-[#0D1526] rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Authorization Callback URL (GitHub Developer Settings):</p>
                        <code class="block font-mono bg-slate-100 dark:bg-slate-800 p-2 rounded text-[11px] text-blue-700 dark:text-blue-400 select-all">{{ url('/auth/github/callback') }}</code>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="submit" class="app-btn app-btn-primary text-xs py-2.5 px-5 flex items-center gap-2 shadow-sm">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Simpan Semua Konfigurasi</span>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function compressImageOnUpload(event, maxDimension) {
    const input = event.target;
    const file = input.files[0];
    if (!file || file.type === 'image/svg+xml' || file.name.endsWith('.ico')) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            let width = img.width;
            let height = img.height;

            if (width > maxDimension || height > maxDimension) {
                if (width > height) {
                    height = Math.round((height * maxDimension) / width);
                    width = maxDimension;
                } else {
                    width = Math.round((width * maxDimension) / height);
                    height = maxDimension;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob(function(blob) {
                if (blob) {
                    const resizedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    const container = new DataTransfer();
                    container.items.add(resizedFile);
                    input.files = container.files;
                }
            }, 'image/jpeg', 0.85);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
