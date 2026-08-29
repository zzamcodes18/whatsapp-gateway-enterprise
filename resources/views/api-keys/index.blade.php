@extends('layouts.app')

@section('title', 'API Keys & Dokumentasi')

@section('content')
<div class="space-y-6" x-data="{ showNewModal: false, docTab: 'curl' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">REST API CREDENTIALS</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">SHA-256 HASHED</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">API Keys & Integrasi</h1>
            <p class="text-xs text-slate-500 font-medium">Gunakan API Key untuk mengintegrasikan pengiriman WhatsApp dengan sistem aplikasi Anda.</p>
        </div>

        <button @click="showNewModal = true" class="btn-xl btn-primary text-xs py-2.5 px-4 flex items-center gap-1.5 cursor-pointer">
            <i data-lucide="key" class="w-4 h-4"></i>
            <span>Generate API Key Baru</span>
        </button>
    </div>

    <!-- Plain Text Token Reveal Banner -->
    @if(session('plain_text_token'))
        <div class="app-card p-5 bg-gradient-to-br from-blue-50 to-indigo-50/50 border-blue-200 space-y-3 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <h3 class="font-bold text-sm text-navy">Kunci API Baru: {{ session('key_name') }}</h3>
            </div>
            <p class="text-xs font-medium text-slate-600">
                Salin token API di bawah ini sekarang. Demi alasan keamanan, token rahasia ini hanya ditampilkan satu kali.
            </p>
            
            <div class="flex items-center gap-2 p-2 bg-white border border-blue-200 rounded-xl">
                <code class="font-mono font-bold text-xs text-blue-700 flex-1 select-all break-all px-2" id="plainToken">
                    {{ session('plain_text_token') }}
                </code>
                <button type="button" onclick="copyToClipboard(document.getElementById('plainToken').innerText.trim(), 'Token API berhasil disalin!')" class="btn-xl btn-primary text-xs py-2 px-3 flex items-center gap-1 cursor-pointer">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span>Salin</span>
                </button>
            </div>
        </div>
    @endif

    <!-- API Keys List Table -->
    <div class="space-y-3.5">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-base text-navy">Daftar API Keys Aktif</h2>
            <span class="app-tag app-tag-slate text-[10px]">{{ $apiKeys->count() }} KUNCI</span>
        </div>

        @if($apiKeys->isEmpty())
            <div class="app-card p-8 text-center text-xs text-slate-400 font-medium bg-white">
                Belum ada API Key yang dibuat. Klik tombol di atas untuk membuat API Key baru.
            </div>
        @else
            <!-- Responsive Table Wrapper with Horizontal Scroll -->
            <div class="app-table-wrapper">
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
                                <td class="p-3.5 font-bold text-slate-900">{{ $key->name }}</td>
                                <td class="p-3.5 font-mono text-slate-700"><code>{{ $key->key_prefix }}...</code></td>
                                <td class="p-3.5 font-mono">{{ $key->rate_limit_per_minute }} req/min</td>
                                <td class="p-3.5 font-mono text-slate-400 whitespace-nowrap">{{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Belum pernah' }}</td>
                                <td class="p-3.5 font-mono text-slate-400 whitespace-nowrap">{{ $key->created_at->format('d M Y') }}</td>
                                <td class="p-3.5 text-right whitespace-nowrap">
                                    <button type="button" @click="$confirm({
                                        title: 'Revoke API Key',
                                        message: 'Apakah Anda yakin ingin menghapus API Key \'{{ $key->name }}\'? Aplikasi yang menggunakan kunci ini akan kehilangan akses.',
                                        confirmText: 'Revoke Key',
                                        type: 'danger',
                                        onConfirm: () => document.getElementById('delete-key-{{ $key->id }}').submit()
                                    })" class="app-btn app-btn-soft-danger text-[11px] py-1 px-2.5 cursor-pointer">
                                        Revoke
                                    </button>
                                    <form id="delete-key-{{ $key->id }}" method="POST" action="{{ route('api-keys.destroy', $key) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Documentation Section -->
    <div class="app-card p-6 bg-slate-950 text-white space-y-4 font-mono text-xs shadow-lg border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3.5">
            <div>
                <span class="app-tag app-tag-blue text-[9px]">REST API v1</span>
                <h3 class="font-bold text-base mt-1 text-white">Panduan Integrasi REST API</h3>
            </div>

            <div class="flex items-center gap-2">
                <button @click="docTab = 'curl'" :class="docTab === 'curl' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1.5 text-xs rounded-lg border border-slate-700 cursor-pointer transition-colors">cURL</button>
                <button @click="docTab = 'js'" :class="docTab === 'js' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1.5 text-xs rounded-lg border border-slate-700 cursor-pointer transition-colors">JavaScript</button>
                <button @click="docTab = 'php'" :class="docTab === 'php' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white bg-slate-900'" class="px-3 py-1.5 text-xs rounded-lg border border-slate-700 cursor-pointer transition-colors">PHP</button>
            </div>
        </div>

        <!-- Endpoints List -->
        <div class="space-y-4">
            <!-- Endpoint: Send Text -->
            <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="bg-emerald-500 text-slate-950 px-2 py-0.5 font-bold rounded text-[10px]">POST</span>
                        <span class="text-white font-bold">/api/v1/messages/send-text</span>
                    </div>
                    <span class="text-slate-400 text-[11px] font-sans">Kirim Pesan Teks</span>
                </div>

                <div x-show="docTab === 'curl'">
                    <pre class="text-emerald-400 overflow-x-auto p-2.5 bg-black/60 rounded-lg"><code>curl -X POST "{{ url('/api/v1/messages/send-text') }}" \
  -H "X-API-Key: lpk_live_your_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": 1,
    "phone": "6281234567890",
    "message": "Kode OTP Anda: 902-192. Berlaku 5 menit."
  }'</code></pre>
                </div>

                <div x-show="docTab === 'js'" style="display: none;">
                    <pre class="text-sky-300 overflow-x-auto p-2.5 bg-black/60 rounded-lg"><code>const res = await fetch("{{ url('/api/v1/messages/send-text') }}", {
  method: "POST",
  headers: {
    "X-API-Key": "lpk_live_your_key_here",
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    device_id: 1,
    phone: "6281234567890",
    message: "Kode OTP Anda: 902-192. Berlaku 5 menit."
  })
});</code></pre>
                </div>

                <div x-show="docTab === 'php'" style="display: none;">
                    <pre class="text-amber-300 overflow-x-auto p-2.5 bg-black/60 rounded-lg"><code>use Illuminate\Support\Facades\Http;

