@extends('layouts.app')

@section('title', 'Pengaturan System & Website')

@section('content')
<div class="space-y-6" x-data="{ tab: 'website' }">

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
            <button @click="tab = 'website'" type="button" 
                :class="tab === 'website' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="globe" class="w-4 h-4"></i>
                <span>Website & Branding</span>
            </button>

            <button @click="tab = 'users'" type="button" 
                :class="tab === 'users' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Pengguna & SSO</span>
            </button>

            <button @click="tab = 'server'" type="button" 
                :class="tab === 'server' ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold bg-white dark:bg-[#111A2E] shadow-2xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/70 font-semibold'"
                class="px-4 py-2.5 border-b-2 rounded-t-xl text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer">
                <i data-lucide="server" class="w-4 h-4"></i>
                <span>Server & Integrasi</span>
            </button>
        </div>

        <!-- Setting Form Content -->
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
            @csrf

            <!-- SECTION 1: IDENTITAS & SEO WEBSITE (Tab: Website & Branding) -->
            <div x-show="tab === 'website'" class="space-y-5">
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

            <!-- SECTION 2: BRANDING & MEDIA (Tab: Website & Branding) -->
            <div x-show="tab === 'website'" class="space-y-5 pt-2">
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

            <!-- SECTION 3: LIMIT & PENDAFTARAN (Tab: Pengguna & SSO) -->
            <div x-show="tab === 'users'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Kebijakan Registrasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur status pendaftaran akun publik. Limit user baru otomatis mengikuti paket default (Free).</p>
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

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 md:col-span-2 flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0"></i>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Limit device & kuota pesan user baru sekarang ditentukan oleh <strong class="text-slate-700 dark:text-slate-300">paket default (Free)</strong> dari menu <strong class="text-slate-700 dark:text-slate-300">Manajemen Paket</strong>. Ubah limit di sana jika ingin menyesuaikan kuota pendaftar baru.
                        </p>
                    </div>

                </div>
            </div>

            <!-- SECTION 4: SMTP MAIL SERVER (Tab: Server & Integrasi) -->
            <div x-show="tab === 'server'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Konfigurasi SMTP Mail Server</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pengaturan server email SMTP untuk pengiriman kode OTP, notifikasi, dan reset password via email.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label for="smtp_host" class="app-label">SMTP Host Server</label>
                        <input type="text" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}" class="app-input font-mono text-xs" placeholder="smtp.gmail.com atau smtp.mailtrap.io">
                    </div>

                    <div class="space-y-1.5">
                        <label for="smtp_port" class="app-label">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}" class="app-input font-mono text-xs" placeholder="587 / 465 / 2525">
                    </div>

                    <div class="space-y-1.5">
                        <label for="smtp_encryption" class="app-label">Enkripsi (Encryption)</label>
                        <select id="smtp_encryption" name="smtp_encryption" class="app-input text-xs cursor-pointer">
                            <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                            <option value="ssl" {{ ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="null" {{ ($settings['smtp_encryption'] ?? '') === 'null' ? 'selected' : '' }}>Tanpa Enkripsi (None)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="smtp_username" class="app-label">SMTP Username / Email</label>
                        <input type="text" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}" class="app-input font-mono text-xs" placeholder="user@domain.com">
                    </div>

                    <div class="space-y-1.5">
                        <label for="smtp_password" class="app-label">SMTP Password / App Password</label>
                        <input type="password" id="smtp_password" name="smtp_password" value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}" class="app-input font-mono text-xs" placeholder="••••••••••••">
                    </div>

                    <div class="space-y-1.5">
                        <label for="smtp_from_address" class="app-label">Pengirim (From Email)</label>
                        <input type="email" id="smtp_from_address" name="smtp_from_address" value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? '') }}" class="app-input text-xs" placeholder="noreply@domain.com">
                    </div>

                    <div class="space-y-1.5 md:col-span-3">
                        <label for="smtp_from_name" class="app-label">Nama Pengirim (From Name)</label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? '') }}" class="app-input text-xs" placeholder="WhatsApp Gateway Enterprise">
                    </div>
                </div>

                <!-- TEST KONEKSI SMTP -->
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                    <div class="bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl p-4">
                        <h4 class="font-semibold text-xs text-blue-900 dark:text-blue-300 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Uji Koneksi (Test Email SMTP)
                        </h4>
                        <p class="text-[11px] text-blue-700 dark:text-blue-400 mb-3">Simpan konfigurasi terlebih dahulu jika Anda baru saja mengubah data SMTP, lalu kirim email uji coba ke alamat email Anda.</p>
                        
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <input type="email" 
                                   form="form-test-smtp" 
                                   name="test_email" 
                                   value="{{ old('test_email', auth()->user()->email) }}" 
                                   class="app-input text-xs flex-1 bg-white dark:bg-slate-900" 
                                   placeholder="email-anda@gmail.com" 
                                   required>
                            <button type="submit" 
                                    form="form-test-smtp" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg font-medium text-xs transition-colors flex items-center justify-center gap-1.5 shadow-xs shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Kirim Email Uji Coba
                            </button>
                        </div>
                        @error('test_email')
                            <p class="mt-2 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 5: ENGINE & MICROSERVICE (Tab: Server & Integrasi) -->
            <div x-show="tab === 'server'" class="space-y-5" style="display: none;">
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

            <!-- SECTION 7: CLOUDFLARE TURNSTILE (Tab: Server & Integrasi) -->
            <div x-show="tab === 'server'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Cloudflare Turnstile (Captcha)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Proteksi anti-bot untuk halaman login menggunakan Cloudflare Turnstile.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div x-data="{ enableTs: {{ old('enable_turnstile', $settings['enable_turnstile'] ?? 'false') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                        <label class="app-label">Status Turnstile</label>
                        <input type="hidden" name="enable_turnstile" :value="enableTs ? 'true' : 'false'">
                        
                        <div class="flex items-center gap-3 pt-1">
                            <button type="button" 
                                    @click="enableTs = !enableTs" 
                                    :class="enableTs ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                <span :class="enableTs ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <span class="text-xs font-bold transition-colors" :class="enableTs ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="enableTs ? '✓ Aktif' : '✕ Nonaktif'"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Widget captcha hanya muncul di halaman login.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="turnstile_site_key" class="app-label">Site Key</label>
                        <input type="text" id="turnstile_site_key" name="turnstile_site_key" value="{{ old('turnstile_site_key', $settings['turnstile_site_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="0x4AAAAAAA...">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Public key dari Cloudflare Dashboard &rarr; Turnstile.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="turnstile_secret_key" class="app-label">Secret Key</label>
                        <input type="password" id="turnstile_secret_key" name="turnstile_secret_key" value="{{ old('turnstile_secret_key', $settings['turnstile_secret_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="0x4AAAAAAA...">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Private key untuk verifikasi server-side.</p>
                    </div>
                </div>

                <div class="p-3 bg-white dark:bg-[#0D1526] rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Cara mendapatkan key:</p>
                    <ol class="list-decimal ml-4 space-y-0.5 text-[11px]">
                        <li>Buka <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Cloudflare Dashboard &rarr; Turnstile</a></li>
                        <li>Klik <strong>Add site</strong> & masukkan domain aplikasi Anda</li>
                        <li>Salin <strong>Site Key</strong> & <strong>Secret Key</strong> ke kolom di samping</li>
                    </ol>
                </div>
            </div>

            <!-- SECTION 8: RECAPTCHA V3 (Tab: Server & Integrasi) -->
            <div x-show="tab === 'server'" class="space-y-5" style="display: none;">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Google reCAPTCHA v3 (Invisible)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Proteksi anti-bot invisible di semua halaman auth (login, register, lupa password, verifikasi OTP). User tidak melihat apa pun.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div x-data="{ enableRc: {{ old('enable_recaptcha', $settings['enable_recaptcha'] ?? 'false') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                        <label class="app-label">Status reCAPTCHA v3</label>
                        <input type="hidden" name="enable_recaptcha" :value="enableRc ? 'true' : 'false'">
                        
                        <div class="flex items-center gap-3 pt-1">
                            <button type="button" 
                                    @click="enableRc = !enableRc" 
                                    :class="enableRc ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                <span :class="enableRc ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <span class="text-xs font-bold transition-colors" :class="enableRc ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="enableRc ? '✓ Aktif' : '✕ Nonaktif'"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Berjalan invisible di background semua form auth.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="recaptcha_site_key" class="app-label">Site Key (v3)</label>
                        <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $settings['recaptcha_site_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="6Lc...">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Public key dari Google reCAPTCHA Admin.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="recaptcha_secret_key" class="app-label">Secret Key (v3)</label>
                        <input type="password" id="recaptcha_secret_key" name="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', $settings['recaptcha_secret_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="6Lc...">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Private key untuk verifikasi server-side.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="recaptcha_min_score" class="app-label">Minimum Score (0.0 – 1.0)</label>
                        <input type="number" step="0.1" min="0" max="1" id="recaptcha_min_score" name="recaptcha_min_score" value="{{ old('recaptcha_min_score', $settings['recaptcha_min_score'] ?? '0.5') }}" class="app-input font-mono text-xs" placeholder="0.5">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Ambang skor risiko. Rekomendasi: <strong>0.5</strong> (seimbang). Naikkan ke 0.7 untuk keamanan ketat, turunkan ke 0.3 jika banyak false positive.</p>
                    </div>
                </div>

                <div class="p-3 bg-white dark:bg-[#0D1526] rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Cara mendapatkan key:</p>
                    <ol class="list-decimal ml-4 space-y-0.5 text-[11px]">
                        <li>Buka <a href="https://www.google.com/recaptcha/admin/create" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Google reCAPTCHA Admin</a></li>
                        <li>Pilih tipe <strong>reCAPTCHA v3</strong> & masukkan domain aplikasi Anda</li>
                        <li>Salin <strong>Site Key</strong> & <strong>Secret Key</strong> ke kolom di samping</li>
                    </ol>
                </div>

                <!-- hCaptcha (Pop-up Challenge) -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">hCaptcha (Pop-up Captcha Developer Actions)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Proteksi pop-up captcha interaktif saat pengguna melakukan aksi sensitif seperti membuat (generate) atau menghentikan (revoke) API Key.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div x-data="{ enableHc: {{ old('enable_hcaptcha', $settings['enable_hcaptcha'] ?? 'false') === 'true' ? 'true' : 'false' }} }" class="space-y-1.5">
                            <label class="app-label">Status hCaptcha</label>
                            <input type="hidden" name="enable_hcaptcha" :value="enableHc ? 'true' : 'false'">
                            
                            <div class="flex items-center gap-3 pt-1">
                                <button type="button" 
                                        @click="enableHc = !enableHc" 
                                        :class="enableHc ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none shadow-2xs">
                                    <span :class="enableHc ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span class="text-xs font-bold transition-colors" :class="enableHc ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="enableHc ? '✓ Aktif' : '✕ Nonaktif'"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Menampilkan pop-up captcha pada modal Generate & Revoke API Key.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="hcaptcha_site_key" class="app-label">Site Key</label>
                            <input type="text" id="hcaptcha_site_key" name="hcaptcha_site_key" value="{{ old('hcaptcha_site_key', $settings['hcaptcha_site_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="10000000-ffff-ffff-ffff-111111111111">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Public Site Key dari Dashboard hCaptcha.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label for="hcaptcha_secret_key" class="app-label">Secret Key</label>
                            <input type="password" id="hcaptcha_secret_key" name="hcaptcha_secret_key" value="{{ old('hcaptcha_secret_key', $settings['hcaptcha_secret_key'] ?? '') }}" class="app-input font-mono text-xs" placeholder="0x00000000000000...">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Secret Key untuk verifikasi server-side.</p>
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-[#0D1526] rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-400 space-y-1">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Cara mendapatkan key:</p>
                        <ol class="list-decimal ml-4 space-y-0.5 text-[11px]">
                            <li>Buka <a href="https://dashboard.hcaptcha.com/" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Dashboard hCaptcha</a></li>
                            <li>Tambahkan New Site & masukkan domain aplikasi Anda</li>
                            <li>Salin <strong>Site Key</strong> & <strong>Secret Key</strong> ke kolom di atas</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: OAUTH & SINGLE SIGN-ON (Tab: Pengguna & SSO) -->
            <div x-show="tab === 'users'" class="space-y-6" style="display: none;">
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

        <!-- Hidden Form for SMTP Test Connection -->
        <form id="form-test-smtp" action="{{ route('admin.settings.test-smtp') }}" method="POST" class="hidden">
            @csrf
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
