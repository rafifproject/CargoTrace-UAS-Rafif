@extends('layouts.operator')

@section('title', 'Semua Pengiriman')

@section('content')
{{-- Page Header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Semua Pengiriman</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola dan pantau seluruh pengiriman kargo operasional.</p>
    </div>
    <a href="{{ route('operator.shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold transition-colors shadow-sm">
        <i data-lucide="package-plus" class="w-4 h-4"></i>
        Buat Pengiriman
    </a>
</div>

{{-- Filter Bar --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-5 shadow-sm">
    <form method="GET" action="{{ route('operator.shipments.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Cari Pengiriman</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Resi, Pelanggan, Kargo..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 placeholder-slate-400">
            </div>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua Status</option>
                <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>Dibuat</option>
                <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>Dipesan</option>
                <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>Diambil</option>
                <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>Dalam Perjalanan</option>
                <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Terkirim</option>
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Ekspedisi</label>
            <select name="carrier_id" class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua Ekspedisi</option>
                @foreach($carriers as $c)
                    <option value="{{ $c->id }}" {{ request('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Keterlambatan</label>
            <select name="delay" class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua</option>
                <option value="delayed" {{ request('delay') === 'delayed' ? 'selected' : '' }}>Terlambat</option>
                <option value="ontime" {{ request('delay') === 'ontime' ? 'selected' : '' }}>Tepat Waktu</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold transition-colors">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                Filter
            </button>
            @if(request()->hasAny(['search','status','carrier_id','delay']))
                <a href="{{ route('operator.shipments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Shipments Table --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Daftar Pengiriman</span>
        <span class="text-xs text-slate-400">{{ $shipments->total() }} data ditemukan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-500 dark:text-slate-400">No. Resi</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Pelanggan</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Ekspedisi</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Rute</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Status</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">ETA</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Keterlambatan</th>
                    <th class="text-center px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($shipments as $shipment)
                @php
                    $statusMap = ['CREATED' => ['Dibuat','slate'], 'BOOKED' => ['Dipesan','amber'], 'PICKED_UP' => ['Diambil','purple'], 'IN_TRANSIT' => ['Dalam Perjalanan','blue'], 'DELIVERED' => ['Terkirim','emerald']];
                    [$statusLabel, $statusColor] = $statusMap[$shipment->status] ?? [$shipment->status, 'slate'];
                @endphp
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="font-mono font-semibold text-slate-800 dark:text-slate-100">{{ $shipment->shipment_number }}</div>
                        @if($shipment->open_exceptions_count > 0)
                            <div class="inline-flex items-center gap-1 mt-0.5 text-[10px] text-amber-600 dark:text-amber-400 font-medium">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                {{ $shipment->open_exceptions_count }} pengecualian
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-slate-600 dark:text-slate-300">{{ $shipment->customer?->name ?? '-' }}</td>
                    <td class="px-3 py-3.5 text-slate-600 dark:text-slate-300">{{ $shipment->carrier?->name ?? '-' }}</td>
                    <td class="px-3 py-3.5">
                        <div class="text-slate-700 dark:text-slate-200 font-medium">{{ $shipment->origin?->city ?? '-' }}</div>
                        <div class="flex items-center gap-1 text-slate-400">
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            {{ $shipment->destination?->city ?? '-' }}
                        </div>
                    </td>
                    <td class="px-3 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-900/40 dark:text-{{ $statusColor }}-300">{{ $statusLabel }}</span>
                    </td>
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400">
                        {{ $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-3 py-3.5">
                        @if($shipment->delay_minutes > 0)
                            <span class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-semibold">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                +{{ round($shipment->delay_minutes / 60) }} jam
                            </span>
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">Tepat Waktu</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('operator.shipments.show', $shipment) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-cyan-50 hover:text-cyan-600 dark:hover:bg-cyan-900/30 dark:hover:text-cyan-400 transition-colors text-[11px] font-medium">
                                <i data-lucide="eye" class="w-3 h-3"></i> Detail
                            </a>
                            <a href="{{ route('operator.shipments.edit', $shipment) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/30 dark:hover:text-amber-400 transition-colors text-[11px] font-medium">
                                <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                        <i data-lucide="package-open" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
                        <p class="font-medium">Tidak ada data pengiriman yang ditemukan.</p>
                        <p class="text-xs mt-1">Coba ubah filter pencarian atau buat pengiriman baru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($shipments->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
            {{ $shipments->links() }}
        </div>
    @endif
</div>
@endsection

