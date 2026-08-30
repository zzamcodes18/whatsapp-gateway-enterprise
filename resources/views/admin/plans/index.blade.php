@extends('layouts.app')

@section('title', 'Manajemen Paket & Subscription')

@section('content')
<div class="space-y-6" x-data="adminPlansManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">ADMINISTRATOR</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">PLANS &amp; SUBSCRIPTIONS</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-navy">Manajemen Paket (Plan)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Buat paket langganan dan atur limit pesan serta jumlah device per paket. Limit user akan otomatis mengikuti paket yang ditetapkan.</p>
        </div>

        <button @click="openCreateModal()" class="btn-xl btn-primary text-xs py-2.5 px-4 flex items-center gap-2 cursor-pointer">
            <i data-lucide="package-plus" class="w-4 h-4"></i>
            <span>Tambah Paket</span>
        </button>
    </div>

    @if(session('success'))
        <div class="app-card p-3.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-xs font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="app-card p-3.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-xs font-semibold text-rose-700 dark:text-rose-400 flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Plans Grid -->
    @if($plans->isEmpty())
        <div class="app-card p-10 text-center space-y-3 bg-white dark:bg-[#111A2E]">
            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-2xl mx-auto flex items-center justify-center">
                <i data-lucide="package" class="w-7 h-7"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Belum Ada Paket</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium max-w-sm mx-auto">
                    Buat paket langganan pertama Anda untuk mengatur limit device dan pesan per user secara terpusat.
                </p>
            </div>
            <button @click="openCreateModal()" class="app-btn app-btn-primary text-xs py-2 px-4 inline-flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Buat Paket Pertama</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($plans as $plan)
                <div class="app-card p-5 space-y-4 bg-white dark:bg-[#111A2E] relative overflow-hidden {{ $plan->is_default ? 'ring-2 ring-blue-500/50' : ($plan->slug === 'admin' ? 'ring-2 ring-violet-500/50' : '') }}">
                    @if($plan->slug === 'admin')
                        <div class="absolute top-0 right-0 bg-violet-600 text-white text-[9px] font-extrabold px-2.5 py-1 rounded-bl-lg tracking-wide uppercase">
                            SISTEM
                        </div>
                    @elseif($plan->is_default)
                        <div class="absolute top-0 right-0 bg-blue-600 text-white text-[9px] font-extrabold px-2.5 py-1 rounded-bl-lg tracking-wide uppercase">
                            DEFAULT
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl {{ $plan->price > 0 ? 'bg-gradient-to-tr from-amber-400 to-orange-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }} flex items-center justify-center flex-shrink-0">
                                <i data-lucide="{{ $plan->price > 0 ? 'crown' : 'gift' }}" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white leading-tight">{{ $plan->name }}</h3>
                                <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500 uppercase">{{ $plan->slug }}</span>
                            </div>
                        </div>
                        @if($plan->description)
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <div class="text-center py-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="font-extrabold text-xl {{ $plan->price > 0 ? 'text-slate-900 dark:text-white' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $plan->formatPrice() }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block mt-0.5">per {{ $plan->duration_days }} hari</span>
                    </div>

                    <div class="space-y-2 text-xs font-medium">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i data-lucide="smartphone" class="w-3.5 h-3.5 text-blue-500"></i>
                                Limit Device
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $plan->formatDeviceLimit() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i data-lucide="message-square" class="w-3.5 h-3.5 text-indigo-500"></i>
                                Limit Pesan/Hari
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $plan->formatMessageLimit() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-violet-500"></i>
                                Limit Pesan/Bulan
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $plan->formatMonthlyMessageLimit() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-emerald-500"></i>
                                Pelanggan Aktif
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $plan->users_count }} User</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="app-tag text-[9px] {{ $plan->is_active ? 'app-tag-emerald' : 'app-tag-slate' }}">
                            {{ $plan->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="openEditModal(@js($plan))" class="app-btn app-btn-secondary text-[11px] py-1 px-2.5 cursor-pointer">
                                <i data-lucide="edit-3" class="w-3 h-3 text-blue-600"></i>
                                <span>Edit</span>
                            </button>
                            <button type="button" @click="$confirm({
                                title: 'Hapus Paket',
                                message: 'Yakin ingin menghapus paket \'{{ $plan->name }}\'? User yang terkait akan dilepas dari paket ini dan kembali ke limit manual.',
                                confirmText: 'Hapus Paket',
                                type: 'danger',
                                onConfirm: () => document.getElementById('delete-plan-{{ $plan->id }}').submit()
                            })" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer" title="Hapus Paket">
                                <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                            </button>
                            <form id="delete-plan-{{ $plan->id }}" method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ================= CREATE PLAN MODAL ================= -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-base text-navy">Tambah Paket Baru</h3>
                <button @click="showCreateModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-3.5">
                @csrf
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Paket</label>
                    <input type="text" name="name" required placeholder="Contoh: Premium / Business" class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Fitur unggulan paket ini..." class="input-text text-xs"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Harga (Rp)</label>
                        <input type="number" name="price" value="0" min="0" class="input-text text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi 0 untuk paket gratis.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Durasi (Hari)</label>
                        <input type="number" name="duration_days" value="30" min="1" max="3650" class="input-text text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Device</label>
                        <input type="number" name="device_limit" value="3" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">0 = unlimited.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Hari</label>
                        <input type="number" name="daily_message_limit" value="500" min="0" max="10000000" class="input-text text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">0 = unlimited.</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Bulan</label>
                    <input type="number" name="monthly_message_limit" value="0" min="0" max="100000000" class="input-text text-xs font-mono font-bold">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Total kuota bulanan. Isi 0 untuk unlimited.</p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Urutan</label>
                        <input type="number" name="sort_order" value="0" min="0" class="input-text text-xs font-mono font-bold">
                    </div>
                    <div class="space-y-1.5 flex items-end">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                            Aktif
                        </label>
                    </div>
                    <div class="space-y-1.5 flex items-end">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                            Default
                        </label>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="showCreateModal = false" class="app-btn app-btn-secondary text-xs py-2 px-3.5">
                        Batal
                    </button>
                    <button type="submit" class="app-btn app-btn-primary py-2 px-4 text-xs flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Simpan Paket</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT PLAN MODAL ================= -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white dark:bg-[#111A2E] max-w-md w-full p-6 space-y-4 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100 dark:border-slate-800 custom-scrollbar" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="font-bold text-base text-navy">Edit Paket</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="editForm.slug"></p>
                </div>
                <button @click="showEditModal = false" class="p-1 text-slate-400 dark:text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'/anjayadminwkwk/plans/' + editForm.id" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Nama Paket</label>
                    <input type="text" name="name" x-model="editForm.name" required class="input-text text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="2" x-model="editForm.description" class="input-text text-xs"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Harga (Rp)</label>
                        <input type="number" name="price" x-model="editForm.price" min="0" class="input-text text-xs font-mono font-bold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Durasi (Hari)</label>
                        <input type="number" name="duration_days" x-model="editForm.duration_days" min="1" max="3650" class="input-text text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Device</label>
                        <input type="number" name="device_limit" x-model="editForm.device_limit" min="0" max="100000" class="input-text text-xs font-mono font-bold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Hari</label>
                        <input type="number" name="daily_message_limit" x-model="editForm.daily_message_limit" min="0" max="10000000" class="input-text text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Limit Pesan / Bulan</label>
                    <input type="number" name="monthly_message_limit" x-model="editForm.monthly_message_limit" min="0" max="100000000" class="input-text text-xs font-mono font-bold">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Urutan</label>
                        <input type="number" name="sort_order" x-model="editForm.sort_order" min="0" class="input-text text-xs font-mono font-bold">
                    </div>
                    <div class="space-y-1.5 flex items-end">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" :checked="editForm.is_active" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                            Aktif
                        </label>
                    </div>
                    <div class="space-y-1.5 flex items-end">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" :checked="editForm.is_default" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                            Default
                        </label>
                    </div>
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
function adminPlansManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editForm: {
            id: null,
            name: '',
            slug: '',
            description: '',
            price: 0,
            duration_days: 30,
            device_limit: 3,
            daily_message_limit: 500,
            monthly_message_limit: 0,
            is_active: true,
            is_default: false,
            sort_order: 0
        },

        openCreateModal() {
            this.showCreateModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        openEditModal(plan) {
            this.editForm = {
                id: plan.id,
                name: plan.name,
                slug: plan.slug,
                description: plan.description || '',
                price: plan.price ?? 0,
                duration_days: plan.duration_days ?? 30,
                device_limit: plan.device_limit ?? 3,
                daily_message_limit: plan.daily_message_limit ?? 500,
                monthly_message_limit: plan.monthly_message_limit ?? 0,
                is_active: !!plan.is_active,
                is_default: !!plan.is_default,
                sort_order: plan.sort_order ?? 0
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
