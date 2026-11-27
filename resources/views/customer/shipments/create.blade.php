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
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shadow-2xs">
                                        🚢
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                            <span>Multimoda Samudra</span>
                                            <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 font-bold">Rekomendasi</span>
                                        </h4>
                                        <span class="text-[10px] text-slate-400">Santos ➔ Hub Masuk ➔ Kurir Kafe</span>
                                    </div>
                                </div>
                                <span id="badge-sea" class="text-[9px] font-bold px-2 py-0.5 rounded-full border">
                                    Terpilih
                                </span>
                            </div>

                            {{-- Highlight Pills --}}
                            <div class="grid grid-cols-2 gap-2 my-2.5">
                                <div class="px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800 text-[11px]">
                                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Estimasi Durasi</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-200">⏱️ 18 - 24 Hari</span>
                                </div>
                                <div class="px-2.5 py-1.5 rounded-lg bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 text-[11px]">
                                    <span class="text-emerald-600 dark:text-emerald-400 block text-[9px] uppercase font-bold">Tarif Dasar</span>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-300">💰 $0.35 / kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-400 flex items-center justify-between">
                            <span>Kapasitas Kontainer Penuh (FCL/LCL)</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Paling Ekonomis ✓</span>
                        </div>
                    </div>

                    {{-- Opsi 2: Pesawat Kargo Kilat (Full Air Express) --}}
                    <div id="mode-card-air" onclick="setTransportMode('AIR')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between hover:shadow-md">
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shadow-2xs">
                                        ✈️
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                            <span>Pesawat Kargo Kilat</span>
                                            <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300 font-bold">Air Express</span>
                                        </h4>
                                        <span class="text-[10px] text-slate-400">Bandara GRU Direct Air ➔ Kurir Kafe</span>
                                    </div>
                                </div>
                                <span id="badge-air" class="text-[9px] font-bold px-2 py-0.5 rounded-full border">
                                    Pilih
                                </span>
                            </div>

                            {{-- Highlight Pills --}}
                            <div class="grid grid-cols-2 gap-2 my-2.5">
                                <div class="px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800 text-[11px]">
                                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Estimasi Durasi</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-200">⚡ 3 - 5 Hari</span>
                                </div>
                                <div class="px-2.5 py-1.5 rounded-lg bg-purple-50/60 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/50 text-[11px]">
                                    <span class="text-purple-600 dark:text-purple-400 block text-[9px] uppercase font-bold">Tarif Kilat</span>
                                    <span class="font-bold text-purple-700 dark:text-purple-300">💰 $2.80 / kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-400 flex items-center justify-between">
                            <span>Sampel Roastery & Order Cepat</span>
                            <span class="text-purple-600 dark:text-purple-400 font-semibold">Prioritas Kilat ✓</span>
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

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jenis Kargo / Komoditas <span class="text-rose-500">*</span></label>
                        <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Kopi Ekspor Brazil Santos (Specialty Coffee)') }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('cargo_type') border-rose-400 @enderror">
                        @error('cargo_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Varietas Kopi Brazil Santos</label>
                        <select name="coffee_variety" onchange="calculateEstimates()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="Kopi Brazil Santos Bourbon Specialty" {{ old('coffee_variety') === 'Kopi Brazil Santos Bourbon Specialty' ? 'selected' : '' }}>Kopi Brazil Santos Bourbon Specialty</option>
                            <option value="Brazil Santos Strictly Soft Fine Cup (SSFC)" {{ old('coffee_variety') === 'Brazil Santos Strictly Soft Fine Cup (SSFC)' ? 'selected' : '' }}>Brazil Santos Strictly Soft Fine Cup (SSFC)</option>
                            <option value="Brazil Santos Yellow Catuai Natural Micro-lot" {{ old('coffee_variety') === 'Brazil Santos Yellow Catuai Natural Micro-lot' ? 'selected' : '' }}>Brazil Santos Yellow Catuai Natural Micro-lot</option>
                            <option value="Brazil Santos Cerrado Mineiro Single-Origin" {{ old('coffee_variety') === 'Brazil Santos Cerrado Mineiro Single-Origin' ? 'selected' : '' }}>Brazil Santos Cerrado Mineiro Single-Origin</option>
                            <option value="Brazil Santos Decaf Swiss Water Process" {{ old('coffee_variety') === 'Brazil Santos Decaf Swiss Water Process' ? 'selected' : '' }}>Brazil Santos Decaf Swiss Water Process</option>
                            <option value="Brazil Santos Espresso Roast Master Blend" {{ old('coffee_variety') === 'Brazil Santos Espresso Roast Master Blend' ? 'selected' : '' }}>Brazil Santos Espresso Roast Master Blend</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Grade / Kualitas Kopi Ekspor</label>
                        <select name="coffee_grade" onchange="calculateEstimates()" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="Specialty Grade 1 (SCA Score 84+)" {{ old('coffee_grade') === 'Specialty Grade 1 (SCA Score 84+)' ? 'selected' : '' }}>Specialty Grade 1 (SCA Score 84+) — Premium Green Beans</option>
                            <option value="Strictly Soft Fine Cup NY 2/3" {{ old('coffee_grade') === 'Strictly Soft Fine Cup NY 2/3' ? 'selected' : '' }}>Strictly Soft Fine Cup NY 2/3 — Export Standard</option>
                            <option value="Exclusive Micro-Lot Nano Batch" {{ old('coffee_grade') === 'Exclusive Micro-Lot Nano Batch' ? 'selected' : '' }}>Exclusive Micro-Lot Nano Batch — Roasted / Green</option>
                            <option value="Commercial Export Grade A" {{ old('coffee_grade') === 'Commercial Export Grade A' ? 'selected' : '' }}>Commercial Export Grade A</option>
                        </select>
                    </div>

                    {{-- JUMLAH PEMESANAN KOPI (BERAT KG) LANGSUNG DI BAGIAN 2 --}}
                    <div class="col-span-1 md:col-span-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Jumlah Pesanan Biji Kopi (kg) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium">Standar Ekspor Santos (@60 kg / Karung Goni)</span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="number" step="10" min="10" name="weight" id="cargo-weight-input" oninput="calculateEstimates()" value="{{ old('weight', 1000) }}" placeholder="1000" required class="w-full px-3 py-2 text-xs font-bold border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 @error('weight') border-rose-400 @enderror">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">kg</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" onclick="setQuickWeight(500)" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-600 dark:bg-slate-800 dark:hover:bg-cyan-950/60 dark:text-slate-300 transition-colors">500 kg</button>
                                <button type="button" onclick="setQuickWeight(1000)" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-600 dark:bg-slate-800 dark:hover:bg-cyan-950/60 dark:text-slate-300 transition-colors">1 Ton</button>
                                <button type="button" onclick="setQuickWeight(3000)" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-600 dark:bg-slate-800 dark:hover:bg-cyan-950/60 dark:text-slate-300 transition-colors">3 Ton</button>
                                <button type="button" onclick="setQuickWeight(5000)" class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-600 dark:bg-slate-800 dark:hover:bg-cyan-950/60 dark:text-slate-300 transition-colors">5 Ton</button>
                            </div>
                        </div>
                        @error('weight')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror

                        {{-- LIVE STATS CHIPS OTOMATIS --}}
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 text-center">
                                <div class="text-[10px] text-slate-400">📦 Total Kemasan:</div>
                                <div id="stat-bags-display" class="text-xs font-bold text-slate-800 dark:text-slate-100 mt-0.5">17 Karung Goni</div>
                            </div>
                            <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 text-center">
                                <div class="text-[10px] text-slate-400">📐 Est. Volume:</div>
                                <div id="stat-volume-display" class="text-xs font-bold text-slate-800 dark:text-slate-100 mt-0.5">1.60 m³</div>
                            </div>
                            <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 text-center">
                                <div class="text-[10px] text-slate-400">💵 Est. Nilai Kargo:</div>
                                <div id="stat-value-display" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">$15,000 USD</div>
                            </div>
                        </div>
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
        </div>

        {{-- RIGHT: Interactive Map Route Preview, Schedule, Manifest & Cost Summary --}}
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

            {{-- 4. JADWAL KEBERANGKATAN & ESTIMASI TIBA (OTOMATIS BERDASARKAN PROSES GUDANG SANTOS & DURASI RUTE) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <i data-lucide="calendar-clock" class="w-4 h-4 text-cyan-500"></i>
                            <span>4. Jadwal Keberangkatan & Estimasi Tiba (Otomatis)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Dihitung otomatis: waktu sortir/packing di gudang Santos + durasi rute pelayaran ke negara tujuan.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800 shrink-0">
                        Otomatis Sistem ✓
                    </span>
                </div>

                {{-- TIMELINE 3-LANGKAH JADWAL LOGISTIK REALISTIS --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    {{-- STEP 1: HARI INI (PEMESANAN) --}}
                    <div class="p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500">Langkah 1</span>
                                <span class="text-[9px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded">Order Masuk</span>
                            </div>
                            <div id="timeline-order-date" class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ date('d M Y') }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                                Pemesanan kargo tercatat ke antrian ekspedisi ekspor Santos Brazil.
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: RENCANA BERANGKAT (ETD SANTOS) --}}
                    <div class="p-3 rounded-xl border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Langkah 2 (ETD)</span>
                                <span id="timeline-packing-badge" class="text-[9px] font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/50 px-1.5 py-0.5 rounded">Packing 2 Hari</span>
                            </div>
                            <div id="timeline-departure-display" class="text-xs font-bold text-amber-900 dark:text-amber-200">{{ date('d M Y', strtotime('+2 days')) }}</div>
                            <div id="timeline-departure-note" class="text-[10px] text-amber-700/90 dark:text-amber-400/90 mt-1 leading-snug">
                                Sortir mutu SCA, karung goni, & stuffing kontainer Santos.
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: ESTIMASI TIBA (ETA TUJUAN) --}}
                    <div class="p-3 rounded-xl border border-cyan-200 dark:border-cyan-900/60 bg-cyan-50/50 dark:bg-cyan-950/20 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400">Langkah 3 (ETA)</span>
                                <span id="timeline-transit-badge" class="text-[9px] font-semibold text-cyan-700 dark:text-cyan-300 bg-cyan-100 dark:bg-cyan-900/50 px-1.5 py-0.5 rounded">~21 Hari</span>
                            </div>
                            <div id="timeline-arrival-display" class="text-xs font-bold text-cyan-900 dark:text-cyan-200">{{ date('d M Y', strtotime('+23 days')) }}</div>
                            <div id="timeline-arrival-note" class="text-[10px] text-cyan-700/90 dark:text-cyan-400/90 mt-1 leading-snug">
                                Estimasi tiba di pelabuhan tujuan & pengantaran ke alamat.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- HIDDEN INPUTS JADWAL & VOLUME UNTUK VALIDASI CONTROLLER --}}
                <input type="hidden" name="expected_departure" id="expected_departure" value="{{ old('expected_departure', date('Y-m-d', strtotime('+2 days'))) }}">
                <input type="hidden" name="expected_arrival" id="expected_arrival" value="{{ old('expected_arrival', date('Y-m-d', strtotime('+23 days'))) }}">
                <input type="hidden" name="volume" id="volume-input" value="{{ old('volume', 1.6) }}">
            </div>

            {{-- 5. MANIFEST EKSPOR BEA CUKAI & CATATAN KHUSUS (TERINTEGRASI DENGAN PEMESANAN KOPI) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <i data-lucide="file-check-2" class="w-4 h-4 text-cyan-500"></i>
                            <span>5. Manifest Dokumen Ekspor & Catatan Penanganan</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Rincian deklarasi bea cukai diterbitkan otomatis mengikuti pilihan biji kopi & berat pesanan.</p>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800 shrink-0">
                        HS Code 0901.11.00 ✓
                    </span>
                </div>

                {{-- KARTU RINGKASAN MANIFEST OTOMATIS --}}
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Komoditas Manifest:</span>
                            <span id="manifest-item-name-text" class="font-bold text-slate-800 dark:text-slate-200">Kopi Brazil Santos Bourbon Specialty</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Klasifikasi Bea Cukai:</span>
                            <span class="font-mono font-bold text-cyan-600 dark:text-cyan-400">0901.11.00 (Green Coffee)</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Satuan Kemasan:</span>
                            <span id="manifest-qty-text" class="font-bold text-slate-800 dark:text-slate-200">17 Karung Goni (@60 kg)</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Deklarasi Nilai Kargo:</span>
                            <span id="manifest-val-text" class="font-bold text-emerald-600 dark:text-emerald-400">$15,000.00 USD</span>
                        </div>
                    </div>
                </div>

                {{-- SATU-SATUNYA INPUT USER: INSTRUKSI KHUSUS / CATATAN --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                        Instruksi Khusus / Catatan Penanganan Kargo (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="cth. Suhu kontainer dijaga 18-20°C, kelembaban udara < 12%..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>

                {{-- HIDDEN INPUTS MANIFEST UNTUK CONTROLLER & DATABASE --}}
                <input type="hidden" name="item_name" id="item-name-input" value="{{ old('item_name', 'Kopi Brazil Santos (Specialty Coffee)') }}">
                <input type="hidden" name="hs_code" id="hs-code-input" value="{{ old('hs_code', '0901.11.00') }}">
                <input type="hidden" name="item_qty" id="item-qty-input" value="{{ old('item_qty', 17) }}">
                <input type="hidden" name="item_value" id="item-value-input" value="{{ old('item_value', 15000) }}">
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
@include('partials.geo-province-cascade')
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
     * Cascading Dropdowns: Negara -> Provinsi -> Kota -> Daerah
     */
    async function onCountrySelected(country) {
        if (!country) return;
        if (window.extraDistrictsMap) {
            Object.assign(fallbackDistrictsMap, window.extraDistrictsMap);
        }
        await window.populateProvincesAndCascade(country);
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
     * Set Berat Kargo Cepat (Pills 500kg, 1 Ton, 3 Ton, 5 Ton)
     */
    function setQuickWeight(kg) {
        const input = document.getElementById('cargo-weight-input');
        if (input) {
            input.value = kg;
            calculateEstimates();
        }
    }

    function formatDateIndo(dateObj) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const d = String(dateObj.getDate()).padStart(2, '0');
        const m = months[dateObj.getMonth()];
        const y = dateObj.getFullYear();
        return `${d} ${m} ${y}`;
    }

    /**
     * Hitung Estimasi Biaya, Jadwal Otomatis & Manifest Kargo Santos
     */
    function calculateEstimates() {
        const weightInput = document.getElementById('cargo-weight-input');
        const weight = Math.max(1, parseFloat(weightInput ? weightInput.value : 1000) || 1000);

        // 1. Hitung Kemasan Karung Goni, Volume Kontainer, & Nilai Deklarasi
        const bags = Math.max(1, Math.ceil(weight / 60));
        const volume = Math.max(0.1, +(weight * 0.0016).toFixed(2));

        const varietySelect = document.querySelector('select[name="coffee_variety"]');
        const gradeSelect = document.querySelector('select[name="coffee_grade"]');
        const varietyName = varietySelect ? varietySelect.value : 'Kopi Brazil Santos Bourbon Specialty';
        const gradeName = gradeSelect ? gradeSelect.value : 'Specialty Grade 1 (SCA Score 84+)';

        let pricePerKg = 15.00;
        if (gradeName.includes('Micro-Lot')) pricePerKg = 22.50;
        else if (gradeName.includes('Strictly Soft')) pricePerKg = 12.00;
        else if (gradeName.includes('Commercial')) pricePerKg = 8.50;

        const totalDeclaredVal = +(weight * pricePerKg).toFixed(2);

        // Update live stats & manifest UI
        const statBags = document.getElementById('stat-bags-display');
        const statVol = document.getElementById('stat-volume-display');
        const statVal = document.getElementById('stat-value-display');
        if (statBags) statBags.textContent = `${bags} Karung Goni`;
        if (statVol) statVol.textContent = `${volume.toFixed(2)} m³`;
        if (statVal) statVal.textContent = `$${totalDeclaredVal.toLocaleString('en-US')} USD`;

        const manifestItemText = document.getElementById('manifest-item-name-text');
        const manifestQtyText = document.getElementById('manifest-qty-text');
        const manifestValText = document.getElementById('manifest-val-text');
        if (manifestItemText) manifestItemText.textContent = `${varietyName}`;
        if (manifestQtyText) manifestQtyText.textContent = `${bags} Karung Goni (@60 kg)`;
        if (manifestValText) manifestValText.textContent = `$${totalDeclaredVal.toLocaleString('en-US', { minimumFractionDigits: 2 })} USD`;

        // Update hidden inputs untuk validasi controller
        const volInput = document.getElementById('volume-input');
        const qtyInput = document.getElementById('item-qty-input');
        const valInput = document.getElementById('item-value-input');
        const nameInput = document.getElementById('item-name-input');
        if (volInput) volInput.value = volume;
        if (qtyInput) qtyInput.value = bags;
        if (valInput) valInput.value = totalDeclaredVal;
        if (nameInput) nameInput.value = `${varietyName} (${gradeName})`;

        // 2. Hitung Jadwal Otomatis:
        // Waktu packing di Santos: <= 2000kg: 2 hari; <= 5000kg: 3 hari; > 5000kg: 4 hari
        const packingDays = (weight > 5000) ? 4 : ((weight > 2000) ? 3 : 2);
        const transitDays = (currentMode === 'AIR') ? 3 : 21;

        const today = new Date();
        const departureDate = new Date();
        departureDate.setDate(today.getDate() + packingDays);

        const arrivalDate = new Date(departureDate);
        arrivalDate.setDate(departureDate.getDate() + transitDays);

        const depIso = departureDate.toISOString().split('T')[0];
        const arrIso = arrivalDate.toISOString().split('T')[0];

        const depHidden = document.getElementById('expected_departure');
        const arrHidden = document.getElementById('expected_arrival');
        if (depHidden) depHidden.value = depIso;
        if (arrHidden) arrHidden.value = arrIso;

        const depDisplay = document.getElementById('timeline-departure-display');
        const arrDisplay = document.getElementById('timeline-arrival-display');
        const packingBadge = document.getElementById('timeline-packing-badge');
        const transitBadge = document.getElementById('timeline-transit-badge');
        const depNote = document.getElementById('timeline-departure-note');

        if (depDisplay) depDisplay.textContent = formatDateIndo(departureDate);
        if (arrDisplay) arrDisplay.textContent = formatDateIndo(arrivalDate);
        if (packingBadge) packingBadge.textContent = `Packing ${packingDays} Hari`;
        if (transitBadge) transitBadge.textContent = (currentMode === 'AIR') ? 'Penerbangan Kilat ~3 Hari' : 'Pelayaran Samudra ~21 Hari';
        if (depNote) {
            depNote.textContent = (weight > 5000) 
                ? 'Sortir muatan besar (>5 Ton), bagging karung goni, & stuffing kontainer pelabuhan.' 
                : 'Sortir mutu SCA, pengemasan karung goni, & stuffing kontainer di Pelabuhan Santos.';
        }

        // 3. Hitung Biaya Tarif Kargo di Panel Kanan
        const ratePerKgText = document.getElementById('rate-per-kg-text');
        const handlingFeeText = document.getElementById('handling-fee-text');
        const leadTimeText = document.getElementById('lead-time-text');
        const costNoteText = document.getElementById('cost-note-text');
        const totalCostEstimate = document.getElementById('total-cost-estimate');

        let ratePerKg = 0.35;
        let handlingFee = 250;
        let leadTime = `${packingDays + transitDays} Hari Total`;
        let note = 'Pengiriman kontainer laut ekonomis (Santos Port)';

        if (currentMode === 'AIR') {
            ratePerKg = 2.80;
            handlingFee = 450;
            leadTime = `${packingDays + transitDays} Hari (Super Kilat)`;
            note = 'Pengiriman udara prioritas kilat (GRU Airport)';
        } else if (currentMode === 'LAND') {
            ratePerKg = 0.85;
            handlingFee = 120;
            leadTime = 'Same-Day / 1 - 2 Hari';
            note = 'Pengantaran langsung armada mobil kargo kopi';
        }

        const totalCost = (weight * ratePerKg) + handlingFee;

        if (ratePerKgText) ratePerKgText.textContent = `$${ratePerKg.toFixed(2)} / kg`;
        if (handlingFeeText) handlingFeeText.textContent = `$${handlingFee.toFixed(2)}`;
        if (leadTimeText) leadTimeText.textContent = leadTime;
        if (costNoteText) costNoteText.textContent = note;
        if (totalCostEstimate) totalCostEstimate.textContent = `$${totalCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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
