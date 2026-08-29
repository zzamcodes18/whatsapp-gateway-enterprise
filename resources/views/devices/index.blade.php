@extends('layouts.app')

@section('title', 'Perangkat WhatsApp')

@section('content')
<div class="space-y-6" x-data="deviceManager(@js($devices->items()))" x-init="init()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">MULTI-DEVICE ENGINE</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500">UUIDv4 SESSIONS</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-slate-900">Perangkat WhatsApp</h1>
            <p class="text-xs text-slate-500 font-medium">
                Kelola sesi nomor WhatsApp Anda. Kuota: <strong class="text-slate-800" x-text="devicesList.length"></strong> / <strong class="text-slate-800">{{ auth()->user()->device_limit > 0 ? auth()->user()->device_limit . ' Unit' : 'Unlimited' }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="refreshDeviceList()" class="app-btn app-btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 cursor-pointer" title="Perbarui Status Sesi">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-600" :class="isRefreshingList ? 'animate-spin' : ''"></i>
                <span class="hidden sm:inline">Refresh Data</span>
            </button>

            <button @click="openModal()" class="app-btn app-btn-primary text-xs py-2 px-4 flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah Device Baru</span>
            </button>
        </div>
    </div>

    <!-- Device List Grid -->
    <div class="space-y-4">
        <!-- Empty State -->
        <template x-if="devicesList.length === 0">
            <div class="app-card p-12 text-center space-y-4 bg-white">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl mx-auto flex items-center justify-center shadow-2xs">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-base text-slate-900">Belum Ada Perangkat WhatsApp</h3>
                    <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto">
                        Mulai dengan menambahkan perangkat baru. Pilih metode <strong>Scan QR Code</strong> atau <strong>Pairing Code 8-Digit</strong>.
                    </p>
                </div>
                <button @click="openModal()" class="app-btn app-btn-primary text-xs py-2.5 px-4 inline-flex items-center gap-2 cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Hubungkan Device Sekarang</span>
                </button>
            </div>
        </template>

        <!-- Devices Grid -->
        <template x-if="devicesList.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="device in devicesList" :key="device.id">
                    <div class="app-card app-card-hover p-5 bg-white space-y-4 flex flex-col justify-between border-slate-200/80">
                        
                        <!-- Top Header -->
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm"
                                     :class="{
                                        'bg-emerald-50 text-emerald-600 border border-emerald-100': device.status === 'connected',
                                        'bg-amber-50 text-amber-600 border border-amber-100': device.status === 'connecting' || device.status === 'qr_ready' || device.status === 'pairing_ready',
                                        'bg-rose-50 text-rose-600 border border-rose-100': device.status === 'disconnected'
                                     }">
                                    <i class="fa-brands fa-whatsapp text-lg"></i>
                                </div>
                                <span class="app-tag text-[10px]"
                                      :class="{
                                        'app-tag-emerald': device.status === 'connected',
                                        'app-tag-amber': device.status === 'connecting' || device.status === 'qr_ready' || device.status === 'pairing_ready',
                                        'app-tag-rose': device.status === 'disconnected'
                                      }">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                            'bg-emerald-500 animate-pulse': device.status === 'connected',
                                            'bg-amber-500 animate-ping': device.status === 'connecting' || device.status === 'qr_ready' || device.status === 'pairing_ready',
                                            'bg-rose-500': device.status === 'disconnected'
                                          }"></span>
                                    <span x-text="device.status ? device.status.toUpperCase() : 'UNKNOWN'"></span>
                                </span>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="font-bold text-sm text-slate-900 truncate" x-text="device.name"></h3>
                                    <button type="button" @click="openEditModal(device)" class="text-slate-400 hover:text-blue-600 p-1 rounded-lg transition-colors cursor-pointer" title="Edit Nama Device">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                <div class="font-mono text-xs text-slate-700 font-semibold mt-1 flex items-center gap-1.5">
                                    <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                                    <span x-text="device.phone_number ? '+' + device.phone_number : 'Nomor Belum Ditautkan'"></span>
                                </div>
                            </div>

                            <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl space-y-1 font-mono text-[11px]">
                                <div class="flex justify-between text-slate-500">
                                    <span>Metode:</span>
                                    <strong class="text-slate-800 uppercase" x-text="device.connection_type"></strong>
                                </div>
                                <div class="flex justify-between text-slate-500 items-center">
                                    <span>UUID Session:</span>
                                    <strong class="text-slate-800 truncate max-w-[130px]" x-text="device.session_id" :title="device.session_id"></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a :href="'/devices/' + device.id" class="flex-1 app-btn app-btn-secondary text-[11px] py-1.5 text-center">
                                Detail
                            </a>

                            <!-- If Connected: Disconnect -->
                            <template x-if="device.status === 'connected'">
                                <button type="button" @click="confirmDisconnect(device)" class="app-btn app-btn-soft-danger text-[11px] py-1.5 px-3 cursor-pointer">
                                    Disconnect
                                </button>
                            </template>

                            <!-- If Not Connected: Quick Connect / View Auth -->
                            <template x-if="device.status !== 'connected'">
                                <button type="button" @click="resumeConnection(device)" class="app-btn app-btn-soft-blue text-[11px] py-1.5 px-3 cursor-pointer">
                                    Hubungkan
                                </button>
                            </template>

                            <!-- Delete Button -->
                            <button type="button" @click="confirmDelete(device)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus Device">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>

                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- ================= ADD DEVICE MODAL ================= -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white max-w-md w-full p-6 space-y-5 max-h-[92vh] overflow-y-auto shadow-2xl border-slate-100" @click.away="closeModal()">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="app-tag app-tag-blue text-[9px]">CONNECT SESSION</span>
                    <h3 class="font-bold text-lg mt-0.5 text-slate-900">Tambah Device WhatsApp</h3>
                </div>
                <button @click="closeModal()" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Step 1: Setup Form -->
            <div x-show="step === 'form'" class="space-y-4">
                <!-- Method Selection Tabs -->
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-xl">
                    <button type="button" @click="connectionType = 'pairing_code'" :class="connectionType === 'pairing_code' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'bg-transparent text-slate-600 font-semibold'" class="py-2 text-xs rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                        <span>Pairing Code</span>
                    </button>
                    <button type="button" @click="connectionType = 'qr'" :class="connectionType === 'qr' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'bg-transparent text-slate-600 font-semibold'" class="py-2 text-xs rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                        <span>Scan QR Code</span>
                    </button>
                </div>

                <!-- Device Name -->
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nama Perangkat</label>
                    <input type="text" x-model="deviceName" placeholder="Contoh: CS Utama Jakarta" class="app-input text-xs">
                </div>

                <!-- Phone Number (Required for pairing_code) -->
                <div x-show="connectionType === 'pairing_code'" class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nomor WhatsApp Ponsel Anda</label>
                    <input type="text" x-model="phoneNumber" placeholder="628123456789 atau 08123456789" class="app-input text-xs font-mono font-bold">
                    <p class="text-[11px] text-slate-500 font-medium">Gunakan format internasional (contoh: 6281234567890).</p>
                </div>

                <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl text-xs space-y-1">
                    <p class="font-bold text-blue-950" x-text="connectionType === 'pairing_code' ? 'Alur Pairing Code:' : 'Alur Scan QR:'"></p>
                    <p class="text-slate-600 text-[11px] leading-relaxed" x-text="connectionType === 'pairing_code' ? 'Server akan meminta 8 digit kode dari WhatsApp untuk dimasukkan pada menu Tautkan Perangkat di aplikasi HP.' : 'Server akan merender QR Code live untuk dipindai melalui kamera WhatsApp ponsel Anda.'"></p>
                </div>

                <div class="pt-1">
                    <button type="button" @click="submitNewDevice()" :disabled="isLoading" class="w-full app-btn app-btn-primary py-2.5 text-xs flex items-center justify-center gap-2 cursor-pointer">
                        <span x-show="!isLoading">Mulai Sesi WhatsApp &rarr;</span>
                        <span x-show="isLoading" class="flex items-center gap-2">
                            <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                            <span>Memproses sesi Baileys Engine...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Step 2: Live Screen with Shimmer Skeleton -->
            <div x-show="step === 'connected_view'" class="space-y-4" style="display: none;">
                
                <!-- Pairing Code Screen -->
                <div x-show="connectionType === 'pairing_code'" class="text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        <span class="font-mono text-xs font-bold text-slate-700">KODE PAIRING WHATSAPP</span>
                    </div>

                    <div class="p-5 bg-gradient-to-b from-blue-50/50 to-slate-50 border border-blue-100 rounded-2xl space-y-3">
                        <template x-if="activePairingCode">
                            <div class="flex items-center justify-center">
                                <div class="px-6 py-3 bg-white border border-blue-200 rounded-xl font-mono font-extrabold text-2xl sm:text-3xl tracking-widest text-blue-700 shadow-sm" x-text="activePairingCode">
                                </div>
                            </div>
                        </template>
                        <template x-if="!activePairingCode">
                            <div class="w-52 h-14 app-shimmer rounded-xl mx-auto border border-slate-200 flex items-center justify-center text-xs font-mono font-semibold text-slate-400">
                                Meminta kode 8-digit...
                            </div>
                        </template>

                        <div class="pt-1" x-show="activePairingCode">
                            <button type="button" @click="copyPairingCode()" class="app-btn app-btn-secondary text-xs py-1.5 px-3.5 inline-flex items-center gap-1.5">
                                <i data-lucide="copy" class="w-3.5 h-3.5 text-blue-600"></i>
                                <span>Salin Kode</span>
                            </button>
                        </div>
                    </div>

                    <div class="text-left p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs space-y-1.5">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i>
                            <span>Langkah di Ponsel:</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1 text-slate-600 font-medium text-[11px]">
                            <li>Buka WhatsApp di HP &rarr; Menu &rarr; <strong>Perangkat Tertaut</strong>.</li>
                            <li>Pilih <strong>Tautkan dengan nomor telepon</strong>.</li>
                            <li>Ketik 8-digit kode di atas.</li>
                        </ol>
                    </div>
                </div>

                <!-- QR Screen -->
                <div x-show="connectionType === 'qr'" class="text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        <span class="font-mono text-xs font-bold text-slate-700">SCAN QR CODE DENGAN WHATSAPP</span>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col items-center justify-center">
                        <template x-if="activeQrCode">
                            <div class="w-52 h-52 bg-white border border-slate-200 rounded-xl p-2.5 shadow-sm flex items-center justify-center">
                                <img :src="activeQrCode" alt="WhatsApp QR Code" class="w-full h-full object-contain">
                            </div>
                        </template>
                        <template x-if="!activeQrCode">
                            <div class="w-52 h-52 app-shimmer rounded-xl border border-slate-200 flex flex-col items-center justify-center p-3">
                                <i data-lucide="loader" class="w-6 h-6 animate-spin text-slate-400"></i>
                                <span class="text-[11px] font-mono font-bold mt-2 text-slate-500">Memuat QR Code...</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Status Footer -->
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" :class="currentStatus === 'connected' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                        <span class="font-mono text-slate-600">Status: <strong x-text="currentStatus.toUpperCase()" :class="currentStatus === 'connected' ? 'text-emerald-700' : 'text-slate-900'"></strong></span>
                    </div>
                    <button type="button" @click="closeModal()" class="app-btn app-btn-secondary text-[11px] py-1 px-3">
                        Selesai
                    </button>
                </div>

            </div>

        </div>
    </div>

    <!-- ================= EDIT DEVICE MODAL ================= -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" style="display: none;" x-cloak>
        <div class="app-card bg-white max-w-sm w-full p-6 space-y-4 shadow-2xl border-slate-100" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="font-bold text-base text-slate-900">Edit Informasi Device</h3>
                <button @click="showEditModal = false" class="p-1 text-slate-400 hover:text-slate-800 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form @submit.prevent="submitEditDevice()" class="space-y-3.5">
                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nama Perangkat</label>
                    <input type="text" x-model="editForm.name" required class="app-input text-xs">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-600">Nomor Telepon</label>
                    <input type="text" x-model="editForm.phone_number" placeholder="628123456789" class="app-input text-xs font-mono">
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="app-btn app-btn-secondary text-xs py-1.5 px-3">
                        Batal
                    </button>
                    <button type="submit" :disabled="isUpdating" class="app-btn app-btn-primary text-xs py-1.5 px-4">
                        <span x-show="!isUpdating">Simpan</span>
                        <span x-show="isUpdating">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function deviceManager(initialDevices = []) {
    return {
        devicesList: initialDevices,
        showModal: false,
        showEditModal: false,
        step: 'form',
        connectionType: 'pairing_code',
        deviceName: '',
        phoneNumber: '',
        isLoading: false,
        isUpdating: false,
        isRefreshingList: false,
        createdDeviceId: null,
        activePairingCode: '',
        activeQrCode: '',
        currentStatus: 'connecting',
        pollInterval: null,
        editForm: {
            id: null,
            name: '',
            phone_number: ''
        },

        init() {
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 100);
        },

        openModal() {
            this.showModal = true;
            this.step = 'form';
            this.deviceName = 'WhatsApp Gateway ' + (Math.floor(Math.random() * 90) + 10);
            this.phoneNumber = '';
            this.activePairingCode = '';
            this.activeQrCode = '';
            this.currentStatus = 'connecting';
            this.isLoading = false;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        closeModal() {
            this.showModal = false;
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
            this.refreshDeviceList();
        },

        openEditModal(device) {
            this.editForm = {
                id: device.id,
                name: device.name,
                phone_number: device.phone_number || ''
            };
            this.showEditModal = true;
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        async submitEditDevice() {
            if (!this.editForm.name.trim()) {
                window.$toast.error('Nama perangkat tidak boleh kosong.');
                return;
            }

            this.isUpdating = true;
            try {
                const res = await axios.put(`/devices/${this.editForm.id}`, {
                    name: this.editForm.name,
                    phone_number: this.editForm.phone_number
                });

                if (res.data && res.data.success) {
                    window.$toast.success(res.data.message || 'Perangkat berhasil diperbarui.');
                    const idx = this.devicesList.findIndex(d => d.id === this.editForm.id);
                    if (idx !== -1) {
                        this.devicesList[idx].name = this.editForm.name;
                        this.devicesList[idx].phone_number = this.editForm.phone_number;
                    }
                    this.showEditModal = false;
                }
            } catch (err) {
                window.$toast.error(err.response?.data?.message || 'Gagal memperbarui perangkat.');
            } finally {
                this.isUpdating = false;
            }
        },

        async submitNewDevice() {
            if (!this.deviceName.trim()) {
                window.$toast.error('Silakan isi nama perangkat.');
                return;
            }
            if (this.connectionType === 'pairing_code' && !this.phoneNumber.trim()) {
                window.$toast.error('Nomor WhatsApp wajib diisi untuk mode Pairing Code.');
                return;
            }

            this.isLoading = true;

            try {
                const response = await axios.post('{{ route("devices.store") }}', {
                    name: this.deviceName,
                    connection_type: this.connectionType,
                    phone_number: this.phoneNumber,
                });

                if (response.data && response.data.success) {
                    const dev = response.data.device;
                    this.createdDeviceId = dev.id;
                    this.activePairingCode = dev.pairing_code || '';
                    this.activeQrCode = dev.qr_code || '';
                    this.currentStatus = dev.status;

                    // Automatically prepend to local list so user sees it right away!
                    const exists = this.devicesList.some(d => d.id === dev.id);
                    if (!exists) {
                        this.devicesList.unshift(dev);
                    }

                    this.step = 'connected_view';
                    this.isLoading = false;

                    window.$toast.info('Sesi WhatsApp dibuat. Menunggu otentikasi...');
                    this.startPolling(dev.id);
                    setTimeout(() => {
                        if (window.renderLucide) window.renderLucide();
                    }, 50);
                } else {
                    window.$toast.error(response.data.message || 'Gagal memulai device.');
                    this.isLoading = false;
                }
            } catch (err) {
                console.error(err);
                window.$toast.error(err.response?.data?.message || 'Terjadi kesalahan sistem.');
                this.isLoading = false;
            }
        },

        resumeConnection(device) {
            this.createdDeviceId = device.id;
            this.connectionType = device.connection_type;
            this.deviceName = device.name;
            this.activePairingCode = device.pairing_code || '';
            this.activeQrCode = device.qr_code || '';
            this.currentStatus = device.status;
            this.step = 'connected_view';
            this.showModal = true;
            this.startPolling(device.id);
            setTimeout(() => {
                if (window.renderLucide) window.renderLucide();
            }, 50);
        },

        startPolling(deviceId) {
            if (this.pollInterval) clearInterval(this.pollInterval);

            this.pollInterval = setInterval(async () => {
                try {
                    const res = await axios.get(`/devices/${deviceId}/status`);
                    if (res.data && res.data.success) {
                        const dev = res.data.device;
                        this.currentStatus = dev.status;

                        if (dev.pairing_code) {
                            this.activePairingCode = dev.pairing_code;
                        }
                        if (dev.qr_code) {
                            this.activeQrCode = dev.qr_code;
                        }

                        // Update in local devicesList
                        const idx = this.devicesList.findIndex(d => d.id === deviceId);
                        if (idx !== -1) {
                            this.devicesList[idx].status = dev.status;
                            if (dev.phone_number) this.devicesList[idx].phone_number = dev.phone_number;
                        }

                        if (dev.status === 'connected') {
                            clearInterval(this.pollInterval);
                            window.$toast.success('WhatsApp Berhasil Terhubung!');
                            setTimeout(() => {
                                this.showModal = false;
                                this.refreshDeviceList();
                            }, 1200);
                        }
                    }
                } catch (e) {
                    console.warn('Poll error:', e);
                }
            }, 3000);
        },

        copyPairingCode() {
            if (!this.activePairingCode) return;
            window.copyToClipboard(this.activePairingCode, `Kode '${this.activePairingCode}' berhasil disalin!`);
        },

        confirmDisconnect(device) {
            window.$confirm({
                title: 'Putuskan Koneksi WhatsApp',
                message: `Apakah Anda yakin ingin memutuskan sesi WhatsApp pada '${device.name}'?`,
                confirmText: 'Disconnect',
                type: 'danger',
                onConfirm: async () => {
                    try {
                        const res = await axios.post(`/devices/${device.id}/disconnect`);
                        if (res.data && res.data.success) {
                            window.$toast.success(res.data.message);
                            const idx = this.devicesList.findIndex(d => d.id === device.id);
                            if (idx !== -1) this.devicesList[idx].status = 'disconnected';
                        }
                    } catch (e) {
                        window.$toast.error('Gagal memutuskan koneksi.');
                    }
                }
            });
        },

        confirmDelete(device) {
            window.$confirm({
                title: 'Hapus Perangkat',
                message: `Apakah Anda yakin ingin menghapus perangkat '${device.name}' secara permanen?`,
                confirmText: 'Hapus Permanen',
                type: 'danger',
                onConfirm: async () => {
                    try {
                        const res = await axios.delete(`/devices/${device.id}`);
                        if (res.data && res.data.success) {
                            window.$toast.success(res.data.message);
                            this.devicesList = this.devicesList.filter(d => d.id !== device.id);
                        }
                    } catch (e) {
                        window.$toast.error('Gagal menghapus perangkat.');
                    }
                }
            });
        },

        async refreshDeviceList() {
            this.isRefreshingList = true;
            try {
                const res = await axios.get('{{ route("devices.index") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.data && res.data.devices) {
                    this.devicesList = res.data.devices.data || res.data.devices;
                    window.$toast.info('Data perangkat berhasil diperbarui.');
                }
            } catch (e) {
                console.warn('Failed to refresh list:', e);
            } finally {
                this.isRefreshingList = false;
                setTimeout(() => {
                    if (window.renderLucide) window.renderLucide();
                }, 50);
            }
        }
    }
}
</script>
@endsection
