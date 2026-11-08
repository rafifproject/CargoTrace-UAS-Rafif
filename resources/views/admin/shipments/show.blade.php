@extends('layouts.admin')

@section('title', 'Detail Pengiriman ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dasbor</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="{{ route('admin.shipments.index') }}" class="hover:underline">Pengiriman</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="font-bold text-slate-800 dark:text-white">{{ $shipment->shipment_number }}</span>
    </nav>

    <!-- Header Summary Card -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">{{ $shipment->shipment_number }}</h1>
                @if($shipment->isDelayed())
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-500 border border-rose-500/20 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        {{ $shipment->formatted_delay }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                        Tepat Waktu
                    </span>
                @endif
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                    {{ $shipment->status }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kargo: <strong class="text-slate-700 dark:text-slate-300">{{ $shipment->cargo_type }}</strong> &bull; Dibuat pada {{ $shipment->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 block uppercase font-semibold">Pelanggan</span>
                <span class="font-bold text-slate-800 dark:text-white truncate block">{{ $shipment->customer->name ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 block uppercase font-semibold">Ekspedisi</span>
                <span class="font-bold text-slate-800 dark:text-white truncate block">{{ $shipment->carrier->name ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 block uppercase font-semibold">Rute</span>
                <span class="font-bold text-cyan-600 dark:text-cyan-400 truncate block">{{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 block uppercase font-semibold">Estimasi Tiba (ETA)</span>
                <span class="font-bold text-slate-800 dark:text-white block">{{ $shipment->estimated_time_arrival->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex gap-6 text-xs font-semibold select-none overflow-x-auto">
        <button onclick="switchTab('overview')" id="tab-btn-overview" class="tab-btn py-3 border-b-2 border-cyan-500 text-cyan-500 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4"></i>
            <span>Ringkasan</span>
        </button>
        <button onclick="switchTab('tracking')" id="tab-btn-tracking" class="tab-btn py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 flex items-center gap-2">
            <i data-lucide="git-commit" class="w-4 h-4"></i>
            <span>Riwayat Pelacakan ({{ $shipment->trackingEvents->count() }})</span>
        </button>
        <button onclick="switchTab('map')" id="tab-btn-map" class="tab-btn py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 flex items-center gap-2">
            <i data-lucide="map" class="w-4 h-4"></i>
            <span>Peta Rute</span>
        </button>
        <button onclick="switchTab('exceptions')" id="tab-btn-exceptions" class="tab-btn py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Pengecualian ({{ $shipment->exceptions->count() }})</span>
        </button>
        <button onclick="switchTab('items')" id="tab-btn-items" class="tab-btn py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 flex items-center gap-2">
            <i data-lucide="boxes" class="w-4 h-4"></i>
            <span>Barang Kargo ({{ $shipment->items->count() }})</span>
        </button>
    </div>

    <!-- Tab Contents -->

    <!-- TAB 1: OVERVIEW -->
    <div id="tab-overview" class="tab-pane space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Specification Card -->
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-cyan-500"></i>
                    <span>Informasi Spesifikasi Pengiriman</span>
                </h3>
                <dl class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="text-slate-400 font-medium">Jenis Kargo</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $shipment->cargo_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Berat Total</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ number_format($shipment->weight, 2) }} kg</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Volume Total</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ number_format($shipment->volume, 2) }} m&sup3;</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Posisi Terkini</dt>
                        <dd class="font-bold text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $shipment->currentLocation->name ?? 'Dalam Perjalanan' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Schedule Card -->
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-cyan-500"></i>
                    <span>Jadwal & Waktu Perjalanan</span>
                </h3>
                <dl class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="text-slate-400 font-medium">Estimasi Keberangkatan</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $shipment->expected_departure->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Keberangkatan Aktual</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $shipment->actual_departure ? $shipment->actual_departure->format('d M Y, H:i') : 'Belum Berangkat' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Estimasi Kedatangan (Jadwal Awal)</dt>
                        <dd class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $shipment->expected_arrival->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">ETA Terkini (Dihitung Sistem)</dt>
                        <dd class="font-bold text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $shipment->estimated_time_arrival->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Weather Status Along the Route -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i data-lucide="cloud-sun" class="w-4 h-4 text-cyan-500"></i>
                <span>Kondisi Cuaca Pelabuhan Asal & Tujuan (Data Open-Meteo)</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Origin Weather -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400">Pelabuhan Asal: {{ $shipment->origin->name }}</span>
                        <div class="text-lg font-black text-slate-900 dark:text-white mt-1">32°C &bull; Cerah Berawan</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelembaban 72% &bull; Kecepatan Angin 12 km/jam</p>
                    </div>
                    <i data-lucide="sun" class="w-8 h-8 text-amber-500"></i>
                </div>
                <!-- Destination Weather -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400">Pelabuhan Tujuan: {{ $shipment->destination->name }}</span>
                        <div class="text-lg font-black text-slate-900 dark:text-white mt-1">26°C &bull; Hujan Ringan</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelembaban 84% &bull; Kecepatan Angin 28 km/jam</p>
                    </div>
                    <i data-lucide="cloud-rain" class="w-8 h-8 text-blue-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: TRACKING TIMELINE -->
    <div id="tab-tracking" class="tab-pane hidden space-y-4">
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-6">Kronologi Riwayat Perjalanan Kargo</h3>
            
            <div class="relative pl-6 space-y-8 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                @forelse($shipment->trackingEvents as $event)
                    <div class="relative flex items-start gap-4">
                        <!-- Timeline Dot -->
                        <div class="absolute -left-6 mt-1 w-5 h-5 rounded-full bg-cyan-600 text-white flex items-center justify-center ring-4 ring-white dark:ring-[#0A1128]">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </div>
                        <div class="flex-1 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $event->shipment_status_at_event }} &bull; {{ $event->event_type }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">
                                    {{ $event->event_time->format('d M Y, H:i') }} ({{ $event->event_time->diffForHumans() }})
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $event->description }}</p>
                            <div class="flex items-center gap-3 mt-2 text-[10px] text-slate-400">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-cyan-500"></i>
                                    {{ $event->location->name ?? ($event->location_name_custom ?? 'Lokasi Kargo') }}
                                </span>
                                <span>&bull;</span>
                                <span>Dicatat oleh: <strong>{{ $event->creator->name ?? 'Sistem' }}</strong></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada riwayat checkpoint pelacakan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 3: ROUTE MAP -->
    <div id="tab-map" class="tab-pane hidden space-y-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2">Visualisasi Geografis Jalur Pengiriman</h3>
            <div id="detail-map" class="w-full h-96 rounded-xl border border-slate-200 dark:border-slate-800 z-10"></div>
        </div>
    </div>

    <!-- TAB 4: EXCEPTIONS -->
    <div id="tab-exceptions" class="tab-pane hidden space-y-4">
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Pengecualian / Hambatan Perjalanan</h3>
            </div>
            <div class="space-y-3">
                @forelse($shipment->exceptions as $exc)
                    <div class="p-4 rounded-xl border {{ $exc->isOpen() ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900' : 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $exc->isOpen() ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                                    {{ $exc->status }}
                                </span>
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $exc->exception_type }}</span>
                                <span class="text-[10px] text-slate-400">Tingkat: <strong>{{ $exc->severity }}</strong></span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $exc->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-700 dark:text-slate-300 font-medium">{{ $exc->description }}</p>
                        @if($exc->resolution_notes)
                            <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-800 text-xs text-emerald-600 dark:text-emerald-400">
                                <strong>Catatan Resolusi:</strong> {{ $exc->resolution_notes }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Tidak ada hambatan atau pengecualian yang tercatat untuk kargo ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 5: ITEMS -->
    <div id="tab-items" class="tab-pane hidden space-y-4">
        <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Manifes Barang & Komoditas</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Nama Komoditas</th>
                        <th class="py-3 px-4">HS Code</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Berat (kg)</th>
                        <th class="py-3 px-4 text-right">Nilai Pabean</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($shipment->items as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-white">{{ $item->item_name }}</td>
                            <td class="py-3 px-4 font-mono text-cyan-600 dark:text-cyan-400">{{ $item->hs_code ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-medium text-slate-700 dark:text-slate-300">{{ $item->quantity }} unit</td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700 dark:text-slate-300">{{ number_format($item->weight, 2) }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800 dark:text-white">
                                {{ $item->currency }} {{ number_format($item->declared_value, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada rincian item kargo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let detailMapInitialized = false;
    let detailMap;

    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-cyan-500', 'text-cyan-500');
            el.classList.add('border-transparent', 'text-slate-500');
        });

        const activePane = document.getElementById('tab-' + tabId);
        const activeBtn = document.getElementById('tab-btn-' + tabId);

        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('border-cyan-500', 'text-cyan-500');
            activeBtn.classList.remove('border-transparent', 'text-slate-500');
        }

        if (tabId === 'map' && !detailMapInitialized) {
            setTimeout(initDetailMap, 100);
        }
    }

    function initDetailMap() {
        detailMapInitialized = true;
        const origin = [{{ $shipment->origin->latitude }}, {{ $shipment->origin->longitude }}];
        const dest = [{{ $shipment->destination->latitude }}, {{ $shipment->destination->longitude }}];

        detailMap = L.map('detail-map').setView(origin, 3);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(detailMap);

        // Origin Marker (Green)
        L.circleMarker(origin, { radius: 8, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1 })
            .bindPopup(`<b>Asal:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}`)
            .addTo(detailMap);

        // Destination Marker (Red)
        L.circleMarker(dest, { radius: 8, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1 })
            .bindPopup(`<b>Tujuan:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}`)
            .addTo(detailMap);

        @if($shipment->currentLocation)
            const current = [{{ $shipment->currentLocation->latitude }}, {{ $shipment->currentLocation->longitude }}];
            L.circleMarker(current, { radius: 9, fillColor: '#00A8E8', color: '#fff', weight: 2, fillOpacity: 1 })
                .bindPopup(`<b>Posisi Saat Ini:</b> {{ $shipment->currentLocation->name }}`)
                .addTo(detailMap);
            L.polyline([origin, current, dest], { color: '#00A8E8', weight: 3, dashArray: '6, 6' }).addTo(detailMap);
            detailMap.fitBounds(L.latLngBounds([origin, current, dest]), { padding: [50, 50] });
        @else
            L.polyline([origin, dest], { color: '#00A8E8', weight: 3, dashArray: '6, 6' }).addTo(detailMap);
            detailMap.fitBounds(L.latLngBounds([origin, dest]), { padding: [50, 50] });
        @endif
    }
</script>
@endpush

