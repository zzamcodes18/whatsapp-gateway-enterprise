@extends('layouts.app')

@section('title', 'Developer Credentials & Webhooks')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: (new URLSearchParams(window.location.search)).get('tab') === 'webhooks' ? 'webhooks' : 'api-keys',
    showNewModal: false,
    showRevokeModal: false,
    revokeKeyName: '',
    revokeActionUrl: ''
}">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">DEVELOPER GATEWAY</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">API KEYS & WEBHOOKS</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-slate-900 dark:text-white">Integrasi & Callback</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola kunci akses REST API dan URL callback Webhook realtime dalam satu tempat.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <button x-show="activeTab === 'api-keys'" @click="showNewModal = true" class="app-btn app-btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>Buat API Key Baru</span>
            </button>

            @if($webhook)
                <form x-show="activeTab === 'webhooks'" method="POST" action="{{ route('webhooks.test') }}">
                    @csrf
                    <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        <span>Test Ping Webhook</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs Switcher -->
    <div class="border-b border-slate-200/80 dark:border-slate-800">
        <div class="flex gap-4 text-xs font-bold">
            <button @click="activeTab = 'api-keys'" 
                    :class="activeTab === 'api-keys' ? 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 border-b-2 pb-2.5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white pb-2.5'"
                    class="flex items-center gap-2 transition-colors cursor-pointer">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>API Keys ({{ $apiKeys->count() }})</span>
            </button>

            <button @click="activeTab = 'webhooks'" 
                    :class="activeTab === 'webhooks' ? 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 border-b-2 pb-2.5' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white pb-2.5'"
                    class="flex items-center gap-2 transition-colors cursor-pointer">
                <i data-lucide="webhook" class="w-4 h-4"></i>
                <span>Webhook Callback {{ $webhook ? '(Aktif)' : '(Belum Diatur)' }}</span>
            </button>
        </div>
    </div>

    <!-- ================= TAB 1: API KEYS MANAGEMENT ================= -->
    <div x-show="activeTab === 'api-keys'" class="space-y-5">

        @if($errors->has('captcha'))
            <div class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-xl text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ $errors->first('captcha') }}</span>
            </div>
        @endif

        <!-- Plain Text Token Reveal Banner -->
        @if(session('plain_text_token'))
            <div class="app-card p-5 bg-gradient-to-br from-blue-50 to-indigo-50/50 dark:from-blue-500/10 dark:to-indigo-500/5 border-blue-200 dark:border-blue-500/20 space-y-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Kunci API Baru Dibuat: {{ session('key_name') }}</h3>
                </div>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400">
                    Salin token API di bawah ini sekarang. Demi alasan keamanan, token rahasia ini hanya ditampilkan **satu kali**.
                </p>
                
                <div class="flex items-center gap-2 p-2 bg-white dark:bg-[#0D1526] border border-blue-200 dark:border-blue-500/20 rounded-xl">
                    <code class="font-mono font-bold text-xs text-blue-700 dark:text-blue-400 flex-1 select-all break-all px-2" id="plainToken">
                        {{ session('plain_text_token') }}
                    </code>
                    <button type="button" onclick="copyToClipboard(document.getElementById('plainToken').innerText.trim(), 'Token API berhasil disalin!')" class="app-btn app-btn-primary text-xs py-2 px-3 flex items-center gap-1 cursor-pointer">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Salin Token</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Active API Keys Table -->
        <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h2 class="font-bold text-base text-slate-900 dark:text-white">Kunci API Aktif Anda</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kunci ini digunakan pada header HTTP Authorization (Bearer token) atau parameter X-API-KEY.</p>
                </div>
                <span class="app-tag app-tag-blue text-[10px] font-mono">{{ $apiKeys->count() }} ACTIVE KEYS</span>
            </div>

            @if($apiKeys->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                    Belum ada API Key yang dibuat. Klik tombol <strong>"Buat API Key Baru"</strong> di atas.
                </div>
            @else
                <div class="app-table-wrapper overflow-x-auto">
                    <table class="w-full text-left text-xs font-medium app-table min-w-[650px]">
                        <thead>
                            <tr>
                                <th class="p-3.5">Nama Kunci</th>
                                <th class="p-3.5">Prefix Token</th>
                                <th class="p-3.5">Rate Limit</th>
                                <th class="p-3.5">Terakhir Digunakan</th>
                                <th class="p-3.5">Dibuat Pada</th>
                                <th class="p-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiKeys as $key)
                                <tr>
                                    <td class="p-3.5 font-bold text-slate-900 dark:text-white">{{ $key->name }}</td>
                                    <td class="p-3.5 font-mono text-slate-700 dark:text-slate-300"><code>{{ $key->key_prefix }}...</code></td>
                                    <td class="p-3.5 font-mono">{{ $key->rate_limit_per_minute }} req/min</td>
                                    <td class="p-3.5 font-mono text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Belum pernah' }}</td>
                                    <td class="p-3.5 font-mono text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ $key->created_at->format('d M Y') }}</td>
                                    <td class="p-3.5 text-right whitespace-nowrap">
                                        <button type="button" @click="
                                            revokeKeyName = '{{ e($key->name) }}';
                                            revokeActionUrl = '{{ route('api-keys.destroy', $key) }}';
                                            showRevokeModal = true;
                                        " class="app-btn app-btn-secondary text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 border-rose-200 dark:border-rose-500/20 text-[11px] py-1 px-2.5 cursor-pointer">
                                            Revoke Key
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    <!-- ================= TAB 2: WEBHOOK CALLBACK CONFIGURATION ================= -->
    <div x-show="activeTab === 'webhooks'" class="space-y-5">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Webhook Form (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="app-card p-6 bg-white dark:bg-[#111A2E] space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="font-bold text-base text-slate-900 dark:text-white">Konfigurasi Target Webhook</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tentukan URL target HTTPS yang akan menerima kiriman data event WhatsApp secara realtime.</p>
                    </div>

                    <form method="POST" action="{{ route('webhooks.store') }}" class="space-y-4">
                        @csrf

                        <!-- Target URL -->
                        <div class="space-y-1.5">
                            <label for="target_url" class="block font-bold text-xs text-slate-700 dark:text-slate-300">Target Webhook URL <span class="text-rose-500">*</span></label>
                            <input 
                                type="url" 
                                name="target_url" 
                                id="target_url" 
                                value="{{ old('target_url', $webhook->target_url ?? '') }}" 
                                placeholder="https://api.domain-anda.com/webhook/whatsapp" 
                                required 
                                class="input-text text-xs font-mono font-bold"
                            >
                        </div>

                        <!-- Secret Key -->
                        <div class="space-y-1.5">
                            <label for="secret_key" class="block font-bold text-xs text-slate-700 dark:text-slate-300">Secret Signature Key (Opsional)</label>
                            <input 
                                type="text" 
                                name="secret_key" 
                                id="secret_key" 
                                value="{{ old('secret_key', $webhook->secret_key ?? '') }}" 
                                placeholder="whsec_xxxxxxxxxxxx" 
                                class="input-text text-xs font-mono"
                            >
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Dikirim melalui header <code>X-WAGateway-Secret</code> untuk verifikasi keaslian payload.</p>
                        </div>

                        <!-- Events Subscription Checkboxes -->
                        <div class="space-y-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <label class="block font-bold text-xs text-slate-700 dark:text-slate-300">Berlangganan Event Realtime</label>
                            
                            @php
                                $subscribedEvents = $webhook->events ?? ['message.received', 'device.connected', 'device.disconnected'];
                            @endphp

                            <div class="space-y-2 text-xs">
                                <label class="flex items-start gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors">
                                    <input type="checkbox" name="events[]" value="message.received" {{ in_array('message.received', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <strong class="text-slate-900 dark:text-white font-mono text-xs">message.received</strong>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Dipicu saat ada pesan WhatsApp masuk dari kontak atau grup.</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors">
                                    <input type="checkbox" name="events[]" value="device.connected" {{ in_array('device.connected', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <strong class="text-slate-900 dark:text-white font-mono text-xs">device.connected</strong>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Dipicu saat nomor WhatsApp berhasil terhubung (Scan QR / Pairing Code).</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors">
                                    <input type="checkbox" name="events[]" value="device.disconnected" {{ in_array('device.disconnected', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <strong class="text-slate-900 dark:text-white font-mono text-xs">device.disconnected</strong>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Dipicu jika koneksi WhatsApp terputus atau logout.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="app-btn app-btn-primary py-2.5 px-5 text-xs flex items-center gap-2 cursor-pointer">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Simpan Konfigurasi Webhook</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Webhook Sample Payload Card (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="app-card p-6 bg-slate-950 text-white space-y-3.5 font-mono text-xs shadow-lg border-slate-800">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-blue-400 font-bold text-xs">Sample Payload (JSON)</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">HTTP POST</span>
                    </div>

                    <pre class="bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 text-[11px] overflow-x-auto text-slate-300">
{
  <span class="text-blue-400">"event"</span>: <span class="text-emerald-300">"message.received"</span>,
  <span class="text-blue-400">"timestamp"</span>: <span class="text-emerald-300">"{{ now()->toIso8601String() }}"</span>,
  <span class="text-blue-400">"device_id"</span>: <span class="text-amber-300">1</span>,
  <span class="text-blue-400">"sender"</span>: <span class="text-emerald-300">"6281234567890@s.whatsapp.net"</span>,
  <span class="text-blue-400">"push_name"</span>: <span class="text-emerald-300">"Budi Santoso"</span>,
  <span class="text-blue-400">"message"</span>: {
    <span class="text-blue-400">"text"</span>: <span class="text-emerald-300">"Halo, saya ingin menanyakan status pesanan #1049"</span>
  }
}</pre>
                    <p class="text-[11px] text-slate-400 font-sans">
                        Server Anda harus mengembalikan kode status <code>HTTP 200 OK</code> untuk mengonfirmasi penerimaan webhook.
                    </p>
                </div>
            </div>

        </div>

    </div>

    <!-- ================= GENERATE API KEY MODAL ================= -->
    <div x-show="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="showNewModal = false"></div>

        <div class="relative bg-white dark:bg-[#111A2E] rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4 z-10">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="key" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Buat API Key Baru</h3>
                </div>
                <button @click="showNewModal = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('api-keys.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="space-y-1.5">
                    <label for="name" class="font-bold text-slate-700 dark:text-slate-300">Nama Aplikasi / Kunci <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Website Main Server, Apps Node.js" class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="rate_limit" class="font-bold text-slate-700 dark:text-slate-300">Batas Request (Per Menit)</label>
                    <input type="number" name="rate_limit" id="rate_limit" value="60" min="10" max="1000" class="input-text text-xs">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Default: 60 request per menit per API key.</p>
                </div>

                @if(\App\Services\HcaptchaService::isEnabled())
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-center">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Verifikasi Keamanan Captcha</label>
                        {!! \App\Services\HcaptchaService::renderWidget() !!}
                    </div>
                @endif

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="showNewModal = false" class="app-btn app-btn-secondary py-2 px-3 text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Generate Key</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= REVOKE API KEY MODAL ================= -->
    <div x-show="showRevokeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="showRevokeModal = false"></div>

        <div class="relative bg-white dark:bg-[#111A2E] rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4 z-10">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg flex items-center justify-center font-bold">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">Revoke API Key</h3>
                </div>
                <button @click="showRevokeModal = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" :action="revokeActionUrl" class="space-y-4 text-xs">
                @csrf
                @method('DELETE')

                <div class="p-3.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl space-y-1">
                    <p class="font-bold text-rose-800 dark:text-rose-300">Konfirmasi Hapus Key</p>
                    <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                        Apakah Anda yakin ingin menghapus API Key <strong class="text-slate-900 dark:text-white" x-text="revokeKeyName"></strong>? Aplikasi yang menggunakan kunci ini akan langsung kehilangan akses.
                    </p>
                </div>

                @if(\App\Services\HcaptchaService::isEnabled())
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-center">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Verifikasi Keamanan Captcha</label>
                        {!! \App\Services\HcaptchaService::renderWidget() !!}
                    </div>
                @endif

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="showRevokeModal = false" class="app-btn app-btn-secondary py-2 px-3 text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="app-btn app-btn-primary bg-rose-600 hover:bg-rose-700 border-rose-600 text-white py-2 px-4 text-xs cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus Key</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{!! \App\Services\HcaptchaService::renderScript() !!}
@endsection
