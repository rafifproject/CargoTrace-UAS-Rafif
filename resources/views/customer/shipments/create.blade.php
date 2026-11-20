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
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftarkan pesanan pengiriman internasional via Jalur Laut (Kapal) atau Jalur Udara (Pesawat Kargo)</p>
    </div>
    <a href="{{ route('customer.shipments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        <span>Kembali</span>
    </a>
</div>

<form method="POST" action="{{ route('customer.shipments.store') }}" id="shipmentBookingForm">
    @csrf
    {{-- Hidden input for selected transport mode --}}
    <input type="hidden" name="transport_mode" id="transport_mode" value="{{ old('transport_mode', 'SEA') }}">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- LEFT: Form Fields --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- PILIHAN MODA PENGIRIMAN: KAPAL LAUT vs PESAWAT UDARA vs MOBIL KARGO/DARAT --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        1. Pilih Jenis Moda Pengiriman <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Pilihan ini menentukan daftar simpul logistik & estimasi tarif</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Opsi 1: Multimoda Laut + Udara Domestik (Standar Ekspor Biji Kopi Brazil Santos) --}}
                    <div id="mode-card-sea" onclick="setTransportMode('SEA')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shadow-sm">
                                    🚢
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <span>Multimoda Samudra + Pesawat Domestik</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 font-bold">Rekomendasi Ekspor</span>
                                    </h4>
                                    <span class="text-[10px] text-slate-400">Kapal Kontainer Samudera ➔ Lanjut Pesawat Domestik ➔ Mobil Kurir</span>
                                </div>
                            </div>
                            <span id="badge-sea" class="text-[9px] font-bold px-2 py-0.5 rounded-full border">
                                Terpilih
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
                            Kontainer kargo diekspor via <strong>Kapal Laut dari Pelabuhan Santos (Brazil)</strong> melintasi samudra ke pelabuhan hub negara tujuan. Setibanya di negara tujuan, kargo dilanjutkan via <strong>Pesawat Kargo Domestik (Air Feeder)</strong> ke kota pemesan & mobil kurir ke alamat kafe.
                        </p>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Tarif Basis Multimoda:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">~$0.35 / kg &bull; Kapasitas Kontainer Penuh</span>
                        </div>
                    </div>

                    {{-- Opsi 2: Pesawat Kargo Kilat (Full Air Express) --}}
                    <div id="mode-card-air" onclick="setTransportMode('AIR')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shadow-sm">
                                    ✈️
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white">Pesawat Kargo Kilat (Air Cargo Express)</h4>
                                    <span class="text-[10px] text-slate-400">Bandara GRU São Paulo Direct Air Cargo ➔ Mobil Kurir</span>
                                </div>
                            </div>
                            <span id="badge-air" class="text-[9px] font-bold px-2 py-0.5 rounded-full border">
                                Pilih
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-3">
                            Penerbangan kargo udara kilat dari <strong>Bandara Internasional São Paulo (GRU)</strong> langsung ke bandara internasional negara tujuan untuk batch sampel biji kopi pesanan khusus dan prioritas tinggi.
                        </p>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Tarif Basis Udara Kilat:</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">~$2.80 / kg &bull; Estimasi Cepat 3-5 Hari</span>
                        </div>
                    </div>
                </div>
                @error('transport_mode')<p class="text-rose-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Informasi Ekspedisi & Komoditas Kopi Brazil Santos --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                        <span>2. Informasi Pemesanan & Komoditas Kopi Brazil Santos</span>
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

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jenis Kargo / Komoditas <span class="text-rose-500">*</span></label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Kopi Ekspor Brazil Santos (Specialty Coffee)') }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('cargo_type') border-rose-400 @enderror">
                        @error('cargo_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Varietas Kopi Brazil Santos</label>
                        <select name="coffee_variety" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="Kopi Brazil Santos Bourbon Specialty" {{ old('coffee_variety') === 'Kopi Brazil Santos Bourbon Specialty' ? 'selected' : '' }}>Kopi Brazil Santos Bourbon Specialty</option>
                            <option value="Brazil Santos Strictly Soft Fine Cup (SSFC)" {{ old('coffee_variety') === 'Brazil Santos Strictly Soft Fine Cup (SSFC)' ? 'selected' : '' }}>Brazil Santos Strictly Soft Fine Cup (SSFC)</option>
                            <option value="Brazil Santos Yellow Catuai Natural Micro-lot" {{ old('coffee_variety') === 'Brazil Santos Yellow Catuai Natural Micro-lot' ? 'selected' : '' }}>Brazil Santos Yellow Catuai Natural Micro-lot</option>
                            <option value="Brazil Santos Cerrado Mineiro Single-Origin" {{ old('coffee_variety') === 'Brazil Santos Cerrado Mineiro Single-Origin' ? 'selected' : '' }}>Brazil Santos Cerrado Mineiro Single-Origin</option>
                            <option value="Brazil Santos Decaf Swiss Water Process" {{ old('coffee_variety') === 'Brazil Santos Decaf Swiss Water Process' ? 'selected' : '' }}>Brazil Santos Decaf Swiss Water Process</option>
                            <option value="Brazil Santos Espresso Roast Master Blend" {{ old('coffee_variety') === 'Brazil Santos Espresso Roast Master Blend' ? 'selected' : '' }}>Brazil Santos Espresso Roast Master Blend</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Grade / Kualitas Kopi Ekspor</label>
                        <select name="coffee_grade" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="Specialty Grade 1 (SCA Score 84+)" {{ old('coffee_grade') === 'Specialty Grade 1 (SCA Score 84+)' ? 'selected' : '' }}>Specialty Grade 1 (SCA Score 84+) — Premium Green Beans</option>
                            <option value="Strictly Soft Fine Cup NY 2/3" {{ old('coffee_grade') === 'Strictly Soft Fine Cup NY 2/3' ? 'selected' : '' }}>Strictly Soft Fine Cup NY 2/3 — Export Standard</option>
                            <option value="Exclusive Micro-Lot Nano Batch" {{ old('coffee_grade') === 'Exclusive Micro-Lot Nano Batch' ? 'selected' : '' }}>Exclusive Micro-Lot Nano Batch — Roasted / Green</option>
                            <option value="Commercial Export Grade A" {{ old('coffee_grade') === 'Commercial Export Grade A' ? 'selected' : '' }}>Commercial Export Grade A</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. RUTE EKSPOR & ALAMAT LENGKAP PENGANTARAN (SANTOS BRAZIL -> NEGARA & KOTA TUJUAN) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                            <span id="route-section-heading">3. Rute Pengiriman Global & Alamat Tujuan Penerima</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pengiriman kargo kopi bermula langsung dari <strong>Pusat Ekspor Kopi Brazil Santos</strong> menuju pelabuhan dan alamat kafe/roastery tujuan.</p>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Geoapify Verified
                    </span>
                </div>

                {{-- TITIK ASAL EKSPOR TERKUNCI (SANTOS, BRAZIL) --}}
                <div class="p-3.5 rounded-xl border border-emerald-200 dark:border-emerald-800/80 bg-emerald-50/50 dark:bg-emerald-950/20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div id="origin-icon-badge" class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-lg shadow-sm">
                            🚢
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                <span>Titik Keberangkatan Kargo (Pusat Kopi Santos Brazil)</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            </div>
                            <div id="origin-display-text" class="text-xs font-bold text-slate-800 dark:text-white">Port of Santos (Santos, Brasil)</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/60 px-2 py-0.5 rounded-full border border-emerald-300 dark:border-emerald-700">
                        Pusat Eksportir Kopi Santos ✓
                    </span>
                    <input type="hidden" name="origin_location_id" id="origin-select" value="{{ old('origin_location_id', 39) }}" data-lat="-23.9610" data-lng="-46.3020">
                </div>

                {{-- VISUAL ALUR MULTIMODA LOGISTIK EKSPOR (Santos ➔ Negara Tujuan ➔ Pesawat Domestik ➔ Mobil Kurir) --}}
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="git-merge" class="w-3.5 h-3.5 text-cyan-500"></i>
                            <span>Alur Multimoda Realistis (Keberangkatan Brazil ➔ Pelabuhan ➔ Pesawat Kargo ➔ Kurir)</span>
                        </span>
                        <span class="text-[10px] font-semibold text-cyan-600 dark:text-cyan-400">Rantai Pasok Standar Industri</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div class="p-2.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-2.5 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg flex-shrink-0">
                                🚢
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-bold text-slate-800 dark:text-slate-100">1. Pelayaran Samudra</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Pelabuhan Santos ➔ Hub Tujuan</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-2.5 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg flex-shrink-0">
                                ✈️
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-bold text-slate-800 dark:text-slate-100">2. Pesawat Domestik</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Hub Masuk ➔ Bandara Kota</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-2.5 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg flex-shrink-0">
                                🚚
                            </div>
                            <div class="min-w-0">
                                <div class="text-[11px] font-bold text-slate-800 dark:text-slate-100">3. Mobil Kurir Paket</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Bandara ➔ Alamat Kafe/Toko</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM TUJUAN PENGANTARAN LENGKAP --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                    {{-- 1. Negara Tujuan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                            1. Negara Tujuan Kargo <span class="text-rose-500">*</span>
                        </label>
                        <select name="delivery_country" id="delivery_country" onchange="onCountrySelected(this.value)" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">— Pilih Negara Tujuan —</option>
                            @foreach($countries as $c)
                                <option value="{{ $c['name'] }}" data-iso="{{ $c['iso'] }}" {{ old('delivery_country', 'Indonesia') == $c['name'] ? 'selected' : '' }}>
                                    {{ $c['flag'] }} {{ $c['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Simpul Transit / Pelabuhan / Bandara Tujuan (Otomatis Filter Sesuai Negara) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label id="dest-label" class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                2. Pelabuhan Laut Transit Tujuan <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-cyan-500 font-semibold">Otomatis Terhubung</span>
                        </div>
                        <select name="destination_location_id" id="dest-select" onchange="updateRoutePreview()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('destination_location_id') border-rose-400 @enderror" required>
                            <option value="">— Pilih Simpul Transit Tujuan —</option>
                        </select>
                        @error('destination_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- 3. Kota Tujuan (Cascading dari Negara) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                3. Kota Tujuan Pengantaran <span class="text-rose-500">*</span>
                            </label>
                            <span id="city-spinner" class="hidden text-[10px] text-cyan-500 animate-pulse">Memuat kota...</span>
                        </div>
                        <select name="delivery_city" id="delivery_city" onchange="onCitySelected(this.value)" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">— Pilih Kota —</option>
                        </select>
                    </div>

                    {{-- 4. Daerah / Distrik (Cascading dari Kota) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                4. Daerah / Distrik / Suburb <span class="text-rose-500">*</span>
                            </label>
                            <span id="district-spinner" class="hidden text-[10px] text-cyan-500 animate-pulse">Memuat daerah...</span>
                        </div>
                        <select name="delivery_district" id="delivery_district" onchange="onDistrictSelected(this.value)" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">— Pilih Daerah / Kecamatan —</option>
                        </select>
                    </div>
                </div>

                {{-- Detail Alamat Jalan / Kafe / Roastery & Geocoding --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                        5. Alamat Spesifik Penerima (Nama Kafe / Roastery / Jalan & Nomor Gedung)
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="delivery_address" id="delivery_address" oninput="triggerGeocodeDebounce()" value="{{ old('delivery_address', 'Jl. Senopati No. 42, Roastery Kopi Santos') }}" placeholder="cth. Jl. Senopati No. 42 atau 38 Upper Montagu Street" class="flex-1 px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <button type="button" onclick="verifyAddressWithGeoapify()" class="px-3 py-2 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="locate-fixed" class="w-3.5 h-3.5"></i>
                            <span>Verifikasi Geoapify</span>
                        </button>
                    </div>

                    {{-- Hidden Lat & Lng --}}
                    <input type="hidden" name="delivery_lat" id="delivery_lat" value="{{ old('delivery_lat', '-6.2297') }}">
                    <input type="hidden" name="delivery_lng" id="delivery_lng" value="{{ old('delivery_lng', '106.8075') }}">

                    {{-- Geocoded Preview Box --}}
                    <div id="geocode-preview-box" class="mt-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-[11px] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-500 dark:text-slate-400">Koordinat Pengantaran Mobil:</span>
                            <span id="geocode-coords-display" class="font-mono font-bold text-slate-800 dark:text-slate-200">-6.2297, 106.8075</span>
                        </div>
                        <span id="geocode-status-badge" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                            ✓ Tergeocoding Presisi
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <input type="checkbox" name="is_last_mile" id="is_last_mile" value="1" checked class="w-4 h-4 text-cyan-600 rounded border-slate-300 focus:ring-cyan-500">
                        <label for="is_last_mile" class="text-xs text-slate-700 dark:text-slate-300 font-medium">
                            Aktifkan Layanan Pengantaran Mobil Kurir (Last-Mile Delivery) langsung dari pelabuhan/bandara ke alamat kafe/toko tujuan
                        </label>
                    </div>
                </div>
            </div>

            {{-- Spesifikasi Fisik & Jadwal --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-cyan-500"></i>
                    <span>4. Spesifikasi Kargo & Jadwal Keberangkatan</span>
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Total Bobot (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="weight" id="cargo-weight-input" oninput="calculateEstimates()" value="{{ old('weight', 1000) }}" placeholder="1000" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('weight') border-rose-400 @enderror">
                        @error('weight')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Volume (m³) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="volume" value="{{ old('volume', 10) }}" placeholder="10" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('volume') border-rose-400 @enderror">
                        @error('volume')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Rencana Berangkat <span class="text-rose-500">*</span></label>
                        <input type="date" name="expected_departure" id="expected_departure" value="{{ old('expected_departure', date('Y-m-d')) }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('expected_departure') border-rose-400 @enderror">
                        @error('expected_departure')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Estimasi Tiba <span class="text-rose-500">*</span></label>
                        <input type="date" name="expected_arrival" id="expected_arrival" value="{{ old('expected_arrival', date('Y-m-d', strtotime('+14 days'))) }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('expected_arrival') border-rose-400 @enderror">
                        @error('expected_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Detail Barang / Item Pertama (Opsional) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="boxes" class="w-4 h-4 text-cyan-500"></i>
                    <span>5. Rincian Barang Kargo (Manifest Item)</span>
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

        {{-- RIGHT: Interactive Map Route Preview & Cost Summary --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Preview Rute Peta --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                        <span id="map-preview-title">Pratinjau Rute Maritim</span>
                    </h3>
                    <span id="route-distance-badge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-500">
                        Pilih Lokasi
                    </span>
                </div>
                <div id="routeMap" class="w-full h-64 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 z-10"></div>
                <p id="route-map-caption" class="text-[11px] text-slate-400 mt-2">
                    Garis putus-putus menunjukkan rute estimasi pelayaran dari pelabuhan asal menuju pelabuhan tujuan.
                </p>
            </div>

            {{-- Kalkulator Tarif & Ringkasan Pemesanan --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Ringkasan & Biaya</h4>
                    <span id="summary-mode-pill" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        🚢 Kapal Laut
                    </span>
                </div>

                {{-- Detail Biaya Transparan (Customer Cost Breakdown) --}}
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500">Basis Tarif Kargo:</span>
                        <span id="rate-per-kg-text" class="font-mono font-semibold text-slate-700 dark:text-slate-200">$0.35 / kg</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500">Biaya Penanganan / Port Fee:</span>
                        <span id="handling-fee-text" class="font-mono font-semibold text-slate-700 dark:text-slate-200">$250.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500">Estimasi Waktu Tempuh:</span>
                        <span id="lead-time-text" class="font-semibold text-cyan-600 dark:text-cyan-400">7 - 21 Hari</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-700/80 flex justify-between items-center">
                        <div>
                            <div class="text-[11px] font-bold text-slate-800 dark:text-slate-200">Total Estimasi Biaya</div>
                            <div class="text-[10px] text-slate-400" id="cost-note-text">Pengiriman laut ekonomis</div>
                        </div>
                        <span id="total-cost-estimate" class="text-base font-extrabold font-mono text-cyan-600 dark:text-cyan-400">
                            $600.00
                        </span>
                    </div>
                </div>

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
                        Setelah pesanan dikirim, sistem akan menerbitkan nomor pelacakan resmi dan mengirimkan konfirmasi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Data Pelabuhan dan Bandara dari Backend
    const portLocations = @json($portLocations);
    const airportLocations = @json($airportLocations);

    // Nilai awal dari old input atau default
    const initialMode = @json(old('transport_mode', 'SEA'));
    const initialOriginId = @json(old('origin_location_id'));
    const initialDestId = @json(old('destination_location_id'));

    // URL dinamis API Geoapify via Laravel route helpers
    const GEO_CITIES_URL = @json(route('geo.cities'));
    const GEO_DISTRICTS_URL = @json(route('geo.districts'));
    const GEO_GEOCODE_URL = @json(route('geo.geocode'));

    // Master data kota instan (Zero latency - tampil seketika sesuai negara yang dipilih)
    const fallbackCitiesMap = {
        'Indonesia': ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Denpasar (Bali)', 'Palembang', 'Yogyakarta', 'Balikpapan', 'Batam', 'Malang', 'Pekanbaru', 'Bandar Lampung', 'Banjarmasin', 'Pontianak', 'Manado', 'Cilegon', 'Samarinda'],
        'Brunei Darussalam': ['Bandar Seri Begawan', 'Kuala Belait', 'Seria', 'Tutong', 'Bangar', 'Muara', 'Jerudong', 'Gadong', 'Sengkurong', 'Berakas', 'Lumut', 'Sungai Liang'],
        'Brunei': ['Bandar Seri Begawan', 'Kuala Belait', 'Seria', 'Tutong', 'Bangar', 'Muara', 'Jerudong', 'Gadong', 'Sengkurong', 'Berakas', 'Lumut', 'Sungai Liang'],
        'Malaysia': ['Kuala Lumpur', 'George Town (Penang)', 'Johor Bahru', 'Port Klang', 'Ipoh', 'Kota Kinabalu', 'Kuching', 'Shah Alam', 'Melaka', 'Petaling Jaya', 'Alor Setar', 'Kuantan', 'Miri', 'Sandakan'],
        'Singapura': ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas', 'Bedok', 'Yishun', 'Ang Mo Kio'],
        'Singapore': ['Singapore', 'Jurong', 'Changi', 'Tampines', 'Woodlands', 'Marina Bay', 'Tuas', 'Bedok', 'Yishun', 'Ang Mo Kio'],
        'Thailand': ['Bangkok', 'Nonthaburi', 'Chiang Mai', 'Phuket', 'Pattaya', 'Chonburi', 'Hat Yai', 'Nakhon Ratchasima', 'Udon Thani', 'Laem Chabang', 'Samut Prakan'],
        'Vietnam': ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Hai Phong', 'Can Tho', 'Bien Hoa', 'Nha Trang', 'Vung Tau', 'Hue', 'Buon Ma Thuot'],
        'Filipina': ['Manila', 'Quezon City', 'Davao City', 'Cebu City', 'Caloocan', 'Zamboanga', 'Taguig', 'Pasig', 'Cagayan de Oro', 'Makati'],
        'Philippines': ['Manila', 'Quezon City', 'Davao City', 'Cebu City', 'Caloocan', 'Zamboanga', 'Taguig', 'Pasig', 'Cagayan de Oro', 'Makati'],
        'Myanmar': ['Yangon', 'Mandalay', 'Naypyidaw', 'Mawlamyine', 'Bago', 'Pathein', 'Monywa', 'Sittwe', 'Taunggyi'],
        'Kamboja': ['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampong Cham', 'Poipet', 'Kampot'],
        'Cambodia': ['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampong Cham', 'Poipet', 'Kampot'],
        'Laos': ['Vientiane', 'Luang Prabang', 'Pakse', 'Savannakhet', 'Thakhek', 'Vang Vieng'],
        'Timor Leste': ['Dili', 'Baucau', 'Maliana', 'Suai', 'Ermera', 'Liquiçá', 'Lospalos', 'Ainaro', 'Oecusse'],
        'Tiongkok': ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu', 'Hangzhou', 'Wuhan', 'Chongqing', 'Dalian'],
        'China': ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen', 'Ningbo', 'Tianjin', 'Qingdao', 'Xiamen', 'Hong Kong', 'Chengdu', 'Hangzhou', 'Wuhan', 'Chongqing', 'Dalian'],
        'Jepang': ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima', 'Sendai', 'Kitakyushu'],
        'Japan': ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Hiroshima', 'Sendai', 'Kitakyushu'],
        'Korea Selatan': ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon', 'Changwon', 'Seongnam', 'Goyang'],
        'South Korea': ['Seoul', 'Busan', 'Incheon', 'Daegu', 'Daejeon', 'Gwangju', 'Ulsan', 'Suwon', 'Changwon', 'Seongnam', 'Goyang'],
        'Taiwan': ['Taipei', 'Kaohsiung', 'Taichung', 'Tainan', 'Taoyuan', 'Hsinchu', 'Keelung'],
        'Hong Kong': ['Central', 'Kowloon', 'Tsuen Wan', 'Sha Tin', 'Tuen Mun', 'Kwun Tong', 'Wan Chai'],
        'India': ['Mumbai', 'Delhi', 'Bengaluru', 'Chennai', 'Kolkata', 'Hyderabad', 'Ahmedabad', 'Pune', 'Surat', 'Kochi', 'Visakhapatnam'],
        'Pakistan': ['Karachi', 'Lahore', 'Faisalabad', 'Rawalpindi', 'Gujranwala', 'Peshawar', 'Multan', 'Islamabad', 'Gwadar', 'Quetta'],
        'Bangladesh': ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Sylhet', 'Gazipur', 'Barisal', 'Rangpur'],
        'Sri Lanka': ['Colombo', 'Kandy', 'Galle', 'Jaffna', 'Negombo', 'Hambantota', 'Trincomalee'],
        'Arab Saudi': ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Khobar', 'Jubail', 'Yanbu', 'Tabuk', 'Abha'],
        'Uni Emirat Arab': ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Al Ain'],
        'United Arab Emirates': ['Dubai', 'Abu Dhabi', 'Sharjah', 'Jebel Ali', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Al Ain'],
        'Qatar': ['Doha', 'Al Rayyan', 'Al Wakrah', 'Al Khor', 'Mesaieed', 'Lusail'],
        'Kuwait': ['Kuwait City', 'Al Ahmadi', 'Hawalli', 'Salmiya', 'Sabah Al Salem', 'Shuwaikh'],
        'Oman': ['Muscat', 'Salalah', 'Sohar', 'Seeb', 'Nizwa', 'Sur', 'Duqm'],
        'Bahrain': ['Manama', 'Riffa', 'Muharraq', 'Hamad Town', 'Sitra'],
        'Turki': ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Adana', 'Mersin', 'Gaziantep', 'Trabzon'],
        'Inggris Raya': ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle', 'Belfast', 'Cardiff'],
        'United Kingdom': ['London', 'Manchester', 'Birmingham', 'Liverpool', 'Edinburgh', 'Glasgow', 'Bristol', 'Leeds', 'Southampton', 'Newcastle', 'Belfast', 'Cardiff'],
        'Belanda': ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen', 'Maastricht'],
        'Netherlands': ['Rotterdam', 'Amsterdam', 'The Hague', 'Utrecht', 'Eindhoven', 'Groningen', 'Tilburg', 'Almere', 'Breda', 'Nijmegen', 'Maastricht'],
        'Jerman': ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig', 'Nuremberg', 'Dresden'],
        'Germany': ['Hamburg', 'Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Bremen', 'Hannover', 'Leipzig', 'Nuremberg', 'Dresden'],
        'Prancis': ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg', 'Montpellier'],
        'France': ['Paris', 'Marseille', 'Le Havre', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg', 'Montpellier'],
        'Italia': ['Rome', 'Milan', 'Genoa', 'Naples', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venice', 'Verona', 'Bari'],
        'Italy': ['Rome', 'Milan', 'Genoa', 'Naples', 'Turin', 'Palermo', 'Bologna', 'Florence', 'Trieste', 'Venice', 'Verona', 'Bari'],
        'Spanyol': ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Malaga', 'Bilbao', 'Alicante', 'Cadiz', 'Algeciras'],
        'Spain': ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Malaga', 'Bilbao', 'Alicante', 'Cadiz', 'Algeciras'],
        'Portugal': ['Lisbon', 'Porto', 'Sines', 'Braga', 'Coimbra', 'Setubal', 'Faro'],
        'Belgia': ['Brussels', 'Antwerp', 'Ghent', 'Charleroi', 'Liège', 'Bruges'],
        'Swiss': ['Zurich', 'Geneva', 'Basel', 'Bern', 'Lausanne', 'Lucerne'],
        'Rusia': ['Moskow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],
        'Russia': ['Moscow', 'Saint Petersburg', 'Novosibirsk', 'Vladivostok', 'Krasnoyarsk', 'Yekaterinburg', 'Kazan', 'Nizhny Novgorod', 'Samara', 'Novorossiysk'],
        'Brazil': ['Santos', 'São Paulo', 'Rio de Janeiro', 'Campinas', 'Belo Horizonte', 'Curitiba', 'Porto Alegre', 'Salvador', 'Recife', 'Brasília', 'Vitória', 'Paranaguá', 'Fortaleza', 'Manaus', 'Belém'],
        'Amerika Serikat': ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas', 'Philadelphia', 'Washington D.C.', 'New Orleans'],
        'United States': ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Miami', 'Seattle', 'San Francisco', 'Boston', 'Atlanta', 'Dallas', 'Philadelphia', 'Washington D.C.', 'New Orleans'],
        'Australia': ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Fremantle', 'Darwin', 'Hobart', 'Canberra', 'Newcastle']
    };

    // Master data daerah / distrik instan
    const fallbackDistrictsMap = {
        // Brunei
        'Bandar Seri Begawan': ['Kiulap', 'Gadong Commercial Area', 'Berakas', 'Kumbang Pasang', 'Mabohai', 'Bandar Central', 'Muara Port Basin', 'Kota Batu', 'Pangkalan Gadong', 'Tasek Lama'],
        'Kuala Belait': ['KB Town', 'Pandan', 'Mumong', 'Kuala Balai', 'Sungai Liang', 'Labi Industrial Hub'],
        'Seria': ['Pekan Seria', 'Lorong Tiga', 'Badas', 'Anduki Airfield', 'Panaga'],
        'Tutong': ['Pekan Tutong', 'Telisai', 'Tanjong Maya', 'Keriam', 'Kiudang', 'Lamunin'],
        'Muara': ['Muara Town Terminal', 'Serasa Port Zone', 'Pelabuhan Muara', 'Kapok', 'Meragang'],
        'Bangar': ['Pekan Bangar', 'Labu Estate', 'Batu Apoi', 'Amo', 'Bokok'],
        'Jerudong': ['Jerudong Park Area', 'Sengkurong', 'Tanjong Bunut', 'Mulaut'],
        'Gadong': ['Gadong Central', 'Menglait', 'Pangkalan Gadong', 'Kiulap Commercial Hub'],

        // Indonesia
        'Jakarta': ['Kebayoran Baru (Senopati)', 'Menteng', 'Setiabudi (Kuningan)', 'Kelapa Gading', 'Pantai Indah Kapuk (PIK)', 'Senayan', 'Kemang', 'Tanjung Priok', 'Gambir', 'Cilandak', 'Pondok Indah', 'Tebet'],
        'Surabaya': ['Gubeng', 'Wonokromo', 'Tegalsari', 'Rungkut Industri', 'Sukolilo', 'Tanjung Perak Port', 'Genteng', 'Kenjeran', 'Sambikerep'],
        'Bandung': ['Coblong (Dago)', 'Sumur Bandung (Braga)', 'Sukajadi (PVJ)', 'Cidadap (Setiabudi)', 'Lengkong', 'Batununggal', 'Gedebage'],
        'Medan': ['Medan Kota', 'Medan Baru', 'Medan Petisah', 'Medan Barat', 'Medan Sunggal', 'Belawan Port Area'],
        'Semarang': ['Semarang Tengah', 'Semarang Barat', 'Semarang Selatan', 'Candisari', 'Banyumanik', 'Tanjung Emas Port'],
        'Denpasar (Bali)': ['Denpasar Selatan (Sanur)', 'Kuta', 'Seminyak', 'Canggu', 'Ubud', 'Denpasar Barat', 'Denpasar Timur'],
        'Yogyakarta': ['Danurejan (Malioboro)', 'Gondokusuman', 'Kraton', 'Depok (Sleman)', 'Kotagede'],

        // Malaysia, Singapura & Thailand
        'Kuala Lumpur': ['KLCC Bukit Bintang', 'Bangsar', 'Mont Kiara', 'Damansara', 'Cheras', 'Sentul', 'Port Klang Gateway'],
        'George Town (Penang)': ['Gurney Drive', 'Bayan Lepas', 'Georgetown Heritage', 'Tanjung Bungah', 'Batu Ferringhi'],
        'Singapore': ['Tanjong Pagar', 'Orchard', 'Marina Bay', 'Changi Air Freight Center', 'Jurong Industrial Estate', 'Bugis', 'Tiong Bahru', 'Tuas Port Hub'],
        'Bangkok': ['Sukhumvit', 'Silom', 'Sathorn', 'Chatuchak', 'Bang Na Logistics Hub', 'Khlong Toei Port Area'],

        // Global
        'London': ['Westminster (Montagu St)', 'City of London', 'Camden', 'Kensington & Chelsea', 'Islington', 'Hackney', 'Greenwich'],
        'New York': ['Manhattan (Midtown)', 'Brooklyn (Williamsburg)', 'Queens (Astoria)', 'Financial District', 'SoHo', 'Chelsea'],
        'Santos': ['Centro Histórico', 'Gonzaga', 'Boqueirão', 'Ponta da Praia', 'Porto de Santos', 'Aparecida'],
        'São Paulo': ['Vila Madalena', 'Pinheiros', 'Jardins', 'Itaim Bibi', 'Moema', 'Centro', 'Paulista'],
        'Rotterdam': ['Centrum', 'Feijenoord', 'Delfshaven', 'Kralingen', 'Waalhaven (Port)']
    };

    let currentMode = initialMode;
    let routeMap = null;
    let originMarker = null;
    let destMarker = null;
    let routePolyline = null;
    let planeMarker = null;
    let deliveryMarker = null;
    let lastMilePolyline = null;
    let geocodeTimeout = null;

    document.addEventListener('DOMContentLoaded', function () {
        initRouteMap();
        setTransportMode(initialMode, false);
        calculateEstimates();

        // Load kota awal jika negara default sudah terpilih
        const countryEl = document.getElementById('delivery_country');
        if (countryEl && countryEl.value) {
            onCountrySelected(countryEl.value);
        }
    });

    /**
     * Mengatur Moda Pengiriman: SEA (Kapal Laut), AIR (Pesawat Udara), atau LAND (Mobil / Truk Kargo)
     */
    function setTransportMode(mode, resetSelections = true) {
        currentMode = mode;
        document.getElementById('transport_mode').value = mode;

        const cardSea = document.getElementById('mode-card-sea');
        const cardAir = document.getElementById('mode-card-air');
        const cardLand = document.getElementById('mode-card-land');
        const badgeSea = document.getElementById('badge-sea');
        const badgeAir = document.getElementById('badge-air');
        const badgeLand = document.getElementById('badge-land');

        const heading = document.getElementById('route-section-heading');
        const destLabel = document.getElementById('dest-label');
        const originIcon = document.getElementById('origin-icon-badge');
        const originText = document.getElementById('origin-display-text');
        const originInput = document.getElementById('origin-select');

        const mapTitle = document.getElementById('map-preview-title');
        const mapCaption = document.getElementById('route-map-caption');
        const summaryPill = document.getElementById('summary-mode-pill');

        // Reset all selector cards to neutral
        [cardSea, cardAir, cardLand].forEach(c => {
            if (c) c.className = 'cursor-pointer relative p-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-[#0A1128] hover:border-slate-300 dark:hover:border-slate-700 flex flex-col justify-between';
        });
        [badgeSea, badgeAir, badgeLand].forEach(b => {
            if (b) {
                b.className = 'text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700';
                b.textContent = 'Pilih';
            }
        });

        if (mode === 'AIR') {
            if (cardAir) cardAir.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-purple-500 bg-purple-50/40 dark:bg-purple-950/20 shadow-sm flex flex-col justify-between';
            if (badgeAir) {
                badgeAir.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-purple-600 text-white border-purple-600';
                badgeAir.textContent = 'Terpilih ✓';
            }

            if (heading) heading.innerHTML = '3. Rute Kargo Udara Kilat (Bandara São Paulo GRU & Bandara Tujuan)';
            if (destLabel) destLabel.innerHTML = '2. Bandara Internasional Masuk Negara Tujuan <span class="text-rose-500">*</span>';
            if (originIcon) originIcon.textContent = '✈️';
            if (originText) originText.textContent = 'Sao Paulo/Guarulhos International Cargo (GRU) — Brasil';
            if (originInput) {
                originInput.value = '93';
                originInput.setAttribute('data-lat', '-23.4356');
                originInput.setAttribute('data-lng', '-46.4731');
            }

            if (mapTitle) mapTitle.textContent = 'Pratinjau Rute Udara (Air Corridor Express)';
            if (mapCaption) mapCaption.textContent = 'Penerbangan kargo udara langsung dari Bandara São Paulo GRU ke Bandara Internasional tujuan, dilanjutkan mobil kurir ke alamat toko.';
            if (summaryPill) {
                summaryPill.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300';
                summaryPill.textContent = '✈️ Pesawat Udara (Air Express)';
            }

            // Atur default tanggal tiba kilat (2-3 hari)
            const depVal = document.getElementById('expected_departure').value;
            if (depVal) {
                const depDate = new Date(depVal);
                depDate.setDate(depDate.getDate() + 3);
                document.getElementById('expected_arrival').value = depDate.toISOString().split('T')[0];
            }
        } else {
            if (cardSea) cardSea.className = 'cursor-pointer relative p-4 rounded-xl border-2 border-blue-500 bg-blue-50/40 dark:bg-blue-950/20 shadow-sm flex flex-col justify-between';
            if (badgeSea) {
                badgeSea.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-blue-600 text-white border-blue-600';
                badgeSea.textContent = 'Terpilih ✓';
            }

            if (heading) heading.innerHTML = '3. Rute Multimoda Terpadu (Pelabuhan Santos ➔ Hub Tujuan ➔ Pesawat Domestik ➔ Mobil Kurir)';
            if (destLabel) destLabel.innerHTML = '2. Pelabuhan Laut Masuk Negara Tujuan <span class="text-rose-500">*</span>';
            if (originIcon) originIcon.textContent = '🚢';
            if (originText) originText.textContent = 'Port of Santos (Santos, Brasil) — Simpul Ekspor Maritim Kopi';
            if (originInput) {
                originInput.value = '39';
                originInput.setAttribute('data-lat', '-23.9610');
                originInput.setAttribute('data-lng', '-46.3020');
            }

            if (mapTitle) mapTitle.textContent = 'Pratinjau Multimoda (Samudra + Pesawat Domestik)';
            if (mapCaption) mapCaption.textContent = 'Kapal kontainer berlayar dari Pelabuhan Santos ke pelabuhan hub negara tujuan, dilanjutkan pesawat kargo domestik ke kota pemesan & mobil kurir ke alamat kafe.';
            if (summaryPill) {
                summaryPill.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300';
                summaryPill.textContent = '🚢 Multimoda Samudra + Pesawat Domestik';
            }

            // Atur default tanggal tiba laut (14-18 hari)
            const depVal = document.getElementById('expected_departure').value;
            if (depVal) {
                const depDate = new Date(depVal);
                depDate.setDate(depDate.getDate() + 14);
                document.getElementById('expected_arrival').value = depDate.toISOString().split('T')[0];
            }
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }

        calculateEstimates();

        const countryEl = document.getElementById('delivery_country');
        if (countryEl && countryEl.value) {
            updateDestinationNodesByCountry(countryEl.value);
        } else {
            updateRoutePreview();
        }
    }

    /**
     * Memfilter Simpul Transit Tujuan (Pelabuhan/Bandara) Otomatis Sesuai Negara Tujuan
     */
    function updateDestinationNodesByCountry(selectedCountry) {
        if (!selectedCountry) return;
        const destSelect = document.getElementById('dest-select');
        if (!destSelect) return;

        const isAir = (currentMode === 'AIR');
        const pool = isAir ? airportLocations : (currentMode === 'LAND' ? portLocations.concat(airportLocations) : portLocations);
        const cleanC = selectedCountry.toLowerCase().trim();

        // Cari lokasi yang negaranya cocok
        let matching = pool.filter(loc => {
            const locC = (loc.country || '').toLowerCase().trim();
            return locC === cleanC || locC.includes(cleanC) || cleanC.includes(locC);
        });

        // Fallback jika belum ada yang spesifik negara tersebut
        if (matching.length === 0) {
            matching = pool;
        }

        let html = '';
        matching.forEach((loc, idx) => {
            const isSel = (initialDestId && String(loc.id) === String(initialDestId)) || idx === 0;
            html += `<option value="${loc.id}" data-lat="${loc.latitude}" data-lng="${loc.longitude}" data-country="${loc.country}" data-city="${loc.city}" ${isSel ? 'selected' : ''}>${loc.name} (${loc.city ? loc.city + ', ' : ''}${loc.country})</option>`;
        });

        destSelect.innerHTML = html;
        if (matching.length > 0) {
            const currentVal = destSelect.value;
            if (!currentVal || !matching.some(m => String(m.id) === String(currentVal))) {
                destSelect.value = matching[0].id;
            }
        }

        // Cek apakah kota sudah terpilih untuk smart alignment
        const cityEl = document.getElementById('delivery_city');
        if (cityEl && cityEl.value) {
            alignPortWithCity(cityEl.value);
        } else {
            updateRoutePreview();
        }
    }

    /**
     * Smart Port Alignment: Menyesuaikan pelabuhan tujuan dengan kota pengantaran yang dipilih
     * Contoh: Kota Surabaya -> Otomatis pilih Pelabuhan Tanjung Perak
     *         Kota Jakarta  -> Otomatis pilih Pelabuhan Tanjung Priok
     */
    function alignPortWithCity(cityName) {
        const destSelect = document.getElementById('dest-select');
        if (!destSelect || !cityName) return;
        const cleanCity = cityName.toLowerCase().trim();

        for (let opt of destSelect.options) {
            const portCity = (opt.getAttribute('data-city') || '').toLowerCase().trim();
            const portText = opt.textContent.toLowerCase();
            if ((portCity && (portCity.includes(cleanCity) || cleanCity.includes(portCity))) || portText.includes(cleanCity)) {
                destSelect.value = opt.value;
                break;
            }
        }
        updateRoutePreview();
    }

    /**
     * Cascading Dropdowns: Negara -> Kota
     */
    async function onCountrySelected(country) {
        if (!country) return;
        updateDestinationNodesByCountry(country);

        const citySelect = document.getElementById('delivery_city');
        const districtSelect = document.getElementById('delivery_district');
        const spinner = document.getElementById('city-spinner');

        // Cari daftar kota yang benar-benar cocok dengan negara yang dipilih
        let baseCities = null;
        const cleanCountry = country.toLowerCase().trim();
        for (const [k, v] of Object.entries(fallbackCitiesMap)) {
            const kLower = k.toLowerCase();
            if (cleanCountry === kLower || cleanCountry.includes(kLower) || kLower.includes(cleanCountry)) {
                baseCities = v;
                break;
            }
        }

        if (baseCities && baseCities.length > 0) {
            let opts = '<option value="">— Pilih Kota Tujuan —</option>';
            baseCities.forEach((city, idx) => {
                const isSel = (idx === 0) ? 'selected' : '';
                opts += `<option value="${city}" ${isSel}>${city}</option>`;
            });
            citySelect.innerHTML = opts;
            citySelect.value = baseCities[0];
            onCitySelected(baseCities[0]);
        } else {
            citySelect.innerHTML = `<option value="">Memuat kota ${country} via Geoapify...</option>`;
            districtSelect.innerHTML = '<option value="">— Pilih Daerah Setelah Kota —</option>';
        }

        // Fetch data live dari Geoapify Search API melalui route dinamis Laravel
        if (spinner) spinner.classList.remove('hidden');

        try {
            const res = await fetch(`${GEO_CITIES_URL}?country=${encodeURIComponent(country)}`);
            if (res.ok) {
                const json = await res.json();
                if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                    const citiesList = json.data;
                    const selectedNow = citySelect.value;
                    let refreshedOpts = '<option value="">— Pilih Kota Tujuan —</option>';
                    citiesList.forEach(city => {
                        const isSel = (city === selectedNow) ? 'selected' : '';
                        refreshedOpts += `<option value="${city}" ${isSel}>${city}</option>`;
                    });
                    citySelect.innerHTML = refreshedOpts;
                    if (!citySelect.value && citiesList[0]) {
                        citySelect.value = citiesList[0];
                        onCitySelected(citiesList[0]);
                    }
                }
            }
        } catch (e) {
            console.warn("Live Geoapify city fetch warning:", e);
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
    }

    /**
     * Cascading Dropdowns: Kota -> Daerah
     */
    async function onCitySelected(city) {
        if (!city) return;
        alignPortWithCity(city);

        const country = document.getElementById('delivery_country').value;
        const districtSelect = document.getElementById('delivery_district');
        const spinner = document.getElementById('district-spinner');

        // Cari daftar distrik/kecamatan yang cocok dengan kota
        let matchedDistricts = null;
        const cityClean = city.toLowerCase().trim();
        for (const [k, v] of Object.entries(fallbackDistrictsMap)) {
            const kLower = k.toLowerCase();
            if (cityClean === kLower || cityClean.includes(kLower) || kLower.includes(cityClean)) {
                matchedDistricts = v;
                break;
            }
        }

        if (matchedDistricts && matchedDistricts.length > 0) {
            let opts = '<option value="">— Pilih Daerah / Kecamatan —</option>';
            matchedDistricts.forEach((d, idx) => {
                const isSel = (idx === 0) ? 'selected' : '';
                opts += `<option value="${d}" ${isSel}>${d}</option>`;
            });
            districtSelect.innerHTML = opts;
            districtSelect.value = matchedDistricts[0];
            onDistrictSelected(matchedDistricts[0]);
        } else {
            districtSelect.innerHTML = `<option value="">Memuat daerah ${city} via Geoapify...</option>`;
        }

        // Fetch live districts via backend
        if (spinner) spinner.classList.remove('hidden');

        try {
            const res = await fetch(`${GEO_DISTRICTS_URL}?country=${encodeURIComponent(country)}&city=${encodeURIComponent(city)}`);
            if (res.ok) {
                const json = await res.json();
                if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                    const districtsList = json.data;
                    const selectedNow = districtSelect.value;
                    let refreshedOpts = '<option value="">— Pilih Daerah / Kecamatan —</option>';
                    districtsList.forEach(d => {
                        const isSel = (d === selectedNow) ? 'selected' : '';
                        refreshedOpts += `<option value="${d}" ${isSel}>${d}</option>`;
                    });
                    districtSelect.innerHTML = refreshedOpts;
                    if (!districtSelect.value && districtsList[0]) {
                        districtSelect.value = districtsList[0];
                        onDistrictSelected(districtsList[0]);
                    }
                }
            }
        } catch (e) {
            console.warn("Live Geoapify district fetch warning:", e);
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
        triggerGeocodeDebounce();
    }

    function onDistrictSelected(district) {
        triggerGeocodeDebounce();
    }

    function triggerGeocodeDebounce() {
        clearTimeout(geocodeTimeout);
        geocodeTimeout = setTimeout(verifyAddressWithGeoapify, 600);
    }

    /**
     * Verifikasi Geocoding Presisi menggunakan Geoapify API
     */
    async function verifyAddressWithGeoapify() {
        const country = document.getElementById('delivery_country').value;
        const city = document.getElementById('delivery_city').value;
        const district = document.getElementById('delivery_district').value;
        const address = document.getElementById('delivery_address').value;
        const coordsDisplay = document.getElementById('geocode-coords-display');
        const statusBadge = document.getElementById('geocode-status-badge');

        if (!city && !country && !address) return;

        if (coordsDisplay) coordsDisplay.textContent = 'Memverifikasi geocoding...';

        try {
            const query = `${address}, ${district}, ${city}, ${country}`;
            const res = await fetch(`${GEO_GEOCODE_URL}?address=${encodeURIComponent(address)}&district=${encodeURIComponent(district)}&city=${encodeURIComponent(city)}&country=${encodeURIComponent(country)}`);
            if (res.ok) {
                const json = await res.json();

                if (json.success && json.data && json.data.lat) {
                    const lat = json.data.lat;
                    const lng = json.data.lng;

                    document.getElementById('delivery_lat').value = lat;
                    document.getElementById('delivery_lng').value = lng;

                    if (coordsDisplay) coordsDisplay.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)} (${json.data.city || city}, ${json.data.district || district})`;
                    if (statusBadge) {
                        statusBadge.textContent = '✓ Terverifikasi Geoapify';
                        statusBadge.className = 'text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800';
                    }

                    updateLastMileMapMarker(lat, lng, json.data.formatted || query);
                    return;
                }
            }
        } catch (err) {
            console.warn("Geocoding fetch warning:", err);
        }

        // Fallback koordinat jika offline/gagal
        if (coordsDisplay && (!document.getElementById('delivery_lat').value || document.getElementById('delivery_lat').value === '-6.2297')) {
            coordsDisplay.textContent = `-6.2297, 106.8075 (${city || 'Jakarta'}, ${district || 'Senopati'})`;
        }
    }

    function updateLastMileMapMarker(lat, lng, label) {
        if (!routeMap) return;
        if (deliveryMarker) routeMap.removeLayer(deliveryMarker);

        const truckIcon = L.divIcon({
            html: '<div style="font-size:20px; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.6));">🚚📍</div>',
            className: 'delivery-truck-pin',
            iconSize: [28, 28],
            iconAnchor: [14, 24]
        });

        deliveryMarker = L.marker([lat, lng], { icon: truckIcon })
            .bindPopup(`<b>Alamat Pengantaran Mobil Kopi Santos:</b><br>${label}`)
            .addTo(routeMap);

        // Update garis last mile dari simpul transit (pelabuhan/bandara) ke mobil kurir
        const destSelect = document.getElementById('dest-select');
        if (destSelect && destSelect.selectedIndex >= 0) {
            const destOpt = destSelect.options[destSelect.selectedIndex];
            if (destOpt && destOpt.value) {
                const dLat = parseFloat(destOpt.getAttribute('data-lat'));
                const dLng = parseFloat(destOpt.getAttribute('data-lng'));
                if (dLat && dLng) {
                    if (lastMilePolyline) routeMap.removeLayer(lastMilePolyline);
                    lastMilePolyline = L.polyline([[dLat, dLng], [lat, lng]], {
                        color: '#F59E0B',
                        weight: 2.5,
                        dashArray: '3, 4',
                        opacity: 0.95
                    }).addTo(routeMap);
                }
            }
        }
    }

    /**
     * Hitung Estimasi Biaya & Durasi Kargo Secara Transparan
     */
    function calculateEstimates() {
        const weightInput = document.getElementById('cargo-weight-input');
        const weight = parseFloat(weightInput.value) || 0;

        const ratePerKgText = document.getElementById('rate-per-kg-text');
        const handlingFeeText = document.getElementById('handling-fee-text');
        const leadTimeText = document.getElementById('lead-time-text');
        const costNoteText = document.getElementById('cost-note-text');
        const totalCostEstimate = document.getElementById('total-cost-estimate');

        let ratePerKg = 0.35;
        let handlingFee = 250;
        let leadTime = '7 - 21 Hari';
        let note = 'Pengiriman kontainer laut ekonomis (Santos Port)';

        if (currentMode === 'AIR') {
            ratePerKg = 2.80;      // Kargo udara kilat
            handlingFee = 450;     // Airport handling & security
            leadTime = '1 - 3 Hari (Super Kilat)';
            note = 'Pengiriman udara prioritas kilat (GRU Airport)';
        } else if (currentMode === 'LAND') {
            ratePerKg = 0.85;      // Kargo darat / mobil pengantaran
            handlingFee = 120;     // Ground logistics
            leadTime = 'Same-Day / 1 - 2 Hari';
            note = 'Pengantaran langsung armada mobil kargo kopi';
        }

        const totalCost = (weight * ratePerKg) + handlingFee;

        ratePerKgText.textContent = `$${ratePerKg.toFixed(2)} / kg`;
        handlingFeeText.textContent = `$${handlingFee.toFixed(2)}`;
        leadTimeText.textContent = leadTime;
        costNoteText.textContent = note;
        totalCostEstimate.textContent = `$${totalCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    /**
     * Inisialisasi Peta Leaflet
     */
    function initRouteMap() {
        if (!document.getElementById('routeMap')) return;

        routeMap = L.map('routeMap', {
            center: [15, 100],
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

    /**
     * Perbarui Garis Rute & Marker pada Peta
     */
    function updateRoutePreview() {
        if (!routeMap) return;

        const originInput = document.getElementById('origin-select');
        const destSelect = document.getElementById('dest-select');

        let originLat = -23.9610;
        let originLng = -46.3020;
        let originName = 'Port of Santos (Santos, Brasil)';

        if (originInput) {
            if (originInput.tagName === 'SELECT') {
                const opt = originInput.options[originInput.selectedIndex];
                if (opt && opt.value) {
                    originLat = parseFloat(opt.getAttribute('data-lat'));
                    originLng = parseFloat(opt.getAttribute('data-lng'));
                    originName = opt.text;
                }
            } else {
                originLat = parseFloat(originInput.getAttribute('data-lat')) || -23.9610;
                originLng = parseFloat(originInput.getAttribute('data-lng')) || -46.3020;
                const disp = document.getElementById('origin-display-text');
                if (disp) originName = disp.textContent;
            }
        }

        let destLat = null;
        let destLng = null;
        let destName = '';

        if (destSelect && destSelect.selectedIndex >= 0) {
            const destOpt = destSelect.options[destSelect.selectedIndex];
            if (destOpt && destOpt.value) {
                destLat = parseFloat(destOpt.getAttribute('data-lat'));
                destLng = parseFloat(destOpt.getAttribute('data-lng'));
                destName = destOpt.text;
            }
        }

        if (originMarker) routeMap.removeLayer(originMarker);
        if (destMarker) routeMap.removeLayer(destMarker);
        if (routePolyline) routeMap.removeLayer(routePolyline);
        if (planeMarker) routeMap.removeLayer(planeMarker);
        if (lastMilePolyline) routeMap.removeLayer(lastMilePolyline);

        const latLngs = [];
        const isAir = (currentMode === 'AIR');
        const originColor = isAir ? '#A855F7' : '#10B981';
        const destColor = isAir ? '#EC4899' : '#3B82F6';
        const routeColor = isAir ? '#C084FC' : '#38BDF8';

        if (originLat && originLng) {
            originMarker = L.circleMarker([originLat, originLng], {
                radius: 7,
                fillColor: originColor,
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Titik Keberangkatan (${isAir ? 'Bandara GRU' : 'Pelabuhan Santos'}):</b><br>${originName}`).addTo(routeMap);
            latLngs.push([originLat, originLng]);
        }

        if (destLat && destLng) {
            destMarker = L.circleMarker([destLat, destLng], {
                radius: 7,
                fillColor: destColor,
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Simpul Transit (${isAir ? 'Bandara Tujuan' : 'Pelabuhan Tujuan'}):</b><br>${destName}`).addTo(routeMap);
            latLngs.push([destLat, destLng]);
        }

        if (latLngs.length === 2) {
            // Garis rute utama (Pelayaran Maritim / Penerbangan Kargo)
            routePolyline = L.polyline(latLngs, {
                color: routeColor,
                weight: 3.5,
                dashArray: isAir ? '4, 8' : '6, 6',
                opacity: 0.95
            }).addTo(routeMap);

            // Pasang ikon di titik tengah rute utama (Kapal atau Pesawat)
            const midLat = (originLat + destLat) / 2;
            const midLng = (originLng + destLng) / 2;
            const iconHtml = isAir 
                ? '<div style="font-size:18px; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5)); transform:rotate(45deg);">✈️</div>'
                : '<div style="font-size:18px; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5));">🚢</div>';
            
            const midIcon = L.divIcon({
                html: iconHtml,
                className: 'route-vehicle-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            planeMarker = L.marker([midLat, midLng], { icon: midIcon }).addTo(routeMap);

            // Hubungkan simpul transit tujuan ke alamat mobil kurir last-mile jika tersedia
            const deliveryLat = parseFloat(document.getElementById('delivery_lat')?.value);
            const deliveryLng = parseFloat(document.getElementById('delivery_lng')?.value);
            if (deliveryLat && deliveryLng && destLat && destLng) {
                lastMilePolyline = L.polyline([[destLat, destLng], [deliveryLat, deliveryLng]], {
                    color: '#F59E0B',
                    weight: 2.5,
                    dashArray: '3, 4',
                    opacity: 0.95
                }).addTo(routeMap);
            }

            routeMap.fitBounds(L.latLngBounds(latLngs), { padding: [35, 35] });

            const distNM = calculateDistance(originLat, originLng, destLat, destLng);
            const distKM = Math.round(distNM * 1.852);

            if (isAir) {
                const flightHours = Math.max(1, Math.round(distKM / 800));
                document.getElementById('route-distance-badge').textContent = `Jalur Udara: ~${distKM.toLocaleString()} km (~${flightHours} jam terbang)`;
            } else {
                const sailingDays = Math.max(2, Math.round(distNM / (20 * 24)));
                document.getElementById('route-distance-badge').textContent = `Jalur Laut: ~${Math.round(distNM).toLocaleString()} NM (~${sailingDays} hari pelayaran)`;
            }
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
