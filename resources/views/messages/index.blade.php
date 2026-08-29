@extends('layouts.app')

@section('title', 'Kirim Pesan & Log · Whatsapp Gateway Enterprise')

@section('content')
<div class="space-y-6" x-data="{ messageType: 'text', msgContent: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">MESSAGING ENGINE</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">REST SENDER</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Kirim Pesan WhatsApp</h1>
            <p class="text-xs text-slate-500 font-medium">Uji coba kirim pesan teks atau berkas media langsung melalui perangkat aktif.</p>
        </div>
    </div>

    <!-- Message Sender Box -->
    <div class="app-card p-6 bg-white space-y-4 shadow-2xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold text-xs">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </div>
                <h2 class="font-bold text-sm text-navy">Form Pengiriman Pesan</h2>
            </div>
            <span class="app-tag app-tag-blue text-[9px]">DIRECT SENDER</span>
        </div>

        @if($devices->isEmpty())
            <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-xl text-xs space-y-2">
                <p class="font-bold text-amber-900 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    <span>Belum ada perangkat WhatsApp yang terhubung!</span>
                </p>
                <p class="text-slate-600 text-xs">
                    Hubungkan minimal satu perangkat WhatsApp terlebih dahulu untuk mulai mengirim pesan.
                </p>
                <div class="pt-1">
                    <a href="{{ route('devices.index') }}" class="btn-xl btn-primary text-xs py-2 px-3.5 inline-flex items-center gap-1.5">
                        <span>Tambah Device</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('messages.send') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Device Selector -->
                    <div class="space-y-1.5">
                        <label for="device_id" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Perangkat Pengirim</label>
                        <select name="device_id" id="device_id" required class="input-text text-xs font-semibold cursor-pointer">
                            @foreach($devices as $dev)
                                <option value="{{ $dev->id }}">
                                    {{ $dev->name }} ({{ $dev->phone_number ? '+' . $dev->phone_number : 'Connected' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Phone -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nomor WhatsApp Tujuan</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="6281234567890 atau 08123456789" required class="input-text text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Message Type Tabs -->
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Tipe Pesan</label>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-2 py-2 px-3.5 rounded-xl cursor-pointer text-xs font-semibold transition-all border" :class="messageType === 'text' ? 'bg-blue-50 border-blue-200 text-blue-700 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="message_type" value="text" x-model="messageType" class="hidden">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            <span>Teks Biasa</span>
                        </label>
                        <label class="flex items-center gap-2 py-2 px-3.5 rounded-xl cursor-pointer text-xs font-semibold transition-all border" :class="messageType === 'image' ? 'bg-blue-50 border-blue-200 text-blue-700 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="message_type" value="image" x-model="messageType" class="hidden">
                            <i data-lucide="image" class="w-4 h-4"></i>
                            <span>Gambar (URL)</span>
                        </label>
                        <label class="flex items-center gap-2 py-2 px-3.5 rounded-xl cursor-pointer text-xs font-semibold transition-all border" :class="messageType === 'document' ? 'bg-blue-50 border-blue-200 text-blue-700 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <input type="radio" name="message_type" value="document" x-model="messageType" class="hidden">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            <span>Dokumen (PDF URL)</span>
                        </label>
                    </div>
                </div>

                <!-- Media URL -->
                <div x-show="messageType !== 'text'" class="space-y-1.5" style="display: none;">
                    <label for="media_url" class="block font-bold text-xs uppercase tracking-wider text-slate-600">URL Direct Berkas Media</label>
                    <input type="url" name="media_url" id="media_url" placeholder="https://example.com/invoice.pdf atau gambar.png" class="input-text text-xs font-mono">
                </div>

                <!-- Message Content -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="message" class="block font-bold text-xs uppercase tracking-wider text-slate-600" x-text="messageType === 'text' ? 'Isi Pesan Teks' : 'Keterangan / Caption'"></label>
                        <span class="text-[11px] font-mono text-slate-400" x-text="msgContent.length + ' karakter'"></span>
                    </div>
                    <textarea name="message" id="message" rows="3" x-model="msgContent" placeholder="Tulis isi pesan di sini..." class="input-text text-xs">{{ old('message') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button type="submit" class="btn-xl btn-primary py-2.5 px-5 text-xs flex items-center gap-2 cursor-pointer">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        <span>Kirim Pesan Sekarang</span>
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Message Logs Table -->
    <div class="space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <h2 class="font-bold text-base text-navy">Riwayat Pesan Gateway</h2>

            <!-- Filters -->
            <form method="GET" action="{{ route('messages.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor/pesan..." class="input-text py-1.5 px-3 text-xs w-48 font-medium">
                <select name="status" onchange="this.form.submit()" class="input-text py-1.5 px-2.5 text-xs font-semibold w-32 cursor-pointer">
                    <option value="">Status: Semua</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="app-btn app-btn-secondary py-1.5 px-3 text-xs cursor-pointer">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-600"></i>
                </button>
            </form>
        </div>

        @if($messages->isEmpty())
            <div class="app-card p-8 text-center text-xs text-slate-400 font-medium bg-white">
                Belum ada data pesan yang sesuai dengan filter pencarian.
            </div>
        @else
            <!-- Responsive Table Wrapper with Horizontal Scroll -->
            <div class="app-table-wrapper">
                <table class="w-full text-left text-xs font-medium app-table min-w-[700px]">
                    <thead>
                        <tr>
                            <th class="p-3.5">Device</th>
                            <th class="p-3.5">Tujuan / Dari</th>
                            <th class="p-3.5">Tipe</th>
                            <th class="p-3.5">Pesan</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $msg)
                            <tr>
                                <td class="p-3.5 font-bold text-slate-900 truncate max-w-[140px]">
                                    {{ $msg->device->name ?? 'Device' }}
                                </td>
                                <td class="p-3.5 font-mono font-bold text-slate-900">
                                    {{ $msg->clean_phone }}
                                </td>
                                <td class="p-3.5">
                                    <span class="font-mono text-[10px] uppercase bg-slate-100 px-2 py-0.5 rounded-md font-bold text-slate-700">
                                        {{ $msg->message_type }}
                                    </span>
                                </td>
                                <td class="p-3.5 max-w-xs truncate text-slate-700">
                                    {{ $msg->message_content }}
                                </td>
                                <td class="p-3.5">
                                    <span class="app-tag text-[9px] {{ in_array($msg->status, ['sent', 'delivered', 'read']) ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                        {{ strtoupper($msg->status) }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                    {{ $msg->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
