@extends('layouts.app')

@section('title', 'Seluruh Perangkat Sistem')

@section('content')
<div class="space-y-6" x-data="adminDevicesManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">GLOBAL SESSIONS</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Seluruh Perangkat Terdaftar</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pantau dan kelola seluruh sesi WhatsApp dari semua akun pengguna di sistem.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="btn-xl btn-primary text-xs py-2.5 px-4 flex items-center gap-2 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Device Sistem</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="app-card p-4 bg-white dark:bg-[#111A2E] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <form method="GET" action="{{ route('admin.devices.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari device, nomor, session, user..." class="input-text py-2 px-3 text-xs w-60 font-medium">
            
            <select name="status" onchange="this.form.submit()" class="input-text py-2 px-2.5 text-xs font-semibold w-36 cursor-pointer">
                <option value="">Status: Semua</option>
                <option value="connected" {{ request('status') === 'connected' ? 'selected' : '' }}>Connected</option>
                <option value="disconnected" {{ request('status') === 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                <option value="qr_ready" {{ request('status') === 'qr_ready' ? 'selected' : '' }}>QR Ready</option>
                <option value="pairing_ready" {{ request('status') === 'pairing_ready' ? 'selected' : '' }}>Pairing Ready</option>
            </select>

            <button type="submit" class="app-btn app-btn-secondary py-2 px-3.5 text-xs cursor-pointer">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-600"></i>
            </button>
        </form>

        <div class="text-xs font-mono font-semibold text-slate-500 dark:text-slate-400">
            Total: {{ $devices->total() }} Sesi
        </div>
    </div>

    <!-- Global Devices Table with Responsive Horizontal Scroll -->
    <div class="app-table-wrapper">
        <table class="w-full text-left text-xs font-medium app-table min-w-[800px]">
            <thead>
                <tr>
                    <th class="p-3.5">Perangkat</th>
                    <th class="p-3.5">Pemilik Akun</th>
                    <th class="p-3.5">Nomor WhatsApp</th>
                    <th class="p-3.5">Metode</th>
                    <th class="p-3.5">Status</th>
                    <th class="p-3.5">Terkoneksi Sejak</th>
                    <th class="p-3.5 text-right">Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr>
                        <td class="p-3.5">
                            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>{{ $device->name }}</span>
                                @if($device->is_system_bot)
                                    <span class="app-tag app-tag-blue text-[8px] py-0 px-1.5">BOT OTP</span>
                                @endif
                            </div>
                            <div class="font-mono text-[10px] text-slate-400 truncate max-w-[140px]" title="{{ $device->session_id }}">{{ $device->session_id }}</div>
                        </td>
                        <td class="p-3.5">
                            @if($device->user)
                                <div class="font-bold text-slate-900 dark:text-white">{{ $device->user->name }}</div>
                                <div class="font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $device->user->email }}</div>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">Tanpa Pemilik</span>
                            @endif
                        </td>
                        <td class="p-3.5 font-mono font-bold text-slate-900 dark:text-white">
                            {{ $device->phone_number ? '+' . $device->phone_number : '-' }}
                        </td>
                        <td class="p-3.5 font-mono uppercase text-[10px] text-slate-600 dark:text-slate-400 font-semibold">
                            {{ $device->connection_type }}
                        </td>
                        <td class="p-3.5">
                            <span class="app-tag text-[9px] {{ $device->status === 'connected' ? 'app-tag-emerald' : 'app-tag-amber' }}">
                                {{ strtoupper($device->status) }}
                            </span>
                        </td>
                        <td class="p-3.5 font-mono text-slate-400 dark:text-slate-500 text-[11px]">
                            {{ $device->connected_at ? $device->connected_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                            <button type="button" @click="openEditModal(@js($device))" class="app-btn app-btn-secondary text-[11px] py-1 px-2.5 cursor-pointer">
                                <i data-lucide="edit-3" class="w-3 h-3 text-blue-600"></i>
                                <span>Edit</span>
                            </button>

                            <form method="POST" action="{{ route('admin.devices.restart', $device) }}" class="inline">
                                @csrf
                                <button type="submit" class="app-btn app-btn-secondary text-[11px] py-1 px-2 cursor-pointer" title="Restart Sesi">
                                    <i data-lucide="refresh-cw" class="w-3 h-3 text-slate-600"></i>
                                </button>
                            </form>

                            @if($device->status === 'connected')
                                <button type="button" @click="$confirm({
                                    title: 'Force Disconnect',
                                    message: 'Apakah Anda yakin ingin memutuskan paksa sesi device \'{{ $device->name }}\'?',
                                    confirmText: 'Putuskan Paksa',
                                    type: 'danger',
                                    onConfirm: () => document.getElementById('admin-disc-{{ $device->id }}').submit()
                                })" class="app-btn app-btn-soft-danger text-[11px] py-1 px-2 cursor-pointer">
                                    Disconnect
                                </button>
                                <form id="admin-disc-{{ $device->id }}" method="POST" action="{{ route('admin.devices.force-disconnect', $device) }}" class="hidden">
                                    @csrf
                                </form>
                            @endif

                            <button type="button" @click="$confirm({
                                title: 'Hapus Device',
                                message: 'Apakah Anda yakin ingin menghapus device \'{{ $device->name }}\' secara permanen dari server?',
                                confirmText: 'Hapus',
                                type: 'danger',
                                onConfirm: () => document.getElementById('admin-del-{{ $device->id }}').submit()
                            })" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer" title="Hapus Device">
                                <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                            </button>
                            <form id="admin-del-{{ $device->id }}" method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium">
                            Belum ada perangkat yang terdaftar di sistem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $devices->links() }}
    </div>

    <!-- ================= ADMIN ADD DEVICE MODAL ================= -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-base text-navy">Tambah Device Pengguna</h3>
                <button @click="showCreateModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.devices.store') }}" class="space-y-3.5">
                @csrf
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Pilih Pemilik Akun</label>
                    <select name="user_id" required class="input-text text-xs font-semibold cursor-pointer">
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Perangkat</label>
                    <input type="text" name="name" required placeholder="Contoh: CS Official Admin" class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Metode Koneksi</label>
                    <select name="connection_type" class="input-text text-xs font-semibold cursor-pointer">
                        <option value="qr">Scan QR Code</option>
                        <option value="pairing_code">Pairing Code 8-Digit</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nomor Telepon (Wajib jika Pairing Code)</label>
                    <input type="text" name="phone_number" placeholder="628123456789" class="input-text text-xs font-mono">
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="showCreateModal = false" class="app-btn app-btn-secondary text-xs py-2 px-3.5">
                        Batal
                    </button>
                    <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Buat Device</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= ADMIN EDIT DEVICE MODAL ================= -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-base text-navy">Edit Data Perangkat</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="'UUID: ' + editForm.session_id"></p>
                </div>
                <button @click="showEditModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'/anjayadminwkwk/devices/' + editForm.id" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Pemilik Akun</label>
                    <select name="user_id" x-model="editForm.user_id" required class="input-text text-xs font-semibold cursor-pointer">
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Perangkat</label>
                    <input type="text" name="name" x-model="editForm.name" required class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nomor WhatsApp</label>
                    <input type="text" name="phone_number" x-model="editForm.phone_number" placeholder="628123456789" class="input-text text-xs font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Status Koneksi</label>
                    <select name="status" x-model="editForm.status" class="input-text text-xs font-semibold cursor-pointer">
                        <option value="connected">Connected</option>
                        <option value="disconnected">Disconnected</option>
                        <option value="connecting">Connecting</option>
                        <option value="qr_ready">QR Ready</option>
                        <option value="pairing_ready">Pairing Ready</option>
                    </select>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="app-btn app-btn-secondary text-xs py-2 px-3.5">
                        Batal
                    </button>
                    <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function adminDevicesManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editForm: {
            id: null,
            user_id: '',
            name: '',
            phone_number: '',
            status: 'disconnected',
            session_id: ''
        },

        openCreateModal() {
            this.showCreateModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        openEditModal(device) {
            this.editForm = {
                id: device.id,
                user_id: device.user_id,
                name: device.name,
                phone_number: device.phone_number || '',
                status: device.status,
                session_id: device.session_id
            };
            this.showEditModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        }
    }
}
</script>
@endsection
