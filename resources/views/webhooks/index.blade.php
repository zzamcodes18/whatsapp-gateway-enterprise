@extends('layouts.app')

@section('title', 'Webhook Callbacks - LAPAKOTP Gateway')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">REALTIME DISPATCHER</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">EVENT DRIVEN</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-slate-900">Webhook Callbacks</h1>
            <p class="text-xs text-slate-500 font-medium">Terima data pesan masuk dan status koneksi secara otomatis ke endpoint server Anda.</p>
        </div>

        @if($webhook)
            <form method="POST" action="{{ route('webhooks.test') }}">
                @csrf
                <button type="submit" class="app-btn app-btn-secondary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>Test Ping Webhook</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Webhook Settings & Preview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Form (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="app-card p-6 bg-white space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-bold text-base text-slate-900">Konfigurasi Endpoint</h2>
                    <p class="text-xs text-slate-500 font-medium">Tentukan URL target HTTPS yang akan menerima POST request event WhatsApp.</p>
                </div>

                <form method="POST" action="{{ route('webhooks.store') }}" class="space-y-4">
                    @csrf

                    <!-- Target URL -->
                    <div class="space-y-1.5">
                        <label for="target_url" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Target Webhook URL</label>
                        <input 
                            type="url" 
                            name="target_url" 
                            id="target_url" 
                            value="{{ old('target_url', $webhook->target_url ?? '') }}" 
                            placeholder="https://api.domain-anda.com/webhook/whatsapp" 
                            required 
                            class="app-input text-xs font-mono font-bold"
                        >
                    </div>

                    <!-- Secret Key -->
                    <div class="space-y-1.5">
                        <label for="secret_key" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Secret Signature Key (Opsional)</label>
                        <input 
                            type="text" 
                            name="secret_key" 
                            id="secret_key" 
                            value="{{ old('secret_key', $webhook->secret_key ?? '') }}" 
                            placeholder="whsec_xxxxxxxxxxxx" 
                            class="app-input text-xs font-mono"
                        >
                        <p class="text-[11px] text-slate-500 font-medium">Dikirimkan melalui header <code>X-LapakOTP-Secret</code> untuk verifikasi keaslian payload.</p>
                    </div>

                    <!-- Events Subscription -->
                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Berlangganan Event</label>
                        
                        @php
                            $subscribedEvents = $webhook->events ?? ['message.received', 'device.connected', 'device.disconnected'];
                        @endphp

                        <div class="space-y-2 text-xs">
                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="events[]" value="message.received" {{ in_array('message.received', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <strong class="text-slate-900 font-mono text-xs">message.received</strong>
                                    <div class="text-[11px] text-slate-500">Dipicu saat ada pesan WhatsApp masuk ke nomor Anda.</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="events[]" value="device.connected" {{ in_array('device.connected', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <strong class="text-slate-900 font-mono text-xs">device.connected</strong>
                                    <div class="text-[11px] text-slate-500">Dipicu saat perangkat WhatsApp berhasil ditautkan.</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" name="events[]" value="device.disconnected" {{ in_array('device.disconnected', $subscribedEvents) ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <strong class="text-slate-900 font-mono text-xs">device.disconnected</strong>
                                    <div class="text-[11px] text-slate-500">Dipicu saat perangkat terputus atau logout dari HP.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-1">
                        <button type="submit" class="app-btn app-btn-primary py-2.5 px-5 text-xs flex items-center gap-2 cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Konfigurasi Webhook</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Example Payload Preview (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="app-card p-6 bg-slate-950 text-white space-y-3.5 font-mono text-xs shadow-lg border-slate-800">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <span class="text-sky-400 font-bold">JSON PAYLOAD FORMAT</span>
                    <span class="text-slate-500 text-[10px]">HTTP POST</span>
                </div>

                <p class="text-slate-400 text-xs font-sans">
                    Struktur payload yang dikirimkan ke server Anda saat event dipicu:
                </p>

                <pre class="text-emerald-400 overflow-x-auto p-3 bg-black/60 rounded-xl border border-slate-800 text-[11px] leading-relaxed"><code>{
  "event": "message.received",
  "data": {
    "message_id": 481,
    "device_id": 1,
    "remote_jid": "6281234567890@s.whatsapp.net",
    "push_name": "Budi Santoso",
    "content": "Halo admin, info jadwal pengiriman",
    "timestamp": 1756304125
  },
  "timestamp": "{{ now()->toIso8601String() }}"
}</code></pre>

                <div class="p-3 bg-slate-900 border border-slate-800 rounded-xl space-y-1 text-[11px] text-slate-400">
                    <div class="text-white font-bold">Request Headers:</div>
                    <div>Content-Type: <code>application/json</code></div>
                    <div>X-LapakOTP-Event: <code>message.received</code></div>
                    <div>X-LapakOTP-Secret: <code>{{ $webhook->secret_key ?? 'whsec_...' }}</code></div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
