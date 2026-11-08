@extends('layouts.admin')

@section('title', 'Riwayat Pelacakan Kargo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Riwayat Pelacakan (Tracking Timeline)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Catatan event perjalanan kargo append-only yang diverifikasi oleh sistem dan operator logistik</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.tracking.index') }}" class="flex items-center gap-2">
                <select name="shipment_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Semua Riwayat Pengiriman</option>
                    @foreach($allShipments as $s)
                        <option value="{{ $s->id }}" {{ request('shipment_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->shipment_number }} &ndash; {{ $s->cargo_type }}
                        </option>
                    @endforeach
                </select>
                @if(request('shipment_id'))
                    <a href="{{ route('admin.tracking.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-lg" title="Tampilkan Semua">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if($selectedShipment)
        <!-- Selected Shipment Quick Banner -->
        <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="package-check" class="w-5 h-5 text-cyan-400"></i>
                <div class="text-xs">
                    <span class="font-bold text-white">{{ $selectedShipment->shipment_number }}</span>: 
                    {{ $selectedShipment->origin->city }} &rarr; {{ $selectedShipment->destination->city }} &bull;
                    Ekspedisi: <strong class="text-white">{{ $selectedShipment->carrier->name }}</strong>
                </div>
            </div>
            <a href="{{ route('admin.shipments.show', $selectedShipment) }}" class="text-xs font-bold underline hover:text-white">Lihat Detail Lengkap</a>
        </div>
    @endif

    <!-- Timeline List -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="relative pl-6 space-y-8 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
            @forelse($events as $event)
                <div class="relative flex items-start gap-4">
                    <!-- Dot -->
                    <div class="absolute -left-6 mt-1 w-5 h-5 rounded-full {{ $event->event_type === 'EXCEPTION_NOTED' ? 'bg-amber-500' : ($event->shipment_status_at_event === 'DELIVERED' ? 'bg-emerald-500' : 'bg-cyan-500') }} text-white flex items-center justify-center ring-4 ring-white dark:ring-[#0A1128]">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    </div>

                    <div class="flex-1 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.shipments.show', $event->shipment_id) }}" class="font-mono text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                                    {{ $event->shipment->shipment_number ?? '-' }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $event->shipment_status_at_event }}
                                </span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    &bull; {{ $event->event_type }}
                                </span>
                            </div>
                            <span class="text-[11px] font-mono text-slate-400">
                                {{ $event->event_time->format('d M Y, H:i') }} ({{ $event->event_time->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $event->description }}</p>

                        <div class="flex flex-wrap items-center gap-4 mt-2.5 pt-2 border-t border-slate-200 dark:border-slate-800/80 text-[11px] text-slate-400">
                            <span class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-cyan-500"></i>
                                {{ $event->location->name ?? ($event->location_name_custom ?? 'Lokasi Kargo') }}
                                @if($event->latitude && $event->longitude)
                                    <span class="font-mono text-[10px] text-slate-500">({{ $event->latitude }}, {{ $event->longitude }})</span>
                                @endif
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                Petugas: <strong>{{ $event->creator->name ?? 'Sistem' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-8">Tidak ada data event pelacakan yang ditemukan.</p>
            @endforelse
        </div>

        @if($events->hasPages())
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

