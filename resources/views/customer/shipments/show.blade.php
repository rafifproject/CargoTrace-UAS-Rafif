@extends('layouts.customer')

@section('title', 'Detail Pengiriman – ' . $shipment->shipment_number)

@section('content')
{{-- Breadcrumbs & Header (Mockup 05) --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('customer.shipments.index') }}" class="hover:text-cyan-500">Pengiriman Saya</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $shipment->shipment_number }}</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold font-mono text-slate-800 dark:text-white">{{ $shipment->shipment_number }}</h1>
            @php
                $statusMap = ['CREATED' => ['Dibuat','slate'], 'BOOKED' => ['Dipesan','amber'], 'PICKED_UP' => ['Diambil','purple'], 'IN_TRANSIT' => ['Dalam Perjalanan','blue'], 'DELIVERED' => ['Terkirim','emerald']];
                [$statusLabel, $statusColor] = $statusMap[$shipment->status] ?? [$shipment->status, 'slate'];
            @endphp
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-900/40 dark:text-{{ $statusColor }}-300">{{ $statusLabel }}</span>
            @if($shipment->delay_minutes > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Terlambat +{{ round($shipment->delay_minutes/60) }} jam</span>
            @endif
        </div>
    </div>
</div>

{{-- Stepper Progress Component (Mockup 05) --}}
@php
    $steps = [
        'CREATED'    => ['Dibuat', 'box'],
        'BOOKED'     => ['Diproses', 'calendar'],
        'PICKED_UP'  => ['Dikirim', 'truck'],
        'IN_TRANSIT' => ['Dalam Perjalanan', 'navigation'],
        'DELIVERED'  => ['Terkirim', 'check-circle-2'],
    ];
    $statusOrder = array_keys($steps);
    $currentIndex = array_search($shipment->status, $statusOrder);
    if ($currentIndex === false) $currentIndex = 0;
@endphp
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 mb-6 shadow-sm">
    <div class="relative flex items-center justify-between">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 w-full bg-slate-200 dark:bg-slate-800 z-0"></div>
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-cyan-500 z-0 transition-all duration-500" style="width: {{ ($currentIndex / (count($steps) - 1)) * 100 }}%"></div>
        @foreach($steps as $key => [$label, $icon])
            @php
                $stepIndex = array_search($key, $statusOrder);
                $isCompleted = $stepIndex < $currentIndex;
                $isCurrent = $stepIndex === $currentIndex;
            @endphp
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-colors {{ $isCompleted ? 'bg-cyan-600 text-white' : ($isCurrent ? 'bg-cyan-500 text-white ring-4 ring-cyan-500/20 shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') }}">
                    <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                </div>
                <span class="mt-2 text-[11px] font-medium {{ $isCurrent ? 'text-cyan-600 dark:text-cyan-400 font-bold' : ($isCompleted ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400') }}">{{ $label }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="flex gap-2 border-b border-slate-200 dark:border-slate-800 mb-6 text-sm">
    <button onclick="switchTab('info')" id="tab-btn-info" class="tab-btn active px-4 py-2.5 font-semibold text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500">Ringkasan</button>
    <button onclick="switchTab('tracking')" id="tab-btn-tracking" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Riwayat Pelacakan ({{ $shipment->trackingEvents->count() }})</button>
    <button onclick="switchTab('map')" id="tab-btn-map" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Peta Rute</button>
    <button onclick="switchTab('exceptions')" id="tab-btn-exceptions" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Kendala ({{ $shipment->exceptions->count() }})</button>
</div>

{{-- TAB 1: RINGKASAN --}}
<div id="tab-info" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Informasi Pengiriman</h3>
                <div class="grid grid-cols-2 gap-y-4 text-xs">
                    <div>
                        <span class="text-slate-400">Penyedia Ekspedisi</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->carrier?->name ?? '-' }} ({{ $shipment->carrier?->code ?? '-' }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Jenis Kargo</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->cargo_type }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Berat & Volume</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ number_format($shipment->weight, 2) }} kg / {{ number_format($shipment->volume, 2) }} m³</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Instruksi Khusus</span>
                        <p class="text-slate-600 dark:text-slate-300 mt-0.5">{{ $shipment->notes ?: 'Tidak ada instruksi khusus' }}</p>
                    </div>
                </div>
            </div>

            {{-- Detail Item Kargo --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Rincian Barang Kargo</h3>
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">Nama Item</th>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">HS Code</th>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">Jumlah</th>
                            <th class="text-right px-3 py-2 text-slate-500 font-semibold">Nilai Pabean</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($shipment->items as $item)
                        <tr>
                            <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200">{{ $item->item_name }}</td>
                            <td class="px-3 py-2.5 font-mono text-slate-500">{{ $item->hs_code ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ $item->quantity }} pcs</td>
                            <td class="px-3 py-2.5 text-right font-mono text-slate-700 dark:text-slate-200">${{ number_format($item->declared_value, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-slate-400">Tidak ada rincian item.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sisi Kanan: Jadwal & Lokasi --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Waktu & Lokasi Perjalanan</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400">Pelabuhan / Bandara Asal</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->origin?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Pelabuhan / Bandara Tujuan</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->destination?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Posisi Kargo Terkini</span>
                        <div class="font-semibold text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $shipment->currentLocation?->name ?? 'Dalam perjalanan' }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Estimasi Kedatangan (ETA)</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('d M Y, H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TAB 2: RIWAYAT PELACAKAN (Timeline) --}}
<div id="tab-tracking" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-6">Riwayat Checkpoint Pengiriman</h3>
        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
            @forelse($shipment->trackingEvents as $event)
            <div class="relative">
                <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-cyan-500 border-2 border-white dark:border-[#0A1128]"></span>
                <div class="text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $event->event_type }}</span>
                        <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($event->event_time)->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 mt-1">{{ $event->description }}</p>
                    @if($event->location)
                        <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-400">
                            <i data-lucide="map-pin" class="w-3 h-3 text-cyan-500"></i> {{ $event->location->name }}, {{ $event->location->country }}
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 py-4">Belum ada riwayat pelacakan.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- TAB 3: PETA RUTE --}}
<div id="tab-map" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Peta Visualisasi Rute Kargo</h3>
        <div id="customer-detail-map" style="height: 420px; border-radius: 12px; background: #0A1128;"></div>
    </div>
</div>

{{-- TAB 4: KENDALA / PENGECUALIAN --}}
<div id="tab-exceptions" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Catatan Kendala & Mitigasi Rute</h3>
        <div class="space-y-3">
            @forelse($shipment->exceptions as $ex)
            <div class="p-4 rounded-xl border {{ $ex->severity === 'CRITICAL' ? 'bg-rose-50 border-rose-200 dark:bg-rose-950/20 dark:border-rose-800/40' : 'bg-amber-50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-800/40' }} text-xs">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $ex->exception_type }}</span>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-bold {{ $ex->severity === 'CRITICAL' ? 'bg-rose-200 text-rose-800 dark:bg-rose-950 dark:text-rose-200' : 'bg-amber-200 text-amber-800 dark:bg-amber-950 dark:text-amber-200' }}">{{ $ex->severity }}</span>
                    </div>
                    <span class="font-bold uppercase text-[10px] {{ $ex->status === 'RESOLVED' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $ex->status }}</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 mt-2">{{ $ex->description }}</p>
                @if($ex->resolution_notes)
                    <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-800 text-[11px] text-emerald-700 dark:text-emerald-400">
                        <b>Tindakan Penyelesaian Operator:</b> {{ $ex->resolution_notes }}
                    </div>
                @endif
            </div>
            @empty
            <div class="py-6 text-center text-xs text-slate-400">
                <i data-lucide="shield-check" class="w-8 h-8 mx-auto mb-2 opacity-50 text-emerald-500"></i>
                <p>Tidak ada kendala pada pengiriman ini. Perjalanan berlangsung normal.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active', 'border-b-2', 'border-cyan-500', 'text-cyan-600', 'dark:text-cyan-400');
        el.classList.add('text-slate-500', 'dark:text-slate-400');
    });

    const activeContent = document.getElementById(`tab-${name}`);
    const activeBtn = document.getElementById(`tab-btn-${name}`);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.add('active', 'border-b-2', 'border-cyan-500', 'text-cyan-600', 'dark:text-cyan-400');
        activeBtn.classList.remove('text-slate-500', 'dark:text-slate-400');
    }

    if (name === 'map') {
        setTimeout(initDetailMap, 100);
    }
}