$res = Http::withHeaders(['X-API-Key' => 'lpk_live_your_key_here'])
    ->post("{{ url('/api/v1/messages/send-text') }}", [
        'device_id' => 1,
        'phone' => '6281234567890',
        'message' => 'Kode OTP Anda: 902-192.',
    ]);</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Create API Key Modal -->
    <div x-show="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white max-w-sm w-full p-6 space-y-4 shadow-2xl border-slate-100 custom-scrollbar" @click.away="showNewModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="font-bold text-base text-navy">Buat API Key Baru</h3>
                <button @click="showNewModal = false" class="p-1 text-slate-400 hover:text-slate-800 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('api-keys.store') }}" class="space-y-3.5">
                @csrf
                <div class="space-y-1.5">
                    <label for="key_name" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nama / Label Kunci</label>
                    <input type="text" name="name" id="key_name" placeholder="Contoh: Server Webhook / POS Toko" required class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="rate_limit" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Rate Limit (Req / Menit)</label>
                    <input type="number" name="rate_limit" id="rate_limit" value="60" min="10" max="1000" class="input-text text-xs font-mono font-bold">
                </div>

                <div class="pt-1">
                    <button type="submit" class="btn-xl btn-primary w-full py-2.5 text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Generate & Simpan</span>
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
