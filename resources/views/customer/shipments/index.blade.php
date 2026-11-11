@extends('layouts.customer')

@section('title', 'Pengiriman Saya')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengiriman Saya</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Daftar lengkap kargo dan jadwal perjalanan kiriman Anda.</p>
    </div>
    <a href="{{ route('customer.shipments.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-all flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Pesan Pengiriman Baru</span>
    </a>
</div>

{{-- Filter Bar (Mockup 04) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-5 shadow-sm">
    <form method="GET" action="{{ route('customer.shipments.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Cari Nomor Resi / Kargo</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Resi atau Jenis Kargo..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 placeholder-slate-400">
            </div>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status Pengiriman</label>
            <select name="status" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua Status</option>
                <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>Dibuat</option>
                <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>Dipesan</option>
                <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>Diambil</option>
                <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>Dalam Perjalanan</option>
                <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Terkirim</option>
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Keterlambatan</label>
            <select name="delay" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua</option>
                <option value="delayed" {{ request('delay') === 'delayed' ? 'selected' : '' }}>Terlambat</option>
                <option value="ontime" {{ request('delay') === 'ontime' ? 'selected' : '' }}>Tepat Waktu</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                Filter
            </button>
            @if(request()->hasAny(['search','status','delay']))
                <a href="{{ route('customer.shipments.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Shipments Table (Mockup 04) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Daftar Kiriman Kargo</span>
        <span class="text-xs text-slate-400">{{ $shipments->total() }} kargo terdaftar</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-500 dark:text-slate-400">No. Pengiriman</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Ekspedisi</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Rute Perjalanan</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Status</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">ETA</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-500 dark:text-slate-400">Aksi</th>
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
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $shipment->cargo_type }}</div>
                        @if($shipment->open_exceptions_count > 0)
                            <div class="inline-flex items-center gap-1 mt-1 text-[10px] text-amber-600 dark:text-amber-400 font-semibold">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                Perhatian: Ada kendala rute
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-slate-600 dark:text-slate-300">{{ $shipment->carrier?->name ?? '-' }}</td>
                    <td class="px-3 py-3.5">
                        <div class="text-slate-700 dark:text-slate-200 font-medium">{{ $shipment->origin?->city ?? '-' }}</div>
                        <div class="flex items-center gap-1 text-slate-400 text-[11px]">
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            {{ $shipment->destination?->city ?? '-' }}
                        </div>
                    </td>
                    <td class="px-3 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-900/40 dark:text-{{ $statusColor }}-300">{{ $statusLabel }}</span>
                    </td>
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400">
                        {{ $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('d M Y') : '-' }}
                        @if($shipment->delay_minutes > 0)
                            <span class="block text-rose-500 font-semibold text-[10px] mt-0.5">+{{ round($shipment->delay_minutes/60) }} jam</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <a href="{{ route('customer.shipments.show', $shipment->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-[11px] transition-colors shadow-sm">
                            <i data-lucide="eye" class="w-3 h-3"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                        <i data-lucide="package-open" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
                        <p class="font-medium">Tidak ada kargo yang ditemukan.</p>
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

