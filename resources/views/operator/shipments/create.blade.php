@extends('layouts.operator')

@section('title', 'Buat Pengiriman Baru')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('operator.shipments.index') }}" class="hover:text-cyan-500">Semua Pengiriman</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Buat Pengiriman Baru</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Buat Pengiriman Baru</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftarkan pengiriman baru via moda Kapal Laut (Pelabuhan) atau Pesawat Udara (Bandara Kargo)</p>
    </div>
    <a href="{{ route('operator.shipments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        <span>Kembali</span>
    </a>
</div>

<form method="POST" action="{{ route('operator.shipments.store') }}">
    @csrf
    {{-- Hidden input for transport mode --}}
    <input type="hidden" name="transport_mode" id="transport_mode" value="{{ old('transport_mode', 'SEA') }}">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- LEFT: Form Fields --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- PILIHAN MODA PENGIRIMAN: KAPAL LAUT vs PESAWAT UDARA --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        1. Jenis Moda Pengiriman <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Pilih moda untuk menentukan daftar simpul logistik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Opsi Kapal Laut --}}
                    <div id="mode-card-sea" onclick="setTransportMode('SEA')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shadow-sm">
                                    🚢
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">Kapal Laut</h4>
                                    <span class="text-[10px] text-slate-400">Maritime Freight</span>
                                </div>
                            </div>
                            <span id="badge-sea" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Terpilih
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                            Simpul Pelabuhan Laut Internasional (~$0.35/kg, 7-21 hari).
                        </p>
                    </div>

                    {{-- Opsi Pesawat Udara --}}
                    <div id="mode-card-air" onclick="setTransportMode('AIR')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shadow-sm">
                                    ✈️
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">Pesawat Udara</h4>
                                    <span class="text-[10px] text-slate-400">Air Cargo Express</span>
                                </div>
                            </div>
                            <span id="badge-air" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Pilih
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                            Simpul Bandara Kargo Internasional (~$2.80/kg, 1-3 hari kilat).
                        </p>
                    </div>
                </div>
                @error('transport_mode')<p class="text-rose-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Informasi Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                    2. Informasi Pengiriman & Pelanggan
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nomor Pengiriman <span class="text-rose-500">*</span></label>
                        <input type="text" name="shipment_number" value="{{ old('shipment_number', $suggestedNumber) }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono @error('shipment_number') border-rose-400 @enderror">
                        @error('shipment_number')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelanggan <span class="text-rose-500">*</span></label>
                        <select name="customer_id" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('customer_id') border-rose-400 @enderror">
                            <option value="">— Pilih Pelanggan —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Ekspedisi (205+ Negara) <span class="text-rose-500">*</span></label>
                            <span class="text-[11px] text-cyan-600 dark:text-cyan-400 font-medium">{{ count($carriers) }} Mitra</span>
                        </div>
                        <div class="space-y-1.5">
                            <input type="text" id="carrier_quick_filter" placeholder="Cari nama ekspedisi, kode, atau negara..." onkeyup="filterCarrierOptions(this.value)" class="w-full px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <select id="carrier_select" name="carrier_id" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('carrier_id') border-rose-400 @enderror">
                                <option value="">— Pilih Ekspedisi Resmi Global —</option>
                                @foreach($carriers as $c)
                                    <option value="{{ $c->id }}" data-search="{{ strtolower($c->name . ' ' . $c->code . ' ' . $c->country) }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }}) — {{ $c->country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('carrier_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jenis Kargo <span class="text-rose-500">*</span></label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type') }}" placeholder="cth. Electronics, Textiles..." class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('cargo_type') border-rose-400 @enderror">
                        @error('cargo_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Rute Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                        <span id="route-heading">3. Rute Pelabuhan Asal & Tujuan</span>
                    </h3>
                    <span id="node-count-badge" class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                        {{ count($portLocations) }} Lokasi
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label id="origin-label" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelabuhan Asal <span class="text-rose-500">*</span></label>
                        <select name="origin_location_id" id="origin-select" onchange="updateMap()" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('origin_location_id') border-rose-400 @enderror">
                            <option value="">— Pilih Titik Asal —</option>
                        </select>
                        @error('origin_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label id="dest-label" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelabuhan Tujuan <span class="text-rose-500">*</span></label>
                        <select name="destination_location_id" id="dest-select" onchange="updateMap()" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('destination_location_id') border-rose-400 @enderror">
                            <option value="">— Pilih Titik Tujuan —</option>
                        </select>
                        @error('destination_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Spesifikasi & Jadwal --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-cyan-500"></i>
                    4. Spesifikasi & Jadwal
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Berat (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight') }}" step="0.01" min="0.1" placeholder="0.00" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('weight')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Volume (m³) <span class="text-rose-500">*</span></label>
                        <input type="number" name="volume" value="{{ old('volume') }}" step="0.01" min="0.1" placeholder="0.00" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('volume')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jadwal Keberangkatan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('expected_departure')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Estimasi Tiba (ETA) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('expected_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Detail Barang / Item Pertama (Opsional, Expandable) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <button type="button" onclick="toggleItemForm()" class="w-full flex items-center justify-between text-left">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="boxes" class="w-4 h-4 text-cyan-500"></i>
                        Detail Item Pertama (Opsional)
                    </span>
                    <i data-lucide="chevron-down" id="item-chevron" class="w-4 h-4 text-slate-400 transition-transform"></i>
                </button>
                <div id="item-form" class="mt-4 grid grid-cols-2 gap-4 hidden">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Item</label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="cth. Microcontrollers..." class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kode HS</label>
                        <input type="text" name="hs_code" value="{{ old('hs_code') }}" placeholder="cth. 8542.31" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kuantitas</label>
                        <input type="number" name="item_qty" value="{{ old('item_qty', 1) }}" min="1" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nilai Deklarasi ($ USD)</label>
                        <input type="number" name="item_value" value="{{ old('item_value') }}" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Catatan Tambahan</label>
                <textarea name="notes" rows="3" placeholder="Informasi penanganan khusus, instruksi, dsb..." class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Pengiriman</span>
                </button>
                <a href="{{ route('operator.shipments.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
            </div>
        </div>

        {{-- RIGHT: Live Map Preview --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden sticky top-6">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="map" class="w-4 h-4 text-cyan-500"></i>
                        <span>Pratinjau Peta Rute</span>
                    </h3>
                    <span id="route-distance-badge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-500">
                        Pilih Lokasi
                    </span>
                </div>
                <div id="route-map" style="height: 320px; background: #0A1128;"></div>
                <div id="route-map-footer" class="px-5 py-3 text-xs text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800">
                    Pilih lokasi asal dan tujuan untuk melihat rute di peta.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const portLocations = @json($portLocations);
const airportLocations = @json($airportLocations);
const initialMode = @json(old('transport_mode', 'SEA'));
const initialOriginId = @json(old('origin_location_id'));
const initialDestId = @json(old('destination_location_id'));

let currentMode = initialMode;
let routeMap = null;
let originMarker = null;
let destMarker = null;
let routeLine = null;
let planeMarker = null;

document.addEventListener('DOMContentLoaded', function () {
    routeMap = L.map('route-map', { zoomControl: true }).setView([15, 100], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 18 }).addTo(routeMap);

    setTransportMode(initialMode, false);
});

function setTransportMode(mode, resetSelections = true) {
    currentMode = mode;
    document.getElementById('transport_mode').value = mode;

    const cardSea = document.getElementById('mode-card-sea');
    const cardAir = document.getElementById('mode-card-air');
    const badgeSea = document.getElementById('badge-sea');
    const badgeAir = document.getElementById('badge-air');
    const heading = document.getElementById('route-heading');
    const badgeCount = document.getElementById('node-count-badge');
    const originLabel = document.getElementById('origin-label');
    const destLabel = document.getElementById('dest-label');

    if (mode === 'AIR') {
        cardAir.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-purple-500 bg-purple-50/40 dark:bg-purple-950/20 shadow-sm flex flex-col justify-between';
        badgeAir.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-600 text-white border-purple-600';
        badgeAir.textContent = 'Terpilih ✓';

        cardSea.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0A1128] hover:border-slate-300 dark:hover:border-slate-700 flex flex-col justify-between';
        badgeSea.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700';
        badgeSea.textContent = 'Pilih';

        heading.innerHTML = '3. Rute Penerbangan Kargo (Bandara Internasional)';
        badgeCount.textContent = `${airportLocations.length} Bandara`;
        originLabel.innerHTML = 'Bandara Asal <span class="text-rose-500">*</span>';
        destLabel.innerHTML = 'Bandara Tujuan <span class="text-rose-500">*</span>';

        populateDropdowns(airportLocations, resetSelections ? null : initialOriginId, resetSelections ? null : initialDestId, 'Bandara');
    } else {
        cardSea.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-blue-500 bg-blue-50/40 dark:bg-blue-950/20 shadow-sm flex flex-col justify-between';
        badgeSea.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-600 text-white border-blue-600';
        badgeSea.textContent = 'Terpilih ✓';

        cardAir.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0A1128] hover:border-slate-300 dark:hover:border-slate-700 flex flex-col justify-between';
        badgeAir.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700';
        badgeAir.textContent = 'Pilih';

        heading.innerHTML = '3. Rute Pelayaran Maritim (Pelabuhan Laut)';
        badgeCount.textContent = `${portLocations.length} Pelabuhan`;
        originLabel.innerHTML = 'Pelabuhan Asal <span class="text-rose-500">*</span>';
        destLabel.innerHTML = 'Pelabuhan Tujuan <span class="text-rose-500">*</span>';

        populateDropdowns(portLocations, resetSelections ? null : initialOriginId, resetSelections ? null : initialDestId, 'Pelabuhan');
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }

    updateMap();
}

function populateDropdowns(locations, selOriginId, selDestId, nodeName) {
    const originSel = document.getElementById('origin-select');
    const destSel = document.getElementById('dest-select');

    let originHtml = `<option value="">— Pilih ${nodeName} Asal —</option>`;
    let destHtml = `<option value="">— Pilih ${nodeName} Tujuan —</option>`;

    locations.forEach(loc => {
        const oSel = (selOriginId && String(loc.id) === String(selOriginId)) ? 'selected' : '';
        const dSel = (selDestId && String(loc.id) === String(selDestId)) ? 'selected' : '';
        const text = `${loc.name} (${loc.city ? loc.city + ', ' : ''}${loc.country})`;

        originHtml += `<option value="${loc.id}" data-lat="${loc.latitude}" data-lng="${loc.longitude}" ${oSel}>${text}</option>`;
        destHtml += `<option value="${loc.id}" data-lat="${loc.latitude}" data-lng="${loc.longitude}" ${dSel}>${text}</option>`;
    });

    originSel.innerHTML = originHtml;
    destSel.innerHTML = destHtml;
}

function updateMap() {
    if (!routeMap) return;

    const originSel = document.getElementById('origin-select');
    const destSel = document.getElementById('dest-select');
    const originOpt = originSel.options[originSel.selectedIndex];
    const destOpt = destSel.options[destSel.selectedIndex];

    if (originMarker) routeMap.removeLayer(originMarker);
    if (destMarker) routeMap.removeLayer(destMarker);
    if (routeLine) routeMap.removeLayer(routeLine);
    if (planeMarker) routeMap.removeLayer(planeMarker);

    const oLat = originOpt && originOpt.value ? parseFloat(originOpt.dataset.lat) : null;
    const oLng = originOpt && originOpt.value ? parseFloat(originOpt.dataset.lng) : null;
    const dLat = destOpt && destOpt.value ? parseFloat(destOpt.dataset.lat) : null;
    const dLng = destOpt && destOpt.value ? parseFloat(destOpt.dataset.lng) : null;

    const isAir = (currentMode === 'AIR');
    const originColor = isAir ? '#A855F7' : '#10b981';
    const destColor = isAir ? '#EC4899' : '#f43f5e';
    const lineColor = isAir ? '#C084FC' : '#06b6d4';

    if (oLat && dLat) {
        originMarker = L.circleMarker([oLat, oLng], { radius: 8, color: originColor, fillColor: originColor, fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>${isAir ? 'Bandara Asal' : 'Pelabuhan Asal'}:</b><br>${originOpt.text}`);
        destMarker = L.circleMarker([dLat, dLng], { radius: 8, color: destColor, fillColor: destColor, fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>${isAir ? 'Bandara Tujuan' : 'Pelabuhan Tujuan'}:</b><br>${destOpt.text}`);
        routeLine = L.polyline([[oLat, oLng], [dLat, dLng]], { color: lineColor, weight: 3, dashArray: isAir ? '4,6' : '8,6' }).addTo(routeMap);

        const midLat = (oLat + dLat) / 2;
        const midLng = (oLng + dLng) / 2;
        const iconHtml = isAir 
            ? '<div style="font-size:16px;">✈️</div>'
            : '<div style="font-size:16px;">🚢</div>';
        
        planeMarker = L.marker([midLat, midLng], {
            icon: L.divIcon({ html: iconHtml, className: 'route-vehicle-icon', iconSize: [20, 20], iconAnchor: [10, 10] })
        }).addTo(routeMap);

        routeMap.fitBounds([[oLat, oLng], [dLat, dLng]], { padding: [40, 40] });

        const dLatRad = (dLat - oLat) * Math.PI / 180;
        const dLngRad = (dLng - oLng) * Math.PI / 180;
        const a = Math.sin(dLatRad/2)**2 + Math.cos(oLat*Math.PI/180) * Math.cos(dLat*Math.PI/180) * Math.sin(dLngRad/2)**2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distKM = Math.round(6371 * c);

        document.getElementById('route-distance-badge').textContent = isAir ? `Rute Udara: ~${distKM} km` : `Rute Laut: ~${Math.round(distKM * 0.5399)} NM`;
        document.getElementById('route-map-footer').textContent = `Terpilih: ${originOpt.text} ➔ ${destOpt.text}`;
    } else if (oLat) {
        originMarker = L.circleMarker([oLat, oLng], { radius: 8, color: originColor, fillColor: originColor, fillOpacity: 1 }).addTo(routeMap);
        routeMap.setView([oLat, oLng], 5);
        document.getElementById('route-distance-badge').textContent = 'Pilih Tujuan';
        document.getElementById('route-map-footer').textContent = 'Pilih simpul tujuan untuk melengkapi rute.';
    } else {
        document.getElementById('route-distance-badge').textContent = 'Pilih Lokasi';
        document.getElementById('route-map-footer').textContent = 'Pilih lokasi asal dan tujuan untuk melihat rute di peta.';
    }
}

function filterCarrierOptions(term) {
    const q = (term || '').toLowerCase().trim();
    const select = document.getElementById('carrier_select');
    if (!select) return;

    for (let i = 1; i < select.options.length; i++) {
        const opt = select.options[i];
        const searchVal = opt.getAttribute('data-search') || opt.text.toLowerCase();
        opt.style.display = (!q || searchVal.includes(q)) ? '' : 'none';
    }
}

function toggleItemForm() {
    const form = document.getElementById('item-form');
    const chevron = document.getElementById('item-chevron');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        form.classList.add('grid');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        form.classList.add('hidden');
        form.classList.remove('grid');
        chevron.style.transform = '';
    }
}
</script>
@endpush
