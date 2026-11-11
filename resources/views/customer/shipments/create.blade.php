@extends('layouts.customer')

@section('title', 'Pesan Pengiriman Kargo Baru')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('customer.shipments.index') }}" class="hover:text-cyan-500">Pengiriman Saya</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Pesan Pengiriman Baru</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pesan Pengiriman Kargo Baru</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftarkan pesanan pengiriman internasional atau domestik Anda melalui jaringan ekspedisi resmi CargoTrace</p>
    </div>
    <a href="{{ route('customer.shipments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        <span>Kembali</span>
    </a>
</div>

<form method="POST" action="{{ route('customer.shipments.store') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- LEFT: Form Fields --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- Informasi Pemesanan --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                        <span>Informasi Pemesanan & Ekspedisi</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
                        Nomor Booking: {{ $suggestedNumber }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                Pilih Mitra Ekspedisi <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-cyan-600 dark:text-cyan-400 font-medium">{{ count($carriers) }} Mitra Ekspedisi Tersedia</span>
                        </div>
                        <div class="space-y-1.5">
                            <input type="text" id="carrier_filter" placeholder="Cari nama ekspedisi, kode, atau negara..." onkeyup="filterCarriers(this.value)" class="w-full px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                            <select id="carrier_select" name="carrier_id" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('carrier_id') border-rose-400 @enderror" required>
                                <option value="">— Pilih Mitra Ekspedisi Resmi —</option>
                                @foreach($carriers as $c)
                                    <option value="{{ $c->id }}" data-search="{{ strtolower($c->name . ' ' . $c->code . ' ' . $c->country) }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }}) — {{ $c->country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('carrier_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jenis Kargo / Komoditas <span class="text-rose-500">*</span></label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type') }}" placeholder="cth. Komoditas Kopi Ekspor, Elektronik, Manufaktur..." required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('cargo_type') border-rose-400 @enderror">
                        @error('cargo_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Rute Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                    <span>Rute Pelabuhan / Simpul Logistik</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelabuhan / Titik Asal <span class="text-rose-500">*</span></label>
                        <select name="origin_location_id" id="origin-select" onchange="updateRoutePreview()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('origin_location_id') border-rose-400 @enderror" required>
                            <option value="">— Pilih Titik Asal —</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('origin_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelabuhan / Titik Tujuan <span class="text-rose-500">*</span></label>
                        <select name="destination_location_id" id="dest-select" onchange="updateRoutePreview()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('destination_location_id') border-rose-400 @enderror" required>
                            <option value="">— Pilih Titik Tujuan —</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Spesifikasi Fisik & Jadwal --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-cyan-500"></i>
                    <span>Spesifikasi Kargo & Jadwal Keberangkatan</span>
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Total Bobot (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="weight" value="{{ old('weight', 1000) }}" placeholder="1000" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('weight') border-rose-400 @enderror">
                        @error('weight')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Volume (m³) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="volume" value="{{ old('volume', 10) }}" placeholder="10" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('volume') border-rose-400 @enderror">
                        @error('volume')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Rencana Berangkat <span class="text-rose-500">*</span></label>
                        <input type="date" name="expected_departure" value="{{ old('expected_departure', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('expected_departure') border-rose-400 @enderror">
                        @error('expected_departure')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Estimasi Tiba <span class="text-rose-500">*</span></label>
                        <input type="date" name="expected_arrival" value="{{ old('expected_arrival', date('Y-m-d', strtotime('+7 days'))) }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('expected_arrival') border-rose-400 @enderror">
                        @error('expected_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Detail Barang / Item Pertama (Opsional) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="boxes" class="w-4 h-4 text-cyan-500"></i>
                    <span>Rincian Barang Kargo (Manifest Item)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Item / Produk</label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="cth. Grade A Arabica Coffee Beans" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kode HS (Harmonized)</label>
                        <input type="text" name="hs_code" value="{{ old('hs_code') }}" placeholder="cth. 0901.11.00" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kuantitas (Unit/Box)</label>
                        <input type="number" name="item_qty" value="{{ old('item_qty', 100) }}" min="1" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nilai Deklarasi Barang (USD $)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                            <input type="number" step="0.01" name="item_value" value="{{ old('item_value', 15000) }}" placeholder="15000.00" class="w-full pl-7 pr-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Instruksi Khusus / Catatan Penanganan</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="cth. Suhu kontainer 20°C, perlakukan dengan hati-hati..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Interactive Map Route Preview & Summary --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Preview Rute Peta --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                        <span>Pratinjau Rute Pengiriman</span>
                    </h3>
                    <span id="route-distance-badge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-500">
                        Pilih Lokasi
                    </span>
                </div>
                <div id="routeMap" class="w-full h-64 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 z-10"></div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Garis putus-putus menunjukkan rute estimasi pelayaran dari pelabuhan asal menuju pelabuhan tujuan.
                </p>
            </div>

            {{-- Summary & Submit Button --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Ringkasan Pemesanan</h4>
                <div class="space-y-2 text-xs divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-400">
                    <div class="flex justify-between pt-1">
                        <span>Pemesan</span>
                        <span class="font-semibold text-slate-800 dark:text-white">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span>Perusahaan</span>
                        <span class="font-semibold text-slate-800 dark:text-white">{{ auth()->user()->company_name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span>Status Awal</span>
                        <span class="font-bold text-amber-600 dark:text-amber-400">Dipesan (Booked)</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="w-full py-2.5 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl text-xs shadow-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Kirim Pemesanan Pengiriman</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-2">
                        Setelah pesanan dikirim, sistem akan menerbitkan nomor pelacakan resmi dan mengirimkan notifikasi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let routeMap = null;
    let originMarker = null;
    let destMarker = null;
    let routePolyline = null;

    document.addEventListener('DOMContentLoaded', function () {
        initRouteMap();
        updateRoutePreview();
    });

    function initRouteMap() {
        if (!document.getElementById('routeMap')) return;

        routeMap = L.map('routeMap', {
            center: [10, 110],
            zoom: 3,
            minZoom: 1,
            maxZoom: 18,
            zoomControl: false
        });

        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Satelit'
        }).addTo(routeMap);
    }

    function updateRoutePreview() {
        if (!routeMap) return;

        const originSelect = document.getElementById('origin-select');
        const destSelect = document.getElementById('dest-select');

        const originOpt = originSelect.options[originSelect.selectedIndex];
        const destOpt = destSelect.options[destSelect.selectedIndex];

        const originLat = originOpt ? parseFloat(originOpt.getAttribute('data-lat')) : null;
        const originLng = originOpt ? parseFloat(originOpt.getAttribute('data-lng')) : null;
        const destLat = destOpt ? parseFloat(destOpt.getAttribute('data-lat')) : null;
        const destLng = destOpt ? parseFloat(destOpt.getAttribute('data-lng')) : null;

        if (originMarker) routeMap.removeLayer(originMarker);
        if (destMarker) routeMap.removeLayer(destMarker);
        if (routePolyline) routeMap.removeLayer(routePolyline);

        const latLngs = [];

        if (originLat && originLng) {
            originMarker = L.circleMarker([originLat, originLng], {
                radius: 7,
                fillColor: '#10B981',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Asal:</b> ${originOpt.text}`).addTo(routeMap);
            latLngs.push([originLat, originLng]);
        }

        if (destLat && destLng) {
            destMarker = L.circleMarker([destLat, destLng], {
                radius: 7,
                fillColor: '#3B82F6',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Tujuan:</b> ${destOpt.text}`).addTo(routeMap);
            latLngs.push([destLat, destLng]);
        }

        if (latLngs.length === 2) {
            routePolyline = L.polyline(latLngs, {
                color: '#38BDF8',
                weight: 3,
                dashArray: '6, 6',
                opacity: 0.9
            }).addTo(routeMap);

            routeMap.fitBounds(L.latLngBounds(latLngs), { padding: [30, 30] });

            const dist = calculateDistance(originLat, originLng, destLat, destLng);
            document.getElementById('route-distance-badge').textContent = `Estimasi Rute: ~${Math.round(dist)} NM`;
        } else if (latLngs.length === 1) {
            routeMap.setView(latLngs[0], 5);
            document.getElementById('route-distance-badge').textContent = 'Pilih Tujuan';
        } else {
            document.getElementById('route-distance-badge').textContent = 'Pilih Rute';
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 3440.065; // Nautical miles
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function filterCarriers(query) {
        const q = query.toLowerCase().trim();
        const select = document.getElementById('carrier_select');
        for (let i = 1; i < select.options.length; i++) {
            const opt = select.options[i];
            const search = opt.getAttribute('data-search') || '';
            opt.style.display = (!q || search.includes(q)) ? '' : 'none';
        }
    }
</script>
@endpush

