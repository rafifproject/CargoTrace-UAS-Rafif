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
    </div>
</div>

<form method="POST" action="{{ route('operator.shipments.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- LEFT: Form Fields --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- Informasi Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                    Informasi Pengiriman
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
                            <span class="text-[11px] text-cyan-600 dark:text-cyan-400 font-medium">Tersedia {{ count($carriers) }} Mitra</span>
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

            {{-- Rute --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                    Rute Pengiriman
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Lokasi Asal <span class="text-rose-500">*</span></label>
                        <select name="origin_location_id" id="origin-select" onchange="updateMap()" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('origin_location_id') border-rose-400 @enderror">
                            <option value="">— Pilih Lokasi Asal —</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->country }})</option>
                            @endforeach
                        </select>
                        @error('origin_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Lokasi Tujuan <span class="text-rose-500">*</span></label>
                        <select name="destination_location_id" id="dest-select" onchange="updateMap()" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('destination_location_id') border-rose-400 @enderror">
                            <option value="">— Pilih Lokasi Tujuan —</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->country }})</option>
                            @endforeach
                        </select>
                        @error('destination_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Spesifikasi & Jadwal --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-cyan-500"></i>
                    Spesifikasi & Jadwal
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
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jadwal Kedatangan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('expected_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Estimasi ETA <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="estimated_time_arrival" value="{{ old('estimated_time_arrival') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('estimated_time_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Catatan</label>
                        <textarea name="notes" rows="2" placeholder="Instruksi khusus, penanganan, atau catatan tambahan..." class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Optional Item --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <button type="button" onclick="toggleItemForm()" class="w-full px-5 py-4 flex items-center justify-between text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="box" class="w-4 h-4 text-cyan-500"></i>
                        Tambahkan Detail Item (Opsional)
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400" id="item-chevron"></i>
                </button>
                <div id="item-form" class="hidden px-5 pb-5 border-t border-slate-100 dark:border-slate-800 pt-4 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Barang</label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="cth. Laptop, Textile Rolls..." class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">HS Code</label>
                        <input type="text" name="hs_code" value="{{ old('hs_code') }}" placeholder="cth. 847130" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jumlah (pcs)</label>
                        <input type="number" name="item_qty" value="{{ old('item_qty', 1) }}" min="1" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nilai Pabean (USD)</label>
                        <input type="number" name="item_value" value="{{ old('item_value') }}" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-sm transition-colors shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pengiriman
                </button>
                <a href="{{ route('operator.shipments.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
            </div>
        </div>

        {{-- RIGHT: Live Map Preview --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden sticky top-6">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="map" class="w-4 h-4 text-cyan-500"></i>
                        Pratinjau Peta Rute
                    </h3>
                </div>
                <div id="route-map" style="height: 320px; background: #0A1128;"></div>
                <div class="px-5 py-3 text-xs text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800">
                    Pilih lokasi asal dan tujuan untuk melihat rute di peta.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Live Route Map
const routeMap = L.map('route-map', { zoomControl: true }).setView([10, 110], 3);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 18 }).addTo(routeMap);

let originMarker = null, destMarker = null, routeLine = null;

function updateMap() {
    const originSel = document.getElementById('origin-select');
    const destSel = document.getElementById('dest-select');
    const originOpt = originSel.options[originSel.selectedIndex];
    const destOpt = destSel.options[destSel.selectedIndex];

    if (originMarker) routeMap.removeLayer(originMarker);
    if (destMarker) routeMap.removeLayer(destMarker);
    if (routeLine) routeMap.removeLayer(routeLine);

    const oLat = parseFloat(originOpt.dataset.lat), oLng = parseFloat(originOpt.dataset.lng);
    const dLat = parseFloat(destOpt.dataset.lat), dLng = parseFloat(destOpt.dataset.lng);

    if (oLat && dLat) {
        originMarker = L.circleMarker([oLat, oLng], { radius: 8, color: '#10b981', fillColor: '#10b981', fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>Asal:</b> ${originOpt.text}`);
        destMarker = L.circleMarker([dLat, dLng], { radius: 8, color: '#f43f5e', fillColor: '#f43f5e', fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>Tujuan:</b> ${destOpt.text}`);
        routeLine = L.polyline([[oLat, oLng], [dLat, dLng]], { color: '#06b6d4', weight: 2, dashArray: '8,6' }).addTo(routeMap);
        routeMap.fitBounds([[oLat, oLng], [dLat, dLng]], { padding: [40, 40] });
    } else if (oLat) {
        originMarker = L.circleMarker([oLat, oLng], { radius: 8, color: '#10b981', fillColor: '#10b981', fillOpacity: 1 }).addTo(routeMap);
        routeMap.setView([oLat, oLng], 5);
    }
}

function filterCarrierOptions(term) {
    const q = (term || '').toLowerCase().trim();
    const select = document.getElementById('carrier_select');
    if (!select) return;

    let hasFirstMatch = false;
    for (let i = 1; i < select.options.length; i++) {
        const opt = select.options[i];
        const searchVal = opt.getAttribute('data-search') || opt.text.toLowerCase();
        if (!q || searchVal.includes(q)) {
            opt.style.display = '';
            if (!hasFirstMatch && q) {
                // Jangan paksa auto select agar user tetap punya kontrol, hanya sediakan opsi
                hasFirstMatch = true;
            }
        } else {
            opt.style.display = 'none';
        }
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

