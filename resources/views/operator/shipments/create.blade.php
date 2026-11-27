@extends('layouts.operator')

@section('title', 'Buat Pengiriman Baru — Santos Coffee Exporters S.A.')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('operator.shipments.index') }}" class="hover:text-cyan-500">Semua Pengiriman</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <a href="{{ route('operator.shipments.incoming') }}" class="hover:text-cyan-500">Antrean Pesanan Masuk</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-slate-600 dark:text-slate-300">Input Manual (Offline)</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                <span>☕</span>
                <span>Input Pengiriman Ekspor Kopi Santos (Manual Offline)</span>
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                Pesanan Offline / Telepon / Kontrak B2B
            </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Formulir pendaftaran manual khusus pesanan offline (via telepon/email/kontrak tahunan fisik) di mana pelanggan tidak memesan via Customer Portal.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('operator.shipments.incoming') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/50 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 border border-cyan-200 dark:border-cyan-800 transition-colors flex items-center gap-1.5">
            <i data-lucide="inbox" class="w-3.5 h-3.5"></i>
            <span>Antrean Pesanan Masuk</span>
        </a>
        <a href="{{ route('operator.shipments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<form method="POST" action="{{ route('operator.shipments.store') }}">
    @csrf
    {{-- Hidden input for transport mode --}}
    <input type="hidden" name="transport_mode" id="transport_mode" value="{{ old('transport_mode', 'SEA') }}">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- LEFT: Form Fields --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- PILIHAN MODA PENGIRIMAN: KAPAL LAUT vs PESAWAT UDARA vs MOBIL KARGO --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        1. Jenis Moda Distribusi Ekspor <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Pilih moda untuk menentukan simpul logistik & jenis armada</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Opsi Kapal Laut --}}
                    <div id="mode-card-sea" onclick="setTransportMode('SEA')" class="cursor-pointer relative p-3.5 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shadow-sm">
                                    🚢
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white">Kapal Laut</h4>
                                    <span class="text-[9px] text-slate-400">Maritime Freight</span>
                                </div>
                            </div>
                            <span id="badge-sea" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border">
                                Terpilih
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-snug">
                            Simpul <strong>Pelabuhan Santos (Brazil)</strong> ke pelabuhan kontainer dunia (7-21 hari).
                        </p>
                    </div>

                    {{-- Opsi Pesawat Udara --}}
                    <div id="mode-card-air" onclick="setTransportMode('AIR')" class="cursor-pointer relative p-3.5 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shadow-sm">
                                    ✈️
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white">Pesawat Udara</h4>
                                    <span class="text-[9px] text-slate-400">Air Cargo Express</span>
                                </div>
                            </div>
                            <span id="badge-air" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border">
                                Pilih
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-snug">
                            Simpul <strong>Bandara São Paulo (GRU)</strong> untuk sampel & pesanan prioritas kilat (1-3 hari).
                        </p>
                    </div>

                    {{-- Opsi Mobil / Truk Kargo (Darat) --}}
                    <div id="mode-card-land" onclick="setTransportMode('LAND')" class="cursor-pointer relative p-3.5 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shadow-sm">
                                    🚚
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white">Mobil / Truk</h4>
                                    <span class="text-[9px] text-slate-400">Ground / Last-Mile</span>
                                </div>
                            </div>
                            <span id="badge-land" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border">
                                Pilih
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-snug">
                            Armada <strong>Mobil Van Kurir Kopi Santos</strong> pengantaran langsung ke kafe/toko/roastery tujuan.
                        </p>
                    </div>
                </div>
                @error('transport_mode')<p class="text-rose-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Informasi Pengiriman, Pelanggan & Komoditas Kopi --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                        <span>2. Informasi Pengiriman & Komoditas Kopi Santos</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-500/10 px-2.5 py-0.5 rounded-full border border-cyan-500/20">
                        {{ $suggestedNumber }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nomor Pengiriman <span class="text-rose-500">*</span></label>
                        <input type="text" name="shipment_number" value="{{ old('shipment_number', $suggestedNumber) }}" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono @error('shipment_number') border-rose-400 @enderror">
                        @error('shipment_number')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pelanggan / Roastery Tujuan <span class="text-rose-500">*</span></label>
                        <select name="customer_id" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('customer_id') border-rose-400 @enderror">
                            <option value="">— Pilih Pelanggan —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Mitra Ekspedisi <span class="text-rose-500">*</span></label>
                            <span class="text-[11px] text-cyan-600 dark:text-cyan-400 font-medium">{{ count($carriers) }} Mitra</span>
                        </div>
                        <select id="carrier_select" name="carrier_id" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('carrier_id') border-rose-400 @enderror">
                            <option value="">— Pilih Ekspedisi Resmi Global —</option>
                            @foreach($carriers as $c)
                                <option value="{{ $c->id }}" {{ old('carrier_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->code }}) — {{ $c->country }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Komoditas Kargo <span class="text-rose-500">*</span></label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Kopi Ekspor Brazil Santos (Specialty Coffee)') }}" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('cargo_type') border-rose-400 @enderror">
                        @error('cargo_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Varietas Kopi Brazil Santos</label>
                        <select name="coffee_variety" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
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
                        <select name="coffee_grade" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
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
                            <span id="route-heading">3. Rute Pengiriman Global & Alamat Tujuan Penerima</span>
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
                    <input type="hidden" name="origin_location_id" id="origin-select" value="{{ old('origin_location_id', $santosPortId ?? 39) }}" data-lat="-23.9610" data-lng="-46.3020">
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
                        <select name="destination_location_id" id="dest-select" onchange="updateMap()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('destination_location_id') border-rose-400 @enderror" required>
                            <option value="">— Pilih Simpul Transit Tujuan —</option>
                        </select>
                        @error('destination_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- 3. Provinsi / State (Cascading dari Negara) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                3. Provinsi / State Tujuan <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-cyan-500 font-semibold">Pilih Provinsi</span>
                        </div>
                        <select name="delivery_province" id="delivery_province" onchange="onProvinceSelected(this.value)" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">— Pilih Provinsi / State —</option>
                        </select>
                    </div>

                    {{-- 4. Kota Tujuan (Cascading dari Provinsi) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                4. Kota Tujuan Pengantaran <span class="text-rose-500">*</span>
                            </label>
                            <span id="city-spinner" class="hidden text-[10px] text-cyan-500 animate-pulse">Memuat kota...</span>
                        </div>
                        <select name="delivery_city" id="delivery_city" onchange="onCitySelected(this.value)" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">— Pilih Kota —</option>
                        </select>
                    </div>

                    {{-- 5. Daerah / Distrik (Cascading dari Kota) --}}
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                                5. Daerah / Distrik / Suburb <span class="text-rose-500">*</span>
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
                        6. Alamat Spesifik Penerima (Nama Kafe / Roastery / Jalan & Nomor Gedung)
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
                            <span id="geocode-coords-display" class="font-mono font-bold text-slate-800 dark:text-slate-200">-6.2297, 106.8075 (Jakarta, Senopati)</span>
                        </div>
                        <span id="geocode-status-badge" class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                            ✓ Terverifikasi Geoapify
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

            {{-- Spesifikasi & Jadwal --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-cyan-500"></i>
                    5. Spesifikasi & Jadwal
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Berat (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight', '1000') }}" step="0.01" min="0.1" placeholder="1000" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('weight')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Volume (m³) <span class="text-rose-500">*</span></label>
                        <input type="number" name="volume" value="{{ old('volume', '10') }}" step="0.01" min="0.1" placeholder="10" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('volume')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jadwal Keberangkatan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', date('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        @error('expected_departure')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Estimasi Tiba (ETA) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', date('Y-m-d\TH:i', strtotime('+14 days'))) }}" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
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
                        <input type="text" name="item_name" value="{{ old('item_name', 'Green Coffee Beans 60kg Jute Bag') }}" placeholder="cth. Green Coffee Beans..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kode HS</label>
                        <input type="text" name="hs_code" value="{{ old('hs_code', '0901.11') }}" placeholder="0901.11" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kuantitas Karung/Box</label>
                        <input type="number" name="item_qty" value="{{ old('item_qty', 540) }}" min="1" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nilai Deklarasi ($ USD)</label>
                        <input type="number" name="item_value" value="{{ old('item_value', '185000') }}" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Catatan Tambahan & Sertifikasi</label>
                <textarea name="notes" rows="3" placeholder="Informasi sertifikasi ICO, GrainPro lining, instruksi penanganan suhu..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('notes', 'Kantong GrainPro hermetis. Suhu kontainer dijaga stabil 18-22°C. Pengantaran mobil kurir langsung ke roastery.') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Pengiriman Kopi Santos</span>
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
                <div id="route-map" style="height: 340px; background: #0A1128;"></div>
                <div id="route-map-footer" class="px-5 py-3 text-xs text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800">
                    Pilih lokasi asal dan tujuan untuk melihat rute di peta.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
@include('partials.geo-province-cascade')
<script>
const portLocations = @json($portLocations);
const airportLocations = @json($airportLocations);
const initialMode = @json(old('transport_mode', 'SEA'));
const initialOriginId = @json(old('origin_location_id'));
const initialDestId = @json(old('destination_location_id'));

// URL dinamis API Geoapify via Laravel route helpers
const GEO_CITIES_URL = @json(route('geo.cities'));
const GEO_DISTRICTS_URL = @json(route('geo.districts'));
const GEO_HUBS_URL      = @json(route('geo.country_hubs'));
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
let routeLine = null;
let planeMarker = null;
let deliveryMarker = null;
let lastMileLine = null;
let geocodeTimeout = null;

document.addEventListener('DOMContentLoaded', function () {
    routeMap = L.map('route-map', { zoomControl: true }).setView([-15, -45], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 18 }).addTo(routeMap);

    setTransportMode(initialMode, false);

    const initialCountry = document.getElementById('delivery_country').value;
    if (initialCountry) {
        onCountrySelected(initialCountry);
    }
});

function setTransportMode(mode, resetSelections = true) {
    currentMode = mode;
    document.getElementById('transport_mode').value = mode;

    const cardSea = document.getElementById('mode-card-sea');
    const cardAir = document.getElementById('mode-card-air');
    const cardLand = document.getElementById('mode-card-land');
    const badgeSea = document.getElementById('badge-sea');
    const badgeAir = document.getElementById('badge-air');
    const badgeLand = document.getElementById('badge-land');
    const heading = document.getElementById('route-heading');
    const destLabel = document.getElementById('dest-label');
    const originIcon = document.getElementById('origin-icon-badge');
    const originText = document.getElementById('origin-display-text');
    const originInput = document.getElementById('origin-select');

    // Reset all cards
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
        if (cardAir) cardAir.className = 'cursor-pointer relative p-3.5 rounded-xl border-2 border-purple-500 bg-purple-50/40 dark:bg-purple-950/20 shadow-sm flex flex-col justify-between';
        if (badgeAir) {
            badgeAir.className = 'text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-purple-600 text-white border-purple-600';
            badgeAir.textContent = 'Terpilih ✓';
        }

        if (heading) heading.innerHTML = '3. Rute Penerbangan Kargo Udara (Bandara São Paulo GRU & Bandara Transit Tujuan)';
        if (destLabel) destLabel.innerHTML = '2. Bandara Transit Tujuan <span class="text-rose-500">*</span>';
        if (originIcon) originIcon.textContent = '✈️';
        if (originText) originText.textContent = 'Sao Paulo/Guarulhos International Cargo (GRU) — Brasil';
        if (originInput) {
            originInput.value = '{{ $brazilAirportId ?? 93 }}';
            originInput.setAttribute('data-lat', '-23.4356');
            originInput.setAttribute('data-lng', '-46.4731');
        }
    } else if (mode === 'LAND') {
        if (cardLand) cardLand.className = 'cursor-pointer relative p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50/40 dark:bg-amber-950/20 shadow-sm flex flex-col justify-between';
        if (badgeLand) {
            badgeLand.className = 'text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-600 text-white border-amber-600';
            badgeLand.textContent = 'Terpilih ✓';
        }

        if (heading) heading.innerHTML = '3. Hub Logistik Darat & Pengantaran Mobil Kopi Santos';
        if (destLabel) destLabel.innerHTML = '2. Hub Logistik Transit Tujuan <span class="text-rose-500">*</span>';
        if (originIcon) originIcon.textContent = '🚚';
        if (originText) originText.textContent = 'Hub Distribusi Kopi Santos (Santos, Brasil)';
        if (originInput) {
            originInput.value = '{{ $santosPortId ?? 39 }}';
            originInput.setAttribute('data-lat', '-23.9610');
            originInput.setAttribute('data-lng', '-46.3020');
        }
    } else {
        if (cardSea) cardSea.className = 'cursor-pointer relative p-3.5 rounded-xl border-2 border-blue-500 bg-blue-50/40 dark:bg-blue-950/20 shadow-sm flex flex-col justify-between';
        if (badgeSea) {
            badgeSea.className = 'text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-600 text-white border-blue-600';
            badgeSea.textContent = 'Terpilih ✓';
        }

        if (heading) heading.innerHTML = '3. Rute Pelayaran Maritim (Pelabuhan Santos ke Dunia)';
        if (destLabel) destLabel.innerHTML = '2. Pelabuhan Laut Transit Tujuan <span class="text-rose-500">*</span>';
        if (originIcon) originIcon.textContent = '🚢';
        if (originText) originText.textContent = 'Port of Santos (Santos, Brasil)';
        if (originInput) {
            originInput.value = '{{ $santosPortId ?? 39 }}';
            originInput.setAttribute('data-lat', '-23.9610');
            originInput.setAttribute('data-lng', '-46.3020');
        }
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }

    const countryEl = document.getElementById('delivery_country');
    if (countryEl && countryEl.value) {
        updateDestinationNodesByCountry(countryEl.value);
    } else {
        updateMap();
    }
}

function matchCountryHub(locCountry, selectedCountry) {
    const a = (locCountry || '').toLowerCase().trim();
    const b = (selectedCountry || '').toLowerCase().trim();
    if (!a || !b) return false;
    if (a === b || a.includes(b) || b.includes(a)) return true;

    const aliases = [
        ['turki', 'turkey', 'türkiye'],
        ['inggris', 'united kingdom', 'uk', 'great britain', 'england'],
        ['jerman', 'germany', 'deutschland'],
        ['belanda', 'netherlands', 'holland'],
        ['prancis', 'france'],
        ['italia', 'italy'],
        ['spanyol', 'spain'],
        ['tiongkok', 'china', 'prc'],
        ['jepang', 'japan'],
        ['korea selatan', 'south korea', 'korea'],
        ['amerika serikat', 'united states', 'usa', 'us'],
        ['uni emirat arab', 'united arab emirates', 'uae'],
        ['arab saudi', 'saudi arabia'],
        ['mesir', 'egypt'],
        ['maroko', 'morocco'],
        ['afrika selatan', 'south africa'],
        ['brasil', 'brazil'],
        ['meksiko', 'mexico'],
        ['kanada', 'canada'],
        ['kolombia', 'colombia'],
        ['selandia baru', 'new zealand'],
        ['polandia', 'poland'],
        ['swedia', 'sweden'],
        ['norwegia', 'norway'],
        ['finlandia', 'finland'],
        ['yunani', 'greece'],
        ['swiss', 'switzerland'],
        ['rusia', 'russia'],
        ['filipina', 'philippines'],
        ['singapura', 'singapore'],
    ];
    return aliases.some(group => group.includes(a) && group.some(alias => b === alias || b.includes(alias) || alias.includes(b)))
        || aliases.some(group => group.includes(b) && group.some(alias => a === alias || a.includes(alias) || alias.includes(a)));
}

/**
 * Filter Simpul Transit Tujuan Berdasarkan Negara
 */
async function updateDestinationNodesByCountry(selectedCountry) {
    if (!selectedCountry) return;
    const destSelect = document.getElementById('dest-select');
    if (!destSelect) return;

    const isAir = (currentMode === 'AIR');
    const pool = isAir ? airportLocations : (currentMode === 'LAND' ? portLocations.concat(airportLocations) : portLocations);

    let matching = pool.filter(loc => matchCountryHub(loc.country, selectedCountry));

    if (matching.length === 0 && typeof GEO_HUBS_URL !== 'undefined') {
        destSelect.innerHTML = `<option value="">Memuat ${isAir ? 'bandara' : 'pelabuhan'} ${selectedCountry}...</option>`;
        try {
            const res = await fetch(`${GEO_HUBS_URL}?country=${encodeURIComponent(selectedCountry)}&mode=${encodeURIComponent(currentMode)}`);
            if (res.ok) {
                const json = await res.json();
                if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                    matching = json.data;
                    matching.forEach(h => {
                        const targetArr = (h.location_type === 'AIRPORT') ? airportLocations : portLocations;
                        if (!targetArr.some(x => String(x.id) === String(h.id))) {
                            targetArr.push(h);
                        }
                    });
                }
            }
        } catch (e) {
            console.warn('Gagal memuat hub negara:', e);
        }
    }

    let isGlobalFallback = false;
    if (matching.length === 0) {
        const allHubs = portLocations.concat(airportLocations);
        matching = allHubs.filter(loc => matchCountryHub(loc.country, selectedCountry));
    }
    if (matching.length === 0) {
        matching = pool;
        isGlobalFallback = true;
    }

    let html = '';
    matching.forEach((loc, idx) => {
        const isSel = (initialDestId && String(loc.id) === String(initialDestId)) || idx === 0;
        html += `<option value="${loc.id}" data-lat="${loc.latitude}" data-lng="${loc.longitude}" data-country="${loc.country}" data-city="${loc.city}" ${isSel ? 'selected' : ''}>${loc.name} (${loc.city ? loc.city + ', ' : ''}${loc.country})</option>`;
    });
    if (isGlobalFallback && html) {
        html = `<optgroup label="Hub Transit Internasional (${selectedCountry} belum memiliki hub langsung)">${html}</optgroup>`;
    }

    destSelect.innerHTML = html;
    if (matching.length > 0) {
        const currentVal = destSelect.value;
        if (!currentVal || !matching.some(m => String(m.id) === String(currentVal))) {
            destSelect.value = matching[0].id;
        }
    }

    const cityEl = document.getElementById('delivery_city');
    if (cityEl && cityEl.value) {
        alignPortWithCity(cityEl.value);
    } else {
        updateMap();
    }
}

/**
 * Smart Port Alignment
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
    updateMap();
}

async function onCountrySelected(country) {
    if (!country) return;
    if (window.extraDistrictsMap) {
        Object.assign(fallbackDistrictsMap, window.extraDistrictsMap);
    }
    await window.populateProvincesAndCascade(country);
}

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
                if (lastMileLine) routeMap.removeLayer(lastMileLine);
                lastMileLine = L.polyline([[dLat, dLng], [lat, lng]], {
                    color: '#F59E0B',
                    weight: 2.5,
                    dashArray: '3, 4',
                    opacity: 0.95
                }).addTo(routeMap);
            }
        }
    }
}

function updateMap() {
    if (!routeMap) return;

    const originInput = document.getElementById('origin-select');
    const destSel = document.getElementById('dest-select');

    let oLat = -23.9610;
    let oLng = -46.3020;
    let originText = 'Port of Santos (Santos, Brasil)';

    if (originInput) {
        if (originInput.tagName === 'SELECT') {
            const originOpt = originInput.options[originInput.selectedIndex];
            if (originOpt && originOpt.value) {
                oLat = parseFloat(originOpt.dataset.lat);
                oLng = parseFloat(originOpt.dataset.lng);
                originText = originOpt.text;
            }
        } else {
            oLat = parseFloat(originInput.getAttribute('data-lat')) || -23.9610;
            oLng = parseFloat(originInput.getAttribute('data-lng')) || -46.3020;
            const disp = document.getElementById('origin-display-text');
            if (disp) originText = disp.textContent;
        }
    }

    let dLat = null;
    let dLng = null;
    let destText = '';

    if (destSel && destSel.selectedIndex >= 0) {
        const destOpt = destSel.options[destSel.selectedIndex];
        if (destOpt && destOpt.value) {
            dLat = parseFloat(destOpt.dataset.lat);
            dLng = parseFloat(destOpt.dataset.lng);
            destText = destOpt.text;
        }
    }

    if (originMarker) routeMap.removeLayer(originMarker);
    if (destMarker) routeMap.removeLayer(destMarker);
    if (routeLine) routeMap.removeLayer(routeLine);
    if (planeMarker) routeMap.removeLayer(planeMarker);
    if (lastMileLine) routeMap.removeLayer(lastMileLine);

    const isAir = (currentMode === 'AIR');
    const isLand = (currentMode === 'LAND');
    const originColor = isAir ? '#A855F7' : (isLand ? '#f59e0b' : '#10b981');
    const destColor = isAir ? '#EC4899' : (isLand ? '#ea580c' : '#f43f5e');
    const lineColor = isAir ? '#C084FC' : (isLand ? '#f59e0b' : '#06b6d4');

    if (oLat && dLat) {
        originMarker = L.circleMarker([oLat, oLng], { radius: 8, color: originColor, fillColor: originColor, fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>Titik Keberangkatan (${isAir ? 'Bandara GRU' : 'Pelabuhan Santos'}):</b><br>${originText}`);
        destMarker = L.circleMarker([dLat, dLng], { radius: 8, color: destColor, fillColor: destColor, fillOpacity: 1 }).addTo(routeMap).bindPopup(`<b>Simpul Transit Tujuan:</b><br>${destText}`);
        routeLine = L.polyline([[oLat, oLng], [dLat, dLng]], { color: lineColor, weight: 3, dashArray: isAir ? '4,6' : (isLand ? '2,4' : '8,6') }).addTo(routeMap);

        const midLat = (oLat + dLat) / 2;
        const midLng = (oLng + dLng) / 2;
        const iconHtml = isAir 
            ? '<div style="font-size:16px;">✈️</div>'
            : (isLand ? '<div style="font-size:16px;">🚚</div>' : '<div style="font-size:16px;">🚢</div>');
        
        planeMarker = L.marker([midLat, midLng], {
            icon: L.divIcon({ html: iconHtml, className: 'route-vehicle-icon', iconSize: [20, 20], iconAnchor: [10, 10] })
        }).addTo(routeMap);

        // Hubungkan simpul transit ke alamat mobil kurir
        const deliveryLat = parseFloat(document.getElementById('delivery_lat')?.value);
        const deliveryLng = parseFloat(document.getElementById('delivery_lng')?.value);
        if (deliveryLat && deliveryLng && dLat && dLng) {
            lastMileLine = L.polyline([[dLat, dLng], [deliveryLat, deliveryLng]], {
                color: '#F59E0B',
                weight: 2.5,
                dashArray: '3, 4',
                opacity: 0.95
            }).addTo(routeMap);
        }

        routeMap.fitBounds([[oLat, oLng], [dLat, dLng]], { padding: [40, 40] });

        const dLatRad = (dLat - oLat) * Math.PI / 180;
        const dLngRad = (dLng - oLng) * Math.PI / 180;
        const a = Math.sin(dLatRad/2)**2 + Math.cos(oLat*Math.PI/180) * Math.cos(dLat*Math.PI/180) * Math.sin(dLngRad/2)**2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distKM = Math.round(6371 * c);

        document.getElementById('route-distance-badge').textContent = isAir ? `Rute Udara: ~${distKM} km` : (isLand ? `Rute Darat: ~${distKM} km` : `Rute Laut: ~${Math.round(distKM * 0.5399)} NM`);
        document.getElementById('route-map-footer').textContent = `Terpilih: ${originText} ➔ ${destText}`;
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
