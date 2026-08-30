@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6" x-data="adminUsersManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">USER ACCOUNTS</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Atur role, batas perangkat (device limit), dan kuota pesan harian untuk masing-masing user.</p>
        </div>

        <button @click="openCreateModal()" class="btn-xl btn-primary text-xs py-2.5 px-4 flex items-center gap-2 cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="app-card p-4 bg-white dark:bg-[#111A2E] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor..." class="input-text py-2 px-3 text-xs w-52 font-medium">
            
            <select name="role" onchange="this.form.submit()" class="input-text py-2 px-2.5 text-xs font-semibold w-32 cursor-pointer">
                <option value="">Role: Semua</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="input-text py-2 px-2.5 text-xs font-semibold w-32 cursor-pointer">
                <option value="">Status: Semua</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit" class="app-btn app-btn-secondary py-2 px-3.5 text-xs cursor-pointer">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-600"></i>
            </button>
        </form>

        <div class="text-xs font-mono font-semibold text-slate-500 dark:text-slate-400">
            Total: {{ $users->total() }} Pengguna
        </div>
    </div>

    <!-- Responsive Users Table with Smooth Horizontal Scroll -->
    <div class="app-table-wrapper">
        <table class="w-full text-left text-xs font-medium app-table min-w-[760px]">
            <thead>
                <tr>
                    <th class="p-3.5">Pengguna</th>
                    <th class="p-3.5">Role</th>
                    <th class="p-3.5">Paket (Plan)</th>
                    <th class="p-3.5">Device (Aktif/Limit)</th>
                    <th class="p-3.5">Pesan Hari Ini (Terkirim/Limit)</th>
                    <th class="p-3.5">Status</th>
                    <th class="p-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="p-3.5">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                            <div class="font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                            @if($user->phone_number)
                                <div class="font-mono text-[11px] text-slate-400 dark:text-slate-500">+{{ $user->phone_number }}</div>
                            @endif
                        </td>
                        <td class="p-3.5">
                            <span class="app-tag text-[9px] {{ $user->isAdmin() ? 'app-tag-blue' : 'app-tag-slate' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            @if($user->plan)
                                <div class="flex flex-col gap-0.5">
                                    <span class="app-tag text-[9px] {{ $user->hasActivePlan() ? 'app-tag-blue' : 'app-tag-rose' }}">
                                        {{ strtoupper($user->plan->name) }}
                                    </span>
                                    @if($user->plan_expires_at)
                                        <span class="text-[10px] font-mono {{ $user->hasActivePlan() ? 'text-slate-400 dark:text-slate-500' : 'text-rose-500 font-bold' }}">
                                            {{ $user->hasActivePlan() ? 's/d ' . $user->plan_expires_at->format('d M Y') : 'EXPIRED' }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Lifetime</span>
                                    @endif
                                </div>
                            @else
                                <span class="app-tag text-[9px] app-tag-slate">MANUAL</span>
                            @endif
                        </td>
                        <td class="p-3.5 font-mono">
                            <strong class="text-slate-900 dark:text-white">{{ $user->devices_count }}</strong> / {{ $user->effectiveDeviceLimit() > 0 ? $user->effectiveDeviceLimit() . ' Unit' : 'Unlimited' }}
                        </td>
                        <td class="p-3.5 font-mono">
                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $user->messages_sent_today }}</span> / {{ $user->effectiveDailyMessageLimit() ? $user->effectiveDailyMessageLimit() . ' msg/hari' : 'Unlimited' }}
                            <div class="text-[10px] text-slate-400 dark:text-slate-500">Total: {{ $user->messages_count }}</div>
                        </td>
                        <td class="p-3.5">
                            <span class="app-tag text-[9px] {{ $user->is_active ? 'app-tag-emerald' : 'app-tag-rose' }}">
                                {{ $user->is_active ? 'Aktif' : 'Suspended' }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                            <button type="button" @click="openEditModal(@js($user))" class="app-btn app-btn-secondary text-[11px] py-1 px-2.5 cursor-pointer">
                                <i data-lucide="edit-3" class="w-3 h-3 text-blue-600"></i>
                                <span>Edit</span>
                            </button>

                            @if(!$user->isAdmin())
                                <button type="button" @click="openPlanModal(@js($user))" class="app-btn app-btn-soft-blue text-[11px] py-1 px-2.5 cursor-pointer">
                                    <i data-lucide="package" class="w-3 h-3"></i>
                                    <span>Plan</span>
                                </button>
                            @endif

                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="app-btn text-[11px] py-1 px-2.5 cursor-pointer {{ $user->is_active ? 'app-btn-soft-danger' : 'app-btn-soft-success' }}">
                                        {{ $user->is_active ? 'Suspend' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <button type="button" @click="$confirm({
                                    title: 'Hapus Pengguna',
                                    message: 'Apakah Anda yakin ingin menghapus user \'{{ $user->name }}\'? Seluruh perangkat dan pesan pengguna ini akan dihapus permanen.',
                                    confirmText: 'Hapus User',
                                    type: 'danger',
                                    onConfirm: () => document.getElementById('delete-usr-{{ $user->id }}').submit()
                                })" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer" title="Hapus User">
                                    <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                                </button>
                                <form id="delete-usr-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-xs text-slate-400 dark:text-slate-500 font-medium">
                            Tidak ada data pengguna yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $users->links() }}
    </div>

    <!-- ================= CREATE USER MODAL ================= -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-base text-navy">Tambah Pengguna Baru</h3>
                <button @click="showCreateModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3.5">
                @csrf
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="John Doe" class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Email Akun</label>
                    <input type="email" name="email" required placeholder="user@perusahaan.com" class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nomor WhatsApp (Opsional)</label>
                    <input type="text" name="phone_number" placeholder="628123456789" class="input-text text-xs font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Password Akun</label>
                    <input type="password" name="password" required placeholder="••••••••" class="input-text text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Role</label>
                        <select name="role" class="input-text text-xs font-semibold cursor-pointer">
                            <option value="user">User Biasa</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Device</label>
                        <input type="number" name="device_limit" value="3" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukkan 0 jika ingin kuota tanpa batas.</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Hari</label>
                    <input type="number" name="daily_message_limit" value="500" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukkan 0 jika ingin kuota tanpa batas.</p>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="showCreateModal = false" class="app-btn app-btn-secondary text-xs py-2 px-3.5">
                        Batal
                    </button>
                    <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Simpan Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT USER MODAL ================= -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-base text-navy">Edit Pengguna & Kuota</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="editForm.email"></p>
                </div>
                <button @click="showEditModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'/anjayadminwkwk/users/' + editForm.id" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editForm.name" required class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Email Akun</label>
                    <input type="email" name="email" x-model="editForm.email" required class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nomor WhatsApp</label>
                    <input type="text" name="phone_number" x-model="editForm.phone_number" class="input-text text-xs font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Ganti Password (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="input-text text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Role</label>
                        <select name="role" x-model="editForm.role" class="input-text text-xs font-semibold cursor-pointer">
                            <option value="user">User Biasa</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Status Akun</label>
                        <select name="is_active" x-model="editForm.is_active" class="input-text text-xs font-semibold cursor-pointer">
                            <option value="1">Aktif</option>
                            <option value="0">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Device</label>
                        <input type="number" name="device_limit" x-model="editForm.device_limit" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Hari</label>
                        <input type="number" name="daily_message_limit" x-model="editForm.daily_message_limit" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Masukkan 0 jika ingin limit device / pesan tanpa batas (unlimited).</p>

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

    <!-- ================= ASSIGN PLAN MODAL ================= -->
    <div x-show="showPlanModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showPlanModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-base text-navy">Tetapkan Paket (Plan)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="planForm.email"></p>
                </div>
                <button @click="showPlanModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl p-3 text-[11px] text-blue-700 dark:text-blue-400 font-medium space-y-1">
                <p><strong>Catatan:</strong> Saat paket ditetapkan, limit device &amp; pesan harian user akan otomatis mengikuti limit paket (meng-override limit manual).</p>
                <p x-show="planForm.current_plan_name" class="font-semibold">Paket saat ini: <span x-text="planForm.current_plan_name"></span></p>
            </div>

            @if($plans->isEmpty())
                <div class="p-4 text-center text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Belum ada paket aktif yang tersedia.
                    <a href="{{ route('admin.plans.index') }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">Buat paket terlebih dahulu &rarr;</a>
                </div>
            @else
                <form :action="'/anjayadminwkwk/users/' + planForm.id + '/assign-plan'" method="POST" class="space-y-3.5">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Pilih Paket</label>
                        <select name="plan_id" class="input-text text-xs font-semibold cursor-pointer" required>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} — {{ $plan->formatPrice() }} / {{ $plan->duration_days }} hari ({{ $plan->formatDeviceLimit() }}, {{ $plan->formatMessageLimit() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        @if($user->plan_id ?? false)
                        @endif
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showPlanModal = false" class="app-btn app-btn-secondary text-xs py-2 px-3.5">
                                Batal
                            </button>
                            <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Tetapkan Paket</span>
                            </button>
                        </div>
                    </div>
                </form>

                <template x-if="planForm.current_plan_id">
                    <form :action="'/anjayadminwkwk/users/' + planForm.id + '/revoke-plan'" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        @csrf
                        <button type="submit" class="app-btn app-btn-soft-danger text-xs py-2 px-3.5 w-full flex items-center justify-center gap-1.5 cursor-pointer">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            <span>Cabut Paket Saat Ini</span>
                        </button>
                    </form>
                </template>
            @endif
        </div>
    </div>

</div>

<script>
function adminUsersManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showPlanModal: false,
        planForm: {
            id: null,
            email: '',
            current_plan_id: null,
            current_plan_name: ''
        },
        editForm: {
            id: null,
            name: '',
            email: '',
            phone_number: '',
            role: 'user',
            device_limit: 3,
            daily_message_limit: 500,
            is_active: 1
        },

        openCreateModal() {
            this.showCreateModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        openEditModal(user) {
            this.editForm = {
                id: user.id,
                name: user.name,
                email: user.email,
                phone_number: user.phone_number || '',
                role: user.role,
                device_limit: user.device_limit ?? 3,
                daily_message_limit: user.daily_message_limit ?? 500,
                is_active: user.is_active ? '1' : '0'
            };
            this.showEditModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        openPlanModal(user) {
            this.planForm = {
                id: user.id,
                email: user.email,
                current_plan_id: user.plan_id || null,
                current_plan_name: user.plan ? user.plan.name : ''
            };
            this.showPlanModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        }
    }
}
</script>
@endsection
