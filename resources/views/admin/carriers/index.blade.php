@extends('layouts.admin')

@section('title', 'Direktori & Manajemen Ekspedisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Direktori & Titik Ekspedisi Global</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Peta interaktif sebaran 206 titik markas resmi mitra ekspedisi, maskapai kargo, dan pelayaran internasional dari 195+ negara dunia</p>
        </div>
        <button type="button" onclick="openCreateModal()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Ekspedisi</span>
        </button>
    </div>

    <!-- PETA SEBARAN TITIK EKSPEDISI GLOBAL DENGAN PANEL CARI NEGARA -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Sub-Header Peta -->
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Peta Sebaran Titik Markas Ekspedisi</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Klik titik marker atau pilih negara di panel samping untuk memfokuskan lokasi markas ekspedisi</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>{{ count($mapCarriers) }} Titik Ekspedisi Terdaftar</span>
                </span>
            </div>
        </div>

        <!-- Grid Container: Peta di Kiri + Sidebar Cari Negara di Kanan -->
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- Peta Leaflet (Lg: col-span-8) -->
            <div class="lg:col-span-8 relative">
                <div id="carriersMap" class="w-full h-[520px] bg-slate-100 dark:bg-slate-900 z-10"></div>
                <!-- Layer Info Pill -->
                <div class="absolute bottom-3 left-3 z-[400] bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200 dark:border-slate-800 px-3 py-1.5 rounded-lg text-[11px] font-medium text-slate-600 dark:text-slate-300 shadow-sm flex items-center gap-2 pointer-events-none">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                    <span>Marker Oranye/Emas: Titik Markas Resmi Ekspedisi Global</span>
                </div>
            </div>

            <!-- Sidebar Cari Negara & Ekspedisi (Lg: col-span-4) -->
            <div class="lg:col-span-4 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 flex flex-col bg-slate-50/50 dark:bg-slate-900/30">
                <!-- Search Box -->
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-[#0A1128]">
                    <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5 mb-2">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Cari Negara / Ekspedisi</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="carrier_side_search" oninput="filterSideCarriers(this.value)" placeholder="Ketik nama negara atau ekspedisi..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <!-- Selected Carrier Active Banner -->
                <div id="side_selected_banner" class="hidden p-3 mx-3 mt-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 shadow-xs">
                    <div class="flex items-center gap-2 mb-1">
                        <span id="banner_flag" class="text-xl">🌐</span>
                        <div>
                            <h4 id="banner_name" class="font-bold text-slate-900 dark:text-white text-xs leading-tight">Nama Ekspedisi</h4>
                            <p id="banner_country" class="text-[10px] text-slate-500 dark:text-slate-400">Negara Asal</p>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-amber-200/60 dark:border-amber-800/40 flex items-center justify-between text-[11px]">
                        <span class="font-mono font-bold text-amber-700 dark:text-amber-400" id="banner_code">CODE</span>
                        <span class="text-slate-600 dark:text-slate-300 font-semibold" id="banner_shipments">0 Kargo</span>
                    </div>
                </div>

                <!-- Scrollable Country & Carrier List -->
                <div class="flex-1 overflow-y-auto max-h-[440px] p-2 space-y-1" id="side_carriers_list">
                    @foreach($mapCarriers as $index => $mc)
                        <button type="button"
                                onclick="zoomToCarrier({{ $mc['lat'] }}, {{ $mc['lng'] }}, '{{ $mc['code'] }}')"
                                data-name="{{ strtolower($mc['name']) }}"
                                data-country="{{ strtolower($mc['country']) }}"
                                data-code="{{ strtolower($mc['code']) }}"
                                class="side-carrier-item w-full text-left p-2.5 rounded-xl border border-transparent hover:border-slate-200 dark:hover:border-slate-700 bg-white/70 dark:bg-slate-900/60 hover:bg-white dark:hover:bg-slate-900 transition-all flex items-center justify-between gap-3 group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                @if(!empty($mc['iso']) && $mc['iso'] !== 'un')
                                    <img src="https://flagcdn.com/w40/{{ $mc['iso'] }}.png" alt="{{ $mc['country'] }}" class="w-6 h-4 object-cover rounded shadow-xs shrink-0">
                                @else
                                    <span class="text-sm shrink-0">🏢</span>
                                @endif
                                <div class="truncate">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                        {{ $mc['country'] }}
                                    </h4>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                        Markas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $mc['name'] }}</span>
                                    </p>
                                </div>
                            </div>
                            <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 group-hover:text-amber-600 dark:group-hover:text-amber-400 shrink-0">
                                {{ $mc['code'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar untuk Tabel Direktori -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.carriers.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode, Nama Ekspedisi, atau Negara di Tabel..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>
            <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">Status: Semua</option>
                <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="w-full sm:w-auto px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg transition-colors">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.carriers.index') }}" class="w-full sm:w-auto px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Carrier Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Ekspedisi</th>
                        <th class="py-3 px-4">Negara Asal</th>
                        <th class="py-3 px-4">Kontak Resmi</th>
                        <th class="py-3 px-4 text-center">Pengiriman Terdaftar</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Titik Peta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($carriers as $c)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-black text-cyan-600 dark:text-cyan-400 tracking-wider">
                                {{ $c->code }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $c->name }}
                                @if($c->website)
                                    <a href="{{ $c->website }}" target="_blank" rel="noopener noreferrer" class="block text-[10px] text-cyan-500 hover:underline">{{ $c->website }}</a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $c->country }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                <span class="block">{{ $c->contact_email }}</span>
                                <span class="text-[10px] font-mono">{{ $c->contact_phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 dark:text-white">
                                {{ $c->shipments_count }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $c->status === 'ACTIVE' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' }}">
                                    {{ $c->status === 'ACTIVE' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            onclick="focusCarrierCode('{{ $c->code }}')"
                                            class="px-2.5 py-1 rounded text-[11px] font-semibold bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-colors flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        <span>Fokus Peta</span>
                                    </button>
                                    <button type="button"
                                            onclick='openEditModal(@json($c))'
                                            class="p-1 rounded text-slate-400 hover:text-cyan-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                            title="Edit Ekspedisi">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button type="button"
                                            onclick="confirmDelete('{{ $c->id }}', '{{ $c->name }}')"
                                            class="p-1 rounded text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                            title="Hapus Ekspedisi">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data ekspedisi yang sesuai pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($carriers->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $carriers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Create Carrier -->
<div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tambah Ekspedisi Baru</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.carriers.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Ekspedisi (Unik)</label>
                <input type="text" name="code" required placeholder="Contoh: MSC, DHL, POS" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ekspedisi</label>
                <input type="text" name="name" required placeholder="Contoh: Mediterranean Shipping Company" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Negara Asal</label>
                <input type="text" name="country" required placeholder="Contoh: Indonesia, Switzerland, Japan" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email Kontak Resmi</label>
                <input type="email" name="contact_email" required placeholder="support@carrier.com" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon</label>
                <input type="text" name="contact_phone" placeholder="+62 21 555-0199" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Situs Web Resmi</label>
                <input type="url" name="website" placeholder="https://www.carrier.com" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Status Operasional</label>
                <select name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
                    <option value="ACTIVE">Aktif</option>
                    <option value="INACTIVE">Nonaktif</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeCreateModal()" class="px-3 py-1.5 text-xs rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Carrier -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Edit Informasi Ekspedisi</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Ekspedisi</label>
                <input type="text" id="edit_code" name="code" required class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ekspedisi</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Negara Asal</label>
                <input type="text" id="edit_country" name="country" required class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email Kontak Resmi</label>
                <input type="email" id="edit_contact_email" name="contact_email" required class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon</label>
                <input type="text" id="edit_contact_phone" name="contact_phone" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Situs Web Resmi</label>
                <input type="url" id="edit_website" name="website" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Status Operasional</label>
                <select id="edit_status" name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
                    <option value="ACTIVE">Aktif</option>
                    <option value="INACTIVE">Nonaktif</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-3 py-1.5 text-xs rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete Carrier Hidden -->
<form id="deleteForm" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let carriersMap = null;
    let carrierMarkers = {};
    const mapCarriersData = {!! json_encode(array_values($mapCarriers->toArray())) !!};

    document.addEventListener('DOMContentLoaded', function () {
        initCarriersMap();
    });

    function initCarriersMap() {
        if (!document.getElementById('carriersMap')) return;

        // Inisialisasi peta Leaflet
        carriersMap = L.map('carriersMap', {
            center: [20, 10],
            zoom: 2,
            minZoom: 2,
            maxZoom: 18,
            zoomControl: true
        });

        // Layer peta CartoDB Positron / Google-like elegan
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CargoTrace Global Carrier Network &copy; CartoDB',
            maxZoom: 19
        }).addTo(carriersMap);

        // Render setiap titik ekspedisi
        mapCarriersData.forEach(c => {
            if (!c.lat || !c.lng || (c.lat === 0 && c.lng === 0)) return;

            // Custom HTML Pin Marker bernuansa emas/amber
            const flagUrl = (c.iso && c.iso !== 'un') ? `https://flagcdn.com/w40/${c.iso}.png` : null;
            const flagHtml = flagUrl ? `<img src="${flagUrl}" style="width:16px;height:11px;border-radius:2px;display:inline-block;object-fit:cover;">` : '🏢';

            const customIcon = L.divIcon({
                className: 'carrier-custom-pin',
                html: `
                    <div style="position:relative; width:30px; height:30px; display:flex; align-items:center; justify-content:center; background:#D97706; color:#fff; border-radius:50%; box-shadow:0 3px 8px rgba(217,119,6,0.45); border:2px solid #FFFFFF; cursor:pointer; transition:transform 0.15s ease;">
                        <span style="font-size:12px;">🏢</span>
                    </div>
                `,
                iconSize: [30, 30],
                iconAnchor: [15, 15],
                popupAnchor: [0, -15]
            });

            const marker = L.marker([c.lat, c.lng], { icon: customIcon }).addTo(carriersMap);

            // Popup informatif mewah persis seperti referensi
            const popupHtml = `
                <div style="font-family:inherit; min-width:240px; padding:4px 2px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; border-bottom:1px solid #E2E8F0; padding-bottom:8px;">
                        ${flagUrl ? `<img src="${flagUrl}" style="width:24px; height:16px; border-radius:3px; object-fit:cover; box-shadow:0 1px 3px rgba(0,0,0,0.1);">` : '🏢'}
                        <div>
                            <div style="font-size:13px; font-weight:800; color:#0F172A; line-height:1.2;">${c.name}</div>
                            <div style="font-size:11px; color:#64748B;">${c.country}</div>
                        </div>
                    </div>
                    <div style="background:#F5F3FF; color:#6D28D9; border-radius:8px; padding:6px 10px; font-size:11px; font-weight:700; margin-bottom:6px;">
                        🏢 Kode: <b>${c.code}</b> | Mitra Resmi
                    </div>
                    <div style="background:#EFF6FF; color:#1D4ED8; border-radius:8px; padding:6px 10px; font-size:11px; margin-bottom:8px;">
                        📦 Pengiriman Aktif: <b>${c.shipments_count || 0} Kargo</b>
                    </div>
                    <div style="font-size:11px; color:#475569; margin-bottom:8px; line-height:1.4;">
                        <div>✉️ ${c.contact_email || '-'}</div>
                        <div>📞 ${c.contact_phone || '-'}</div>
                    </div>
                    ${c.website ? `
                        <a href="${c.website}" target="_blank" rel="noopener noreferrer" style="display:block; text-align:center; background:#D97706; color:#FFFFFF; font-weight:700; padding:6px 10px; border-radius:8px; text-decoration:none; font-size:11px;">
                            Kunjungi Situs Resmi →
                        </a>
                    ` : ''}
                </div>
            `;

            marker.bindPopup(popupHtml, { maxWidth: 300 });

            marker.on('click', function () {
                updateSelectedBanner(c);
            });

            // Simpan referensi marker dengan key lowercase code & country
            carrierMarkers[c.code.toLowerCase()] = { marker: marker, data: c };
            carrierMarkers[c.country.toLowerCase()] = { marker: marker, data: c };
        });
    }

    function zoomToCarrier(lat, lng, code) {
        if (!carriersMap) return;

        carriersMap.flyTo([lat, lng], 6, { duration: 1.2 });

        const item = carrierMarkers[code.toLowerCase()];
        if (item && item.marker) {
            item.marker.openPopup();
            updateSelectedBanner(item.data);
        }

        // Highlight item di sidebar
        document.querySelectorAll('.side-carrier-item').forEach(el => {
            el.classList.remove('border-amber-500', 'bg-amber-50', 'dark:bg-amber-950/40');
            if (el.getAttribute('data-code') === code.toLowerCase()) {
                el.classList.add('border-amber-500', 'bg-amber-50', 'dark:bg-amber-950/40');
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    function focusCarrierCode(code) {
        const item = carrierMarkers[code.toLowerCase()];
        if (item) {
            window.scrollTo({ top: document.getElementById('carriersMap').offsetTop - 80, behavior: 'smooth' });
            setTimeout(() => {
                zoomToCarrier(item.data.lat, item.data.lng, code);
            }, 300);
        }
    }

    function updateSelectedBanner(c) {
        const banner = document.getElementById('side_selected_banner');
        if (!banner) return;

        banner.classList.remove('hidden');
        document.getElementById('banner_name').textContent = c.name;
        document.getElementById('banner_country').textContent = c.country;
        document.getElementById('banner_code').textContent = c.code;
        document.getElementById('banner_shipments').textContent = (c.shipments_count || 0) + ' Kargo Terdaftar';

        const flagEl = document.getElementById('banner_flag');
        if (c.iso && c.iso !== 'un') {
            flagEl.innerHTML = `<img src="https://flagcdn.com/w40/${c.iso}.png" class="w-6 h-4 object-cover rounded shadow-xs inline-block">`;
        } else {
            flagEl.textContent = '🏢';
        }
    }

    function filterSideCarriers(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.side-carrier-item');
        items.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const country = el.getAttribute('data-country') || '';
            const code = el.getAttribute('data-code') || '';
            if (!q || name.includes(q) || country.includes(q) || code.includes(q)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    // Modal Operations
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(carrier) {
        const form = document.getElementById('editForm');
        form.action = `/admin/carriers/${carrier.id}`;
        document.getElementById('edit_code').value = carrier.code || '';
        document.getElementById('edit_name').value = carrier.name || '';
        document.getElementById('edit_country').value = carrier.country || '';
        document.getElementById('edit_contact_email').value = carrier.contact_email || '';
        document.getElementById('edit_contact_phone').value = carrier.contact_phone || '';
        document.getElementById('edit_website').value = carrier.website || '';
        document.getElementById('edit_status').value = carrier.status || 'ACTIVE';
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus ekspedisi "${name}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/carriers/${id}`;
            form.submit();
        }
    }
</script>
@endpush
