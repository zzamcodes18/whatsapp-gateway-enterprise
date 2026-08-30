@extends('layouts.app')

@section('title', 'Template Pesan WhatsApp')

@section('content')
<div class="space-y-6" x-data="templateManager()">

    <!-- Toast Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/80 dark:border-emerald-500/20 rounded-2xl flex items-center justify-between gap-3 text-emerald-800 dark:text-emerald-300 text-sm shadow-xs">
            <div class="flex items-center gap-2.5 font-medium">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200/80 dark:border-rose-500/20 rounded-2xl space-y-1 text-rose-800 dark:text-rose-300 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                <span>Terjadi Kesalahan:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="app-tag app-tag-blue">AUTOMATION READY</span>
                <span class="font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">MESSAGE TEMPLATES</span>
            </div>
            <h1 class="font-extrabold text-xl sm:text-2xl mt-1 text-slate-900 dark:text-white">Template Pesan WhatsApp</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola template pesan dinamis dengan parameter <code>{otp}</code>, <code>{name}</code>, dan Tombol Interaktif.</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="app-btn app-btn-primary text-xs py-2 px-4 flex items-center gap-2 cursor-pointer shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Template Baru</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('templates.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau isi template..." class="w-full pl-9 pr-4 py-2 text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
        </div>
        @if(request('search'))
            <a href="{{ route('templates.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium flex items-center gap-1">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                <span>Reset Pencarian</span>
            </a>
        @endif
    </form>

    <!-- Templates Grid -->
    @if($templates->isEmpty())
        <div class="bg-white dark:bg-[#111A2E] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-12 text-center space-y-4">
            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto text-slate-400 dark:text-slate-500">
                <i data-lucide="file-code-2" class="w-7 h-7"></i>
            </div>
            <div class="max-w-sm mx-auto space-y-1">
                <h3 class="font-extrabold text-slate-800 dark:text-white text-base">Belum Ada Template Pesan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Buat template pertama Anda untuk mempercepat pengiriman OTP, promosi, atau notifikasi via REST API.</p>
            </div>
            <button @click="openCreateModal()" class="app-btn app-btn-primary text-xs py-2 px-4 inline-flex items-center gap-2 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Template Pertama</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($templates as $tpl)
                <div class="bg-white dark:bg-[#111A2E] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 space-y-4 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-2xs">
                    
                    <!-- Card Header -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-500/20">
                                @switch($tpl->category)
                                    @case('otp') <i data-lucide="key" class="w-3 h-3"></i> OTP @break
                                    @case('promo') <i data-lucide="tag" class="w-3 h-3"></i> Promo @break
                                    @case('notification') <i data-lucide="bell" class="w-3 h-3"></i> Notifikasi @break
                                    @case('button') <i data-lucide="mouse-pointer-click" class="w-3 h-3"></i> Interactive @break
                                    @default <i data-lucide="file-text" class="w-3 h-3"></i> General
                                @endswitch
                                {{ strtoupper($tpl->category) }}
                            </span>

                            <!-- Template ID Badge -->
                            <button @click="copyText('{{ $tpl->id }}')" class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center gap-1 transition-colors group cursor-pointer" title="Klik untuk Salin Template ID">
                                <span>ID: #{{ $tpl->id }}</span>
                                <i data-lucide="copy" class="w-3 h-3 text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300"></i>
                            </button>
                        </div>

                        <h3 class="font-bold text-slate-900 dark:text-white text-base line-clamp-1">{{ $tpl->name }}</h3>
                    </div>

                    <!-- Template Content Body Box -->
                    <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 rounded-xl p-3.5 space-y-2 text-xs text-slate-700 dark:text-slate-300 font-sans">
                        @if($tpl->title)
                            <div class="font-bold text-slate-900 dark:text-white border-b border-slate-200/60 dark:border-slate-700/60 pb-1 text-[11px]">
                                {{ $tpl->title }}
                            </div>
                        @endif

                        <div class="whitespace-pre-line leading-relaxed text-[11px]">
                            {!! preg_replace('/(\{[\w]+\})/', '<span class="px-1 py-0.5 rounded bg-blue-100 dark:bg-blue-500/15 text-blue-800 dark:text-blue-300 font-mono font-bold">$1</span>', e($tpl->content)) !!}
                        </div>

                        @if($tpl->footer)
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium italic border-t border-slate-200/60 dark:border-slate-700/60 pt-1">
                                {{ $tpl->footer }}
                            </div>
                        @endif

                        <!-- Buttons Badge Preview -->
                        @if(!empty($tpl->buttons))
                            <div class="pt-2 flex flex-wrap gap-1.5">
                                @foreach($tpl->buttons as $btn)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shadow-2xs">
                                        @if(($btn['type'] ?? '') === 'url') <i data-lucide="external-link" class="w-3 h-3 text-blue-600"></i>
                                        @elseif(($btn['type'] ?? '') === 'copy') <i data-lucide="copy" class="w-3 h-3 text-emerald-600"></i>
                                        @elseif(($btn['type'] ?? '') === 'call') <i data-lucide="phone" class="w-3 h-3 text-purple-600"></i>
                                        @else <i data-lucide="corner-down-left" class="w-3 h-3 text-slate-500"></i>
                                        @endif
                                        <span>{{ $btn['text'] ?? 'Button' }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Card Actions Footer -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <button @click="openTestModal({{ json_encode($tpl) }})" class="px-3 py-1.5 text-[11px] font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Test Kirim</span>
                        </button>

                        <div class="flex items-center gap-1">
                            <button @click="openEditModal({{ json_encode($tpl) }})" class="p-1.5 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer" title="Edit Template">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            
                            <form method="POST" action="{{ route('templates.destroy', $tpl->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template \'{{ addslashes($tpl->name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer" title="Hapus Template">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    @endif

    <!-- ========================= CREATE / EDIT MODAL ========================= -->
    <div x-show="showFormModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="showFormModal = false"></div>

            <div class="inline-block w-full max-w-4xl my-auto overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#111A2E] shadow-2xl rounded-2xl relative z-10">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold">
                            <i data-lucide="file-code-2" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base" x-text="isEditMode ? 'Edit Template Pesan #' + formData.id : 'Buat Template Pesan Baru'"></h3>
                    </div>
                    <button @click="showFormModal = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body: Split Form & WhatsApp Live Preview -->
                <form :action="isEditMode ? '{{ url('/templates') }}/' + formData.id : '{{ route('templates.store') }}'" method="POST" class="p-6 max-h-[80vh] overflow-y-auto">
                    @csrf
                    <template x-if="isEditMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Left Column: Form Controls (7 cols) -->
                        <div class="lg:col-span-7 space-y-4">
                            
                            <!-- Category & Name -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                    <select name="category" x-model="formData.category" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                                        <option value="otp">OTP Code</option>
                                        <option value="promo">Promosi</option>
                                        <option value="notification">Notifikasi</option>
                                        <option value="button">Interactive Button</option>
                                        <option value="general">General</option>
                                        <option value="other">Lainnya (Input Manual)</option>
                                    </select>
                                    
                                    <div x-show="formData.category === 'other'" class="mt-2" style="display: none;" x-cloak>
                                        <input type="text" name="custom_category" x-model="formData.custom_category" placeholder="Kategori Kustom (misal: tagihan)" class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none font-semibold text-blue-700 dark:text-blue-400">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Template <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: Verification OTP Login" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                                </div>
                            </div>

                            <!-- Header Title (Optional) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Header (Opsional)</label>
                                <input type="text" name="title" x-model="formData.title" placeholder="Contoh: KODE VERIFIKASI ANDA" class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                            </div>

                            <!-- Body Content Textarea -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan Template <span class="text-rose-500">*</span></label>
                                <textarea name="content" x-model="formData.content" rows="4" required placeholder="Halo, terima kasih telah mendaftar. Kode verifikasi Anda adalah 884920. Berlaku selama 5 menit." class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl p-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none font-sans leading-relaxed"></textarea>
                            </div>

                            <!-- Footer Text -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Catatan / Footer (Opsional)</label>
                                <input type="text" name="footer" x-model="formData.footer" placeholder="Contoh: Pesan ini dikirim secara otomatis oleh sistem." class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                            </div>

                            <!-- Interactive Buttons Builder -->
                            <div class="border-t border-slate-200/80 dark:border-slate-800 pt-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Tombol Interaktif (Interactive Buttons)</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Maksimal 3 tombol interaktif</p>
                                    </div>
                                    <button type="button" @click="addButton()" x-show="formData.buttons.length < 3" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-bold rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>Tambah Tombol</span>
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <template x-for="(btn, index) in formData.buttons" :key="index">
                                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl">
                                            <div class="grid grid-cols-12 gap-2 items-center">
                                                
                                                <!-- Button Type Select -->
                                                <div class="col-span-12 sm:col-span-4">
                                                    <select :name="'buttons[' + index + '][type]'" x-model="btn.type" class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 outline-none font-medium">
                                                        <option value="copy">Copy Code (Salin)</option>
                                                        <option value="reply">Quick Reply</option>
                                                        <option value="url">Link Web (URL)</option>
                                                        <option value="call">Telepon (Call)</option>
                                                    </select>
                                                </div>

                                                <!-- Button Text Input -->
                                                <div class="col-span-12 sm:col-span-4">
                                                    <input type="text" :name="'buttons[' + index + '][text]'" x-model="btn.text" placeholder="Teks Tombol" required class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 outline-none">
                                                </div>

                                                <!-- Dynamic Value Input -->
                                                <div class="col-span-10 sm:col-span-3">
                                                    <template x-if="btn.type === 'copy'">
                                                        <input type="text" :name="'buttons[' + index + '][code]'" x-model="btn.code" placeholder="Kode / 884920" class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 outline-none font-mono">
                                                    </template>
                                                    <template x-if="btn.type === 'url'">
                                                        <input type="text" :name="'buttons[' + index + '][url]'" x-model="btn.url" placeholder="https://..." class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 outline-none font-mono">
                                                    </template>
                                                    <template x-if="btn.type === 'call'">
                                                        <input type="text" :name="'buttons[' + index + '][phone]'" x-model="btn.phone" placeholder="+628123..." class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 outline-none font-mono">
                                                    </template>
                                                    <template x-if="btn.type === 'reply'">
                                                        <input type="text" :name="'buttons[' + index + '][id]'" x-model="btn.id" placeholder="ID (Opsional)" class="w-full text-[11px] bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 outline-none font-mono">
                                                    </template>
                                                </div>

                                                <!-- Remove Button -->
                                                <div class="col-span-2 sm:col-span-1 flex justify-center">
                                                    <button type="button" @click="removeButton(index)" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors cursor-pointer" title="Hapus Tombol">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>

                        <!-- Right Column: WhatsApp Live Preview Simulator (5 cols) -->
                        <div class="lg:col-span-5 bg-slate-900 rounded-2xl p-4 text-white space-y-3 flex flex-col justify-between shadow-inner min-h-[320px]">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-[11px] font-bold text-slate-300">Live Preview WhatsApp</span>
                                    </div>
                                    <span class="text-[10px] font-mono text-slate-400">iOS/Android</span>
                                </div>

                                <!-- WhatsApp Chat Bubble Card -->
                                <div class="bg-[#0b141a] p-3 rounded-2xl border border-slate-800 space-y-2">
                                    <div class="bg-[#202c33] rounded-xl p-3 text-slate-100 text-xs space-y-2 shadow-sm relative">
                                        
                                        <!-- Title Header -->
                                        <div x-show="formData.title" x-text="formData.title" class="font-bold text-white text-xs border-b border-slate-700/60 pb-1"></div>

                                        <!-- Message Body -->
                                        <div class="whitespace-pre-line text-slate-200 text-[11px] leading-relaxed" x-text="formData.content || 'Ketik isi pesan template di sebelah kiri...'"></div>

                                        <!-- Footer Text -->
                                        <div x-show="formData.footer" x-text="formData.footer" class="text-[10px] text-slate-400 italic border-t border-slate-700/60 pt-1"></div>

                                        <!-- Timestamp indicator -->
                                        <div class="text-[9px] text-slate-400 text-right font-mono">12:00 PM ✓✓</div>
                                    </div>

                                    <!-- Rendered Buttons Preview -->
                                    <div class="space-y-1 pt-1">
                                        <template x-for="btn in formData.buttons" :key="btn.text">
                                            <div class="bg-[#202c33] text-emerald-400 text-center py-2 px-3 rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-2 border border-slate-700/40">
                                                <template x-if="btn.type === 'copy'"><span>📋 Salin Kode: <span class="font-mono text-white" x-text="btn.code || btn.text"></span></span></template>
                                                <template x-if="btn.type === 'url'"><span>🔗 <span x-text="btn.text"></span></span></template>
                                                <template x-if="btn.type === 'call'"><span>📞 <span x-text="btn.text"></span></span></template>
                                                <template x-if="btn.type === 'reply'"><span>💬 <span x-text="btn.text"></span></span></template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="text-[10px] text-slate-400 text-center font-mono">
                                Tampilan simulasi pesan WhatsApp
                            </div>
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <button type="button" @click="openTestDraftModal()" class="px-3 py-2 text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-amber-200/80 dark:border-amber-500/20 rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Uji Kirim Draft Pesan</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="showFormModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-5 font-bold flex items-center gap-2 cursor-pointer">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Template'"></span>
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- ========================= TEST SEND MODAL ========================= -->
    <div x-show="showTestModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="showTestModal = false"></div>

            <div class="inline-block w-full max-w-lg my-auto overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#111A2E] shadow-2xl rounded-2xl relative z-10">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Uji Kirim Template Pesan</h3>
                    </div>
                    <button @click="showTestModal = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="'{{ url('/templates') }}/' + activeTemplate.id + '/test'" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700 rounded-xl space-y-1">
                        <div class="text-xs font-bold text-slate-800 dark:text-white" x-text="activeTemplate.name"></div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">Template ID: #<span x-text="activeTemplate.id"></span></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Perangkat WhatsApp Sender</label>
                        <select name="device_id" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                            @foreach($userDevices as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->name }} (+{{ $dev->phone_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Penerima</label>
                        <input type="text" name="phone" placeholder="081234567890" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Isi Nilai Sampel Variabel (Key = Value)</label>
                            <button type="button" @click="testSampleVariables = generateVariablesFromText((activeTemplate.title || '') + ' ' + (activeTemplate.content || '') + ' ' + (activeTemplate.footer || ''))" class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-500/20 cursor-pointer flex items-center gap-1">
                                <span>⚡ Auto-isi Variabel</span>
                            </button>
                        </div>
                        <textarea name="sample_variables" x-model="testSampleVariables" rows="3" placeholder="otp=884920&#10;name=Budi Santoso&#10;code=PROMO2026" class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl p-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none font-mono"></textarea>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">Tulis satu variabel per baris (format <code>nama_var=nilai</code>)</span>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showTestModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-5 font-bold flex items-center gap-2 cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirim Test Pesan Sekarang</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- ========================= TEST DRAFT SEND MODAL (BEFORE SAVE) ========================= -->
    <div x-show="showTestDraftModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="showTestDraftModal = false"></div>

            <div class="inline-block w-full max-w-lg my-auto overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#111A2E] shadow-2xl rounded-2xl relative z-10">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Uji Kirim Draft (Sebelum Disimpan)</h3>
                    </div>
                    <button @click="showTestDraftModal = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('templates.test-draft') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <!-- Hidden payload from draft form -->
                    <input type="hidden" name="title" :value="formData.title">
                    <input type="hidden" name="content" :value="formData.content">
                    <input type="hidden" name="footer" :value="formData.footer">
                    <template x-for="(btn, idx) in formData.buttons" :key="idx">
                        <div>
                            <input type="hidden" :name="'buttons[' + idx + '][type]'" :value="btn.type">
                            <input type="hidden" :name="'buttons[' + idx + '][text]'" :value="btn.text">
                            <input type="hidden" :name="'buttons[' + idx + '][code]'" :value="btn.code">
                            <input type="hidden" :name="'buttons[' + idx + '][url]'" :value="btn.url">
                            <input type="hidden" :name="'buttons[' + idx + '][phone]'" :value="btn.phone">
                            <input type="hidden" :name="'buttons[' + idx + '][id]'" :value="btn.id">
                        </div>
                    </template>

                    <div class="p-3 bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/20 rounded-xl space-y-1">
                        <div class="text-xs font-bold text-amber-900 dark:text-amber-300">Mencoba kirim draft: <span x-text="formData.name || 'Draft Template Baru'"></span></div>
                        <div class="text-[11px] text-amber-700 dark:text-amber-400">Pesan ini dikirim secara langsung tanpa perlu menyimpan template terlebih dahulu.</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Perangkat WhatsApp Sender</label>
                        <select name="device_id" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                            @foreach($userDevices as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->name }} (+{{ $dev->phone_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Penerima</label>
                        <input type="text" name="phone" placeholder="081234567890" required class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Isi Nilai Sampel Variabel (Key = Value)</label>
                            <button type="button" @click="testSampleVariables = generateVariablesFromText((formData.title || '') + ' ' + (formData.content || '') + ' ' + (formData.footer || ''))" class="text-[10px] font-bold text-amber-700 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-500/20 cursor-pointer flex items-center gap-1">
                                <span>⚡ Auto-isi Variabel</span>
                            </button>
                        </div>
                        <textarea name="sample_variables" x-model="testSampleVariables" rows="3" placeholder="otp=884920&#10;name=Budi Santoso&#10;code=PROMO2026" class="w-full text-xs bg-white dark:bg-[#0D1526] border border-slate-200 dark:border-slate-700 rounded-xl p-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none font-mono"></textarea>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">Tulis satu variabel per baris (format <code>nama_var=nilai</code>)</span>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="showTestDraftModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="app-btn app-btn-primary text-xs py-2 px-5 font-bold flex items-center gap-2 cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirim Test Draft</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

<script>
function templateManager() {
    return {
        showFormModal: false,
        showTestModal: false,
        showTestDraftModal: false,
        isEditMode: false,
        activeTemplate: {},
        testSampleVariables: '',
        formData: {
            id: null,
            name: '',
            category: 'otp',
            custom_category: '',
            title: '',
            content: '',
            footer: '',
            buttons: []
        },

        generateVariablesFromText(text) {
            if (!text) text = '';
            const regex = /\{([a-zA-Z0-9_\-\.]+)\}/g;
            const matches = new Set();
            let match;
            while ((match = regex.exec(text)) !== null) {
                matches.add(match[1].trim());
            }

            const varList = Array.from(matches);
            if (varList.length === 0) {
                return "otp=884920\nname=Budi Santoso\ncode=PROMO2026";
            }

            const sampleDefaults = {
                'otp': '884920',
                'code': 'PROMO2026',
                'kode': 'PROMO2026',
                'name': 'Budi Santoso',
                'nama': 'Budi Santoso',
                'phone': '6281234567890',
                'wa': '6281234567890',
                'amount': '150000',
                'total': '150000',
                'nominal': '150000',
                'harga': '150000',
                'link': 'https://transaksikita.com',
                'url': 'https://transaksikita.com'
            };

            return varList.map(k => {
                const lowerK = k.toLowerCase();
                const defaultVal = sampleDefaults[lowerK] || ('Nilai ' + k);
                return `${k}=${defaultVal}`;
            }).join('\n');
        },

        openCreateModal() {
            this.isEditMode = false;
            this.formData = {
                id: null,
                name: '',
                category: 'otp',
                custom_category: '',
                title: '',
                content: '',
                footer: '',
                buttons: [
                    { type: 'copy', text: 'Salin Kode', code: '884920' }
                ]
            };
            this.showFormModal = true;
        },

        openEditModal(template) {
            this.isEditMode = true;
            const standardCategories = ['otp', 'promo', 'notification', 'button', 'general'];
            const isStandard = standardCategories.includes(template.category);

            this.formData = {
                id: template.id,
                name: template.name,
                category: isStandard ? template.category : 'other',
                custom_category: isStandard ? '' : template.category,
                title: template.title || '',
                content: template.content,
                footer: template.footer || '',
                buttons: template.buttons ? JSON.parse(JSON.stringify(template.buttons)) : []
            };
            this.showFormModal = true;
        },

        openTestModal(template) {
            this.activeTemplate = template;
            let fullText = (template.title || '') + ' ' + (template.content || '') + ' ' + (template.footer || '');
            if (template.buttons && template.buttons.length) {
                const btns = typeof template.buttons === 'string' ? JSON.parse(template.buttons) : template.buttons;
                if (Array.isArray(btns)) {
                    btns.forEach(btn => {
                        fullText += ' ' + (btn.text || '') + ' ' + (btn.code || '') + ' ' + (btn.url || '');
                    });
                }
            }
            this.testSampleVariables = this.generateVariablesFromText(fullText);
            this.showTestModal = true;
        },

        openTestDraftModal() {
            if (!this.formData.content) {
                alert('Silakan isi "Isi Pesan Template" terlebih dahulu sebelum melakukan uji kirim.');
                return;
            }
            let fullText = (this.formData.title || '') + ' ' + (this.formData.content || '') + ' ' + (this.formData.footer || '');
            if (this.formData.buttons && this.formData.buttons.length) {
                this.formData.buttons.forEach(btn => {
                    fullText += ' ' + (btn.text || '') + ' ' + (btn.code || '') + ' ' + (btn.url || '');
                });
            }
            this.testSampleVariables = this.generateVariablesFromText(fullText);
            this.showTestDraftModal = true;
        },

        addButton() {
            if (this.formData.buttons.length < 3) {
                this.formData.buttons.push({
                    type: 'copy',
                    text: 'Salin Kode',
                    code: '884920',
                    url: '',
                    phone: '',
                    id: ''
                });
            }
        },

        removeButton(index) {
            this.formData.buttons.splice(index, 1);
        },

        insertTag(tag) {
            this.formData.content += (this.formData.content ? ' ' : '') + tag;
        },

        copyText(text) {
            navigator.clipboard.writeText(text);
            alert('Template ID disalin: #' + text);
        }
    }
}
</script>
@endsection
