@extends('layouts.admin')

@section('title', 'Semua Pengiriman')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Semua Pengiriman</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan menyeluruh seluruh manifes pengiriman kargo internasional</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors shadow-sm">
                <i data-lucide="download" class="w-3.5 h-3.5 text-cyan-500"></i>
                <span>Ekspor CSV</span>
            </a>
            <a href="{{ route('admin.shipments.map') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm">
                <i data-lucide="map" class="w-3.5 h-3.5"></i>
                <span>Lihat di Peta Global</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Resi, Pelanggan, Kargo..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Status: Semua</option>
                    <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>Dalam Proses (In Transit)</option>
                    <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Terkirim (Delivered)</option>
                    <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>Dipesan (Booked)</option>
                    <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>Dijemput (Picked Up)</option>
                    <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>Dibuat (Created)</option>
                </select>
            </div>

            <!-- Carrier Filter -->
            <div>
                <select name="carrier_id" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Ekspedisi: Semua</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ request('carrier_id') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Delay Filter -->
            <div>
                <select name="delay" class="w-full px-2.5 py-1.5 text-xs bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Keterlambatan: Semua</option>
                    <option value="delayed" {{ request('delay') === 'delayed' ? 'selected' : '' }}>Terlambat Saja</option>
                    <option value="ontime" {{ request('delay') === 'ontime' ? 'selected' : '' }}>Tepat Waktu Saja</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-1.5 px-3 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.shipments.index') }}" class="py-1.5 px-2.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-lg transition-colors" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Shipments Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">No. Pengiriman</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Ekspedisi</th>
                        <th class="py-3 px-4">Asal</th>
                        <th class="py-3 px-4">Tujuan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">ETA</th>
                        <th class="py-3 px-4">Keterlambatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <!-- Shipment Number -->
                            <td class="py-3.5 px-4 font-bold text-cyan-600 dark:text-cyan-400">
                                <a href="{{ route('admin.shipments.show', $shipment) }}" class="hover:underline flex items-center gap-1.5">
                                    {{ $shipment->shipment_number }}
                                    @if(($shipment->transport_mode ?? 'SEA') === 'AIR')
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">✈️ Udara</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">🚢 Laut</span>
                                    @endif
                                    @if($shipment->open_exceptions_count > 0)
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse" title="{{ $shipment->open_exceptions_count }} Pengecualian Aktif"></span>
                                    @endif
                                </a>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                                {{ $shipment->customer->name ?? '-' }}
                                <span class="block text-[10px] text-slate-400">{{ $shipment->customer->company_name ?? '' }}</span>
                            </td>

                            <!-- Carrier -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $shipment->carrier->code }} &bull; {{ $shipment->carrier->name }}
                                </span>
                            </td>

                            <!-- Origin -->
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                <div class="font-medium">{{ $shipment->origin->city ?? '-' }}</div>
                                <span class="text-[10px] text-slate-400">{{ $shipment->origin->country ?? '' }}</span>
                            </td>

                            <!-- Destination -->
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                <div class="font-medium">{{ $shipment->destination->city ?? '-' }}</div>
                                <span class="text-[10px] text-slate-400">{{ $shipment->destination->country ?? '' }}</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4">
                                @if($shipment->status === 'IN_TRANSIT')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-cyan-500/10 text-cyan-500 border border-cyan-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-ping"></span>
                                        Dalam Proses
                                    </span>
                                @elseif($shipment->status === 'DELIVERED')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terkirim
                                    </span>
                                @elseif($shipment->status === 'BOOKED')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                        Dipesan
                                    </span>
                                @elseif($shipment->status === 'PICKED_UP')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-500/10 text-indigo-500 border border-indigo-500/20">
                                        Dijemput
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Dibuat
                                    </span>
                                @endif
                            </td>

                            <!-- ETA -->
                            <td class="py-3.5 px-4 font-mono text-slate-700 dark:text-slate-300">
                                {{ $shipment->estimated_time_arrival->format('d M Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $shipment->estimated_time_arrival->format('H:i') }}</span>
                            </td>

                            <!-- Delay -->
                            <td class="py-3.5 px-4">
                                @if($shipment->isDelayed())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-500/10 text-rose-500 border border-rose-500/20">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        {{ $shipment->formatted_delay }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        Tepat Waktu
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.shipments.show', $shipment) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-cyan-50 dark:bg-slate-800 dark:hover:bg-cyan-950/60 text-slate-700 hover:text-cyan-600 dark:text-slate-300 dark:hover:text-cyan-400 border border-slate-200 dark:border-slate-700 transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                <i data-lucide="package-x" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                                <p class="text-sm">Tidak ada data pengiriman yang cocok dengan kriteria pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($shipments->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <strong>{{ $shipments->firstItem() }}</strong> &ndash; <strong>{{ $shipments->lastItem() }}</strong> dari <strong>{{ $shipments->total() }}</strong> pengiriman
                </span>
                <div>
                    {{ $shipments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

