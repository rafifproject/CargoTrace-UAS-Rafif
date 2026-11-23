@extends('layouts.operator')

@section('title', 'Antrean Pesanan Masuk dari Pelanggan')

@push('styles')
<style>
    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 179, 237, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(99, 179, 237, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 179, 237, 0); }
    }
    @keyframes slide-in {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-4px); }
    }
    .pulse-ring  { animation: pulse-ring 2.4s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite; }
    .slide-in    { animation: slide-in 0.35s ease-out both; }
    .float-anim  { animation: float 3s ease-in-out infinite; }
    .card-row:hover { box-shadow: 0 4px 24px -4px rgba(0,168,232,0.10); }
    .kpi-glass {
        background: rgba(255,255,255,0.72);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .dark .kpi-glass {
        background: rgba(10,17,40,0.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .gradient-hero {
        background: linear-gradient(135deg, #034078 0%, #001F54 55%, #0a1128 100%);
    }
    .avatar-initials {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .badge-paid {
        box-shadow: 0 0 8px 0 rgba(52,211,153,0.25);
    }
    .badge-unpaid {
        box-shadow: 0 0 8px 0 rgba(251,191,36,0.25);
    }
</style>
@endpush

@section('content')

{{-- ===== GRADIENT HERO HEADER ===== --}}
<div class="gradient-hero rounded-2xl mb-6 px-6 pt-6 pb-5 relative overflow-hidden shadow-xl">
    {{-- Decorative orbs --}}
    <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-24 w-24 h-24 rounded-full bg-blue-400/10 blur-2xl pointer-events-none"></div>

    <div class="flex flex-wrap items-start justify-between gap-4 relative z-10">
        <div>
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mb-2">
                <a href="{{ route('operator.shipments.index') }}" class="hover:text-cyan-400 transition-colors">Semua Pengiriman</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i>
                <span class="text-slate-300">Antrean Pesanan Masuk</span>
            </div>

            {{-- Title row --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 border border-cyan-400/30 flex items-center justify-center float-anim flex-shrink-0">
                    <i data-lucide="inbox" class="w-5 h-5 text-cyan-300"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white font-sans leading-tight">
                        Antrean Pesanan Masuk
                        <span class="ml-2 align-middle px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-400/20 text-cyan-300 border border-cyan-400/30 tracking-wide">
                            Self-Service Portal
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pesanan ekspor kopi mandiri oleh pelanggan — operator cukup verifikasi &amp; jadwalkan armada, tanpa input ulang.
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('operator.shipments.index') }}"
               class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-white/10 hover:bg-white/20 border border-white/15 transition-all">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                Semua Pengiriman
            </a>
            <a href="{{ route('operator.shipments.create') }}"
               class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-500 hover:bg-cyan-400 shadow-lg shadow-cyan-500/30 transition-all">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                Input Manual (Offline/B2B)
            </a>
        </div>
    </div>

    {{-- KPI Metric Strip --}}
    <div class="grid grid-cols-3 gap-3 mt-5 relative z-10">
        {{-- Menunggu Verifikasi --}}
        <div class="kpi-glass rounded-xl border border-white/10 px-4 py-3 flex items-center gap-3">
            @if($totalPending > 0)
            <div class="w-9 h-9 rounded-full bg-blue-500/20 border border-blue-400/30 flex items-center justify-center pulse-ring flex-shrink-0">
                <i data-lucide="inbox" class="w-4 h-4 text-blue-300"></i>
            </div>
            @else
            <div class="w-9 h-9 rounded-full bg-slate-500/20 border border-slate-400/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="inbox" class="w-4 h-4 text-slate-400"></i>
            </div>
            @endif
            <div>
                <div class="text-2xl font-extrabold text-white font-sans leading-none">{{ $totalPending }}</div>
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Menunggu Verifikasi</div>
                <div class="text-[10px] text-blue-400">Status CREATED &amp; BOOKED</div>
            </div>
        </div>

        {{-- Invoice Lunas --}}
        <div class="kpi-glass rounded-xl border border-white/10 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-check" class="w-4 h-4 text-emerald-300"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-emerald-300 font-sans leading-none">{{ $paidPending }}</div>
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Invoice Lunas</div>
                <div class="text-[10px] text-emerald-400">Siap Otorisasi Dispatch ✓</div>
            </div>
        </div>

        {{-- Menunggu Wire --}}
        <div class="kpi-glass rounded-xl border border-white/10 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-amber-500/20 border border-amber-400/30 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-4 h-4 text-amber-300"></i>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-amber-300 font-sans leading-none">{{ $unpaidPending }}</div>
                <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Menunggu Wire</div>
                <div class="text-[10px] text-amber-400">Pelunasan T/T Telegraphic</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== FILTER BAR ===== --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 px-5 py-4 mb-5 shadow-sm">
    <form method="GET" action="{{ route('operator.shipments.incoming') }}" class="flex flex-wrap gap-3 items-end">
        {{-- Search --}}
        <div class="flex-1 min-w-56">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Cari Pesanan</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="No. Resi, No. Invoice, Pelanggan, Kopi..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-400 placeholder-slate-400 transition-all">
            </div>
        </div>

        {{-- Status Pembayaran --}}
        <div class="min-w-40">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Status Bayar</label>
            <select name="payment_status"
                    class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-400 transition-all">
                <option value="">Semua Status</option>
                <option value="PAID"   {{ request('payment_status') === 'PAID'   ? 'selected' : '' }}>✅ Lunas (Settled)</option>
                <option value="UNPAID" {{ request('payment_status') === 'UNPAID' ? 'selected' : '' }}>⏳ Belum Bayar</option>
            </select>
        </div>

        {{-- Tahap Kargo --}}
        <div class="min-w-40">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Tahap Kargo</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-400 transition-all">
                <option value="">Semua Tahap</option>
                <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>📋 CREATED — Pesanan Baru</option>
                <option value="BOOKED"  {{ request('status') === 'BOOKED'  ? 'selected' : '' }}>📦 BOOKED — Terjadwal</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2 items-end">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold transition-all shadow-sm shadow-cyan-500/20">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                Filter
            </button>
            @if(request()->hasAny(['search','payment_status','status']))
            <a href="{{ route('operator.shipments.incoming') }}"
               class="inline-flex items-center gap-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
               title="Reset filter">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ===== SHIPMENT CARDS LIST ===== --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

    {{-- Table Header Bar --}}
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/30">
        <div class="flex items-center gap-2.5">
            <i data-lucide="list-ordered" class="w-4 h-4 text-cyan-500"></i>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Daftar Antrean Booking Kargo Masuk</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                {{ $shipments->total() }} Pesanan
            </span>
        </div>
        <span class="text-[11px] text-slate-400 hidden sm:block">Klik <strong>Verifikasi</strong> untuk menjadwalkan ke armada pengangkutan</span>
    </div>

    {{-- Card Rows --}}
    <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
        @forelse($shipments as $index => $shipment)
        @php
            $initials = collect(explode(' ', $shipment->customer?->name ?? 'U'))
                ->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
            $avatarColors = [
                'from-cyan-500 to-blue-600',
                'from-violet-500 to-purple-600',
                'from-emerald-500 to-teal-600',
                'from-orange-400 to-amber-500',
                'from-pink-500 to-rose-600',
            ];
            $avatarColor = $avatarColors[$index % count($avatarColors)];
            $isPaid = $shipment->payment_status === 'PAID';
            $isCreated = $shipment->status === 'CREATED';
            $borderColor = $isPaid ? 'border-l-emerald-400' : 'border-l-amber-400';
        @endphp
        <div class="card-row slide-in border-l-4 {{ $borderColor }} bg-white dark:bg-transparent hover:bg-slate-50/80 dark:hover:bg-slate-900/30 transition-all duration-200 px-5 py-4">
            <div class="flex flex-wrap items-center gap-4">

                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $avatarColor }} flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white text-sm avatar-initials">{{ $initials }}</span>
                </div>

                {{-- Resi & Invoice --}}
                <div class="min-w-28 flex-shrink-0">
                    <a href="{{ route('operator.shipments.show', $shipment) }}"
                       class="font-mono font-bold text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 dark:hover:text-cyan-300 hover:underline underline-offset-2 transition-colors">
                        {{ $shipment->shipment_number }}
                    </a>
                    <div class="font-mono text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate max-w-[130px]">
                        {{ $shipment->invoice_number ?? '—' }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                        <i data-lucide="clock-3" class="w-2.5 h-2.5"></i>
                        {{ $shipment->created_at->diffForHumans() }}
                    </div>
                </div>

                {{-- Pelanggan --}}
                <div class="min-w-36 flex-1">
                    <div class="font-semibold text-sm text-slate-800 dark:text-slate-100 leading-tight">
                        {{ $shipment->customer?->name ?? 'Pelanggan Direct' }}
                    </div>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate max-w-[180px]">
                        {{ $shipment->customer?->company_name ?? $shipment->customer?->email ?? '—' }}
                    </div>
                </div>

                {{-- Komoditas --}}
                <div class="min-w-40 flex-1 hidden md:block">
                    <div class="text-sm font-medium text-slate-700 dark:text-slate-200 leading-tight">
                        {{ Str::limit($shipment->coffee_variety ?? $shipment->cargo_type, 32) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                        <i data-lucide="weight" class="w-2.5 h-2.5"></i>
                        <span>{{ number_format($shipment->weight) }} kg</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <i data-lucide="box" class="w-2.5 h-2.5"></i>
                        <span>{{ number_format($shipment->volume, 1) }} m³</span>
                    </div>
                </div>

                {{-- Rute & Moda --}}
                <div class="min-w-36 hidden lg:block">
                    <div class="flex items-center gap-1.5 mb-0.5">
                        @php
                            $mode = $shipment->transport_mode ?? 'SEA';
                        @endphp
                        @if($mode === 'AIR')
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                                <i data-lucide="plane" class="w-2.5 h-2.5"></i> UDARA
                            </span>
                        @elseif($mode === 'LAND')
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                                <i data-lucide="truck" class="w-2.5 h-2.5"></i> DARAT
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                <i data-lucide="ship" class="w-2.5 h-2.5"></i> LAUT
                            </span>
                        @endif
                        <span class="text-[11px] font-medium text-slate-600 dark:text-slate-300 truncate max-w-[100px]">
                            {{ $shipment->carrier?->name ?? '—' }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $shipment->origin?->city ?? '—' }}</span>
                        <i data-lucide="arrow-right" class="w-2.5 h-2.5 text-slate-400"></i>
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $shipment->delivery_city ?? $shipment->destination?->city ?? '—' }}</span>
                    </div>
                </div>

                {{-- Badges: Invoice + Cargo --}}
                <div class="flex flex-col gap-1.5 items-start flex-shrink-0">
                    {{-- Payment Badge --}}
                    @if($isPaid)
                        <span class="badge-paid inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                            Lunas
                        </span>
                    @else
                        <span class="badge-unpaid inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Menunggu Wire
                        </span>
                    @endif

                    {{-- Cargo Status Badge --}}
                    @if($shipment->status === 'CREATED')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span>
                            CREATED
                        </span>
                    @elseif($shipment->status === 'BOOKED')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                            BOOKED
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 inline-block"></span>
                            {{ $shipment->status }}
                        </span>
                    @endif
                </div>

                {{-- Aksi Verifikasi --}}
                <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
                    <form method="POST" action="{{ route('operator.shipments.verify', $shipment) }}"
                          onsubmit="return confirm('Verifikasi dan lanjutkan alur pemuatan untuk pesanan {{ $shipment->shipment_number }}?');">
                        @csrf
                        @method('PATCH')
                        @if($isCreated)
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold text-[11px] shadow-md shadow-emerald-500/20 transition-all hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5"
                                    title="Verifikasi & Konfirmasi Jadwal Booking">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                Verifikasi Booking
                            </button>
                        @else
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-bold text-[11px] shadow-md shadow-cyan-500/20 transition-all hover:shadow-lg hover:shadow-cyan-500/30 hover:-translate-y-0.5"
                                    title="Otorisasi Penjemputan Gudang">
                                <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                                Pickup Gudang
                            </button>
                        @endif
                    </form>

                    {{-- Detail Button --}}
                    <a href="{{ route('operator.shipments.show', $shipment) }}"
                       class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-all"
                       title="Lihat Rincian Lengkap">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>
                </div>

            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="px-5 py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
            </div>
            <h4 class="text-base font-bold text-slate-700 dark:text-slate-200 mb-1">Tidak Ada Antrean Pesanan Baru</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                Semua pesanan mandiri dari pelanggan telah diverifikasi atau sedang dalam proses pelayaran armada.
            </p>
            <div class="mt-5 flex items-center justify-center gap-3">
                <a href="{{ route('operator.shipments.index') }}"
                   class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Lihat Semua Pengiriman
                </a>
                <a href="{{ route('operator.shipments.create') }}"
                   class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-all">
                    + Input Manual Offline
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($shipments->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/20">
        {{ $shipments->links() }}
    </div>
    @endif
</div>

@endsection