let mapInitialized = false;
function initDetailMap() {
    if (mapInitialized) return;
    mapInitialized = true;

    const oLat = {{ $shipment->origin?->latitude ?? 0 }};
    const oLng = {{ $shipment->origin?->longitude ?? 0 }};
    const dLat = {{ $shipment->destination?->latitude ?? 0 }};
    const dLng = {{ $shipment->destination?->longitude ?? 0 }};

    const map = L.map('customer-detail-map').setView([oLat, oLng], 4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    L.circleMarker([oLat, oLng], { radius: 8, color: '#10b981', fillColor: '#10b981', fillOpacity: 1 }).addTo(map).bindPopup('<b>Asal:</b> {{ $shipment->origin?->name }}');
    L.circleMarker([dLat, dLng], { radius: 8, color: '#f43f5e', fillColor: '#f43f5e', fillOpacity: 1 }).addTo(map).bindPopup('<b>Tujuan:</b> {{ $shipment->destination?->name }}');
    L.polyline([[oLat, oLng], [dLat, dLng]], { color: '#06b6d4', weight: 3, dashArray: '6,6' }).addTo(map);

    @if($shipment->currentLocation)
        const cLat = {{ $shipment->currentLocation->latitude }};
        const cLng = {{ $shipment->currentLocation->longitude }};
        L.circleMarker([cLat, cLng], { radius: 9, color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 1 }).addTo(map).bindPopup('<b>Lokasi Saat Ini:</b> {{ $shipment->currentLocation->name }}').openPopup();
        map.fitBounds([[oLat, oLng], [dLat, dLng], [cLat, cLng]], { padding: [50, 50] });
    @else
        map.fitBounds([[oLat, oLng], [dLat, dLng]], { padding: [50, 50] });
    @endif
}
</script>
@endpush

