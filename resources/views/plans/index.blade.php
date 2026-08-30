@extends('layouts.app')

@section('title', 'Upgrade Paket')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5">
                <i data-lucide="rocket" class="w-6 h-6 text-blue-600"></i>
                Upgrade Paket
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Tingkatkan kuota device dan pesan Anda dengan paket yang lebih besar.
            </p>
        </div>
        @if($currentPlan)
            <div class="app-card px-4 py-3 bg-white dark:bg-[#111A2E] flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="package" class="w-4.5 h-4.5"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Paket Anda Saat Ini</div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        {{ $currentPlan->name }}
                        @if(auth()->user()->hasActivePlan())
                            <span class="app-tag app-tag-emerald text-[9px]">AKTIF</span>
                        @else
                            <span class="app-tag app-tag-rose text-[9px]">EXPIRED</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Notice -->
    <div class="rounded-xl border border-slate-200/80 dark:border-slate-700/60 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/60 dark:to-[#111A2E] p-4 flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-blue-500/25">
            <i data-lucide="credit-card" class="w-4.5 h-4.5"></i>
        </div>
        <div class="space-y-1">
            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Sistem Pembayaran Belum Tersedia</div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Upgrade mandiri melalui halaman ini akan segera hadir setelah metode pembayaran online diintegrasikan.
                Untuk saat ini, silakan hubungi admin untuk upgrade paket secara manual — prosesnya cepat dan gratis biaya admin.
            </p>
        </div>
    </div>

    <!-- Plans Grid -->
    @if($plans->isEmpty())
        <div class="app-card p-10 text-center bg-white dark:bg-[#111A2E]">
            <i data-lucide="package-search" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600"></i>
            <h3 class="mt-3 font-bold text-slate-900 dark:text-white">Belum Ada Paket Tersedia</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Admin belum menambahkan paket apapun. Silakan cek kembali nanti.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                @endphp
                <div class="app-card p-5 space-y-4 bg-white dark:bg-[#111A2E] relative overflow-hidden {{ $isCurrent ? 'ring-2 ring-blue-500/60 shadow-lg shadow-blue-500/10' : 'hover:shadow-md hover:border-slate-200 dark:hover:border-slate-700 transition-all' }}">
                    @if($isCurrent)
                        <div class="absolute top-0 right-0 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[9px] font-extrabold px-2.5 py-1 rounded-bl-lg tracking-wide uppercase shadow-sm">
                            PAKET ANDA
                        </div>
                    @elseif($plan->is_default)
                        <div class="absolute top-0 right-0 bg-slate-500/90 text-white text-[9px] font-extrabold px-2.5 py-1 rounded-bl-lg tracking-wide uppercase">
                            STARTER
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl {{ $plan->price > 0 ? 'bg-gradient-to-tr from-blue-500 to-indigo-600 text-white shadow-sm shadow-indigo-500/25' : 'bg-gradient-to-tr from-emerald-400 to-teal-500 text-white shadow-sm shadow-emerald-500/25' }} flex items-center justify-center flex-shrink-0">
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

                    <div class="text-center py-3.5 bg-gradient-to-b from-slate-50 to-slate-100/50 dark:from-slate-800/60 dark:to-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <span class="font-extrabold text-2xl {{ $plan->price > 0 ? 'bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $plan->formatPrice() }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 block mt-0.5">per {{ $plan->duration_days >= 36500 ? 'permanen' : $plan->duration_days . ' hari' }}</span>
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
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        @if($isCurrent)
                            <button type="button" disabled class="app-btn app-btn-secondary w-full text-xs py-2.5 cursor-not-allowed opacity-60">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Paket Aktif Anda</span>
                            </button>
                        @else
                            <button type="button" disabled class="app-btn app-btn-primary w-full text-xs py-2.5 cursor-not-allowed opacity-60" title="Segera hadir">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                <span>Upgrade — Segera Hadir</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Contact Admin CTA -->
    <div class="app-card p-5 bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg shadow-blue-500/15">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                <i data-lucide="headset" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-sm">Butuh Upgrade Sekarang?</h3>
                <p class="text-xs text-blue-50/90 mt-0.5 leading-relaxed">
                    Hubungi admin untuk aktivasi paket premium secara manual. Sertakan email akun Anda dan pilihan paket.
                </p>
            </div>
        </div>
        <a href="{{ route('pages.support') }}" class="app-btn bg-white text-blue-700 hover:bg-blue-50 text-xs font-bold py-2.5 px-4 flex-shrink-0 shadow-sm">
            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
            <span>Hubungi Support</span>
        </a>
    </div>
</div>
@endsection
