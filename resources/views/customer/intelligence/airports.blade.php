@extends('layouts.customer')

@section('title', 'Direktori Bandara Kargo Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-cyan-500">Dashboard</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Intelijen & Layanan Global</span>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-sky-600 dark:text-sky-400 font-semibold">Bandara Kargo Global</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-sky-500/10 text-sky-500 dark:bg-sky-500/20">
                    <i data-lucide="plane" class="w-5 h-5"></i>
                </span>
                <span>Direktori Bandara Kargo Global (Global Air Cargo Hubs)</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Jaringan simpul bandara kargo internasional dan terminal ekspres udara terintegrasi <strong>FreeAirportDB API</strong> (<span class="font-mono text-cyan-600 dark:text-cyan-400">api.freeairportdb.com</span>) untuk pengiriman cepat kargo Anda.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('customer.shipments.create') }}?mode=AIR" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-sky-600 hover:bg-sky-500 shadow-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Pesan Pengiriman Udara</span>
            </a>
        </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Hub Udara</span>
                <i data-lucide="plane-takeoff" class="w-4 h-4 text-sky-500"></i>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ count($allAirports) }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Bandara Kargo Siap Layani</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">#1 World SuperHub</span>
                <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
            </div>
            <p class="text-base font-bold text-amber-600 dark:text-amber-400 mt-1">HKG (Hong Kong)</p>
            <p class="text-[11px] text-slate-400 mt-0.5">4.8M Ton Kargo / Tahun</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Transit Kilat</span>
                <i data-lucide="zap" class="w-4 h-4 text-purple-500"></i>
            </div>
            <p class="text-base font-bold text-purple-600 dark:text-purple-400 mt-1">1 - 3 Hari Kerja</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Rata-rata Durasi Antar Benua</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Gateway Utama RI</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <p class="text-base font-bold text-emerald-600 dark:text-emerald-400 mt-1">CGK (Soekarno-Hatta)</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Terminal Kargo Nasional</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('customer.intelligence.airports') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode IATA (cth: CGK, SIN, HKG, MEM), Nama Bandara, Kota, atau Negara..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-500 rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Cari Bandara</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('customer.intelligence.airports') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors flex items-center gap-1">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Map of Global Airports -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-sky-500"></i>
                <span>Peta Sebaran Bandara Kargo Internasional Dunia</span>
            </h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">Klik marker untuk melihat spesifikasi bandara</span>
        </div>
        <div id="airportsMap" class="w-full h-88 rounded-xl border border-slate-200 dark:border-slate-800 z-10"></div>
    </div>

    <!-- Table of Airports -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                Daftar Simpul Bandara Kargo Internasional ({{ count($allAirports) }} Data)
            </h3>
            <span class="text-[11px] text-slate-400">Sumber: FreeAirportDB Open Aviation Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">IATA / ICAO</th>
                        <th class="py-3 px-4">Nama Bandara</th>
                        <th class="py-3 px-4">Negara & Kota</th>
                        <th class="py-3 px-4">Koordinat</th>
                        <th class="py-3 px-4">Kapasitas Kargo</th>
                        <th class="py-3 px-4">Kategori Hub</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($allAirports as $apt)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 font-mono">
                                    <span class="font-extrabold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 px-1.5 py-0.5 rounded border border-sky-200 dark:border-sky-800">
                                        {{ $apt['code'] ?? '-' }}
                                    </span>
                                    <span class="text-slate-400 text-[10px]">
                                        {{ $apt['icao'] ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">✈️</span>
                                    <span>{{ $apt['name'] ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                <div class="flex items-center gap-1.5">
                                    @if(!empty($apt['iso']))
                                        <img src="https://flagcdn.com/w40/{{ strtolower($apt['iso']) }}.png" class="w-4 h-3 rounded object-cover shadow-2xs" alt="">
                                    @endif
                                    <span>{{ $apt['city'] ?? '-' }}, <strong>{{ $apt['country'] ?? '-' }}</strong></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                                {{ number_format($apt['lat'] ?? 0, 3) }}, {{ number_format($apt['lng'] ?? 0, 3) }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $apt['capacity'] ?? 'TBA' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                    {{ $apt['type'] ?? 'International Airport' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('customer.shipments.create') }}?mode=AIR" class="px-2.5 py-1 text-[11px] font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 hover:bg-sky-100 dark:hover:bg-sky-900/60 border border-sky-200 dark:border-sky-800 rounded-lg transition-colors inline-flex items-center gap-1">
                                    <span>Pesan Kargo</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <i data-lucide="plane" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                <p>Tidak ada bandara yang cocok dengan kata kunci pencarian Anda.</p>
                            </td>
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
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('airportsMap', {
            center: [20, 20],
            zoom: 2.2,
            minZoom: 2,
            maxZoom: 18
        });

        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Satelit | FreeAirportDB'
        }).addTo(map);

        const airports = {!! json_encode(array_values($allAirports)) !!};

        airports.forEach(a => {
            if (!a.lat || !a.lng) return;

            const icon = L.divIcon({
                className: 'airport-marker-wrapper',
                html: `<div style="background:#0284C7; color:#fff; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(2,132,199,0.5);">✈️</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([a.lat, a.lng], { icon }).addTo(map);

            const popupHtml = `
                <div style="font-family: Outfit, sans-serif; min-width: 200px; padding: 2px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-weight:800; font-size:12px; color:#0284C7;">${a.code} / ${a.icao || ''}</span>
                        <span style="font-size:10px; background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:6px; font-weight:700;">AIR CARGO</span>
                    </div>
                    <div style="font-weight:700; font-size:12px; color:#0f172a; margin-bottom:3px;">${a.name}</div>
                    <div style="font-size:11px; color:#64748b; margin-bottom:6px;">📍 ${a.city}, ${a.country}</div>
                    <div style="border-top:1px solid #f1f5f9; padding-top:4px; font-size:10px; color:#475569; display:grid; grid-template-columns:1fr 1fr; gap:4px;">
                        <div><strong>Kapasitas:</strong><br>${a.capacity || '-'}</div>
                        <div><strong>Runway:</strong><br>${a.runway || '-'}</div>
                    </div>
                    <div style="margin-top:6px; text-align:center;">
                        <a href="{{ route('customer.shipments.create') }}?mode=AIR" style="display:inline-block; width:100%; background:#0284C7; color:#fff; text-decoration:none; padding:5px 8px; border-radius:6px; font-size:11px; font-weight:700;">✈️ Pesan Rute Udara Ini</a>
                    </div>
                </div>
            `;
            marker.bindPopup(popupHtml);
        });
    });
</script>
@endpush

