@extends('layouts.admin')

@section('title', 'Manajemen Ekspedisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Ekspedisi (Carrier Management)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola data mitra perusahaan pengiriman, pelayaran, dan maskapai kargo global</p>
        </div>
        <button type="button" onclick="openCreateModal()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Ekspedisi</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.carriers.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode, Nama Ekspedisi, atau Negara..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>
            <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">Status: Semua</option>
                <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="w-full sm:w-auto px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg">Cari</button>
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
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4 text-center">Pengiriman</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
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
                                    <a href="{{ $c->website }}" target="_blank" class="block text-[10px] text-cyan-500 hover:underline">{{ $c->website }}</a>
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
                                    <button type="button" onclick='openEditModal(@json($c))' class="p-1 rounded text-slate-500 hover:text-cyan-500 dark:text-slate-400 dark:hover:text-cyan-400" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('admin.carriers.destroy', $c) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus atau menonaktifkan ekspedisi ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400" title="Hapus / Nonaktifkan">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada ekspedisi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($carriers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $carriers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Edit Ekspedisi -->
<div id="carrierModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl">
        <h3 id="modalTitle" class="text-base font-bold text-slate-900 dark:text-white">Tambah Ekspedisi Baru</h3>
        
        <form id="carrierForm" method="POST" action="{{ route('admin.carriers.store') }}" class="mt-4 space-y-3">
            @csrf
            <div id="methodContainer"></div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="carrier_code" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Ekspedisi</label>
                    <input type="text" id="carrier_code" name="code" required placeholder="Contoh: DHL" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white uppercase">
                </div>
                <div>
                    <label for="carrier_name" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan</label>
                    <input type="text" id="carrier_name" name="name" required placeholder="Contoh: DHL Express" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="carrier_country" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Negara Asal</label>
                    <input type="text" id="carrier_country" name="country" required placeholder="Jerman" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="carrier_status" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select id="carrier_status" name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                        <option value="ACTIVE">Aktif</option>
                        <option value="INACTIVE">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="carrier_email" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Email Kontak</label>
                    <input type="email" id="carrier_email" name="contact_email" required placeholder="contact@dhl.com" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="carrier_phone" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">No. Telepon (Opsional)</label>
                    <input type="text" id="carrier_phone" name="contact_phone" placeholder="+49 228 1820" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label for="carrier_website" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Website Resmi (Opsional)</label>
                <input type="url" id="carrier_website" name="website" placeholder="https://www.dhl.com" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeCarrierModal()" class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500">Simpan Ekspedisi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Ekspedisi Baru';
        document.getElementById('carrierForm').action = "{{ route('admin.carriers.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('carrier_code').value = '';
        document.getElementById('carrier_name').value = '';
        document.getElementById('carrier_country').value = '';
        document.getElementById('carrier_email').value = '';
        document.getElementById('carrier_phone').value = '';
        document.getElementById('carrier_website').value = '';
        document.getElementById('carrier_status').value = 'ACTIVE';
        document.getElementById('carrierModal').classList.remove('hidden');
    }

    function openEditModal(carrier) {
        document.getElementById('modalTitle').textContent = 'Edit Data Ekspedisi: ' + carrier.name;
        document.getElementById('carrierForm').action = '/admin/carriers/' + carrier.id;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('carrier_code').value = carrier.code;
        document.getElementById('carrier_name').value = carrier.name;
        document.getElementById('carrier_country').value = carrier.country;
        document.getElementById('carrier_email').value = carrier.contact_email;
        document.getElementById('carrier_phone').value = carrier.contact_phone || '';
        document.getElementById('carrier_website').value = carrier.website || '';
        document.getElementById('carrier_status').value = carrier.status;
        document.getElementById('carrierModal').classList.remove('hidden');
    }

    function closeCarrierModal() {
        document.getElementById('carrierModal').classList.add('hidden');
    }
</script>
@endpush

