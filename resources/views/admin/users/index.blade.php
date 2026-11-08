@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Peran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Pengguna & Peran (RBAC)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola akun hak akses multi-role: Administrator Sistem, Operator Logistik, dan Pelanggan Kargo</p>
        </div>
        <button type="button" onclick="openCreateUserModal()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-colors">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div class="relative md:col-span-2">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Email, atau Perusahaan..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>
            <div>
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Semua Peran (Role)</option>
                    <option value="ADMINISTRATOR" {{ request('role') === 'ADMINISTRATOR' ? 'selected' : '' }}>Administrator</option>
                    <option value="LOGISTICS_OPERATOR" {{ request('role') === 'LOGISTICS_OPERATOR' ? 'selected' : '' }}>Logistics Operator</option>
                    <option value="CUSTOMER" {{ request('role') === 'CUSTOMER' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Semua Status</option>
                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                    <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- User Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Terakhir Masuk</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-[11px]">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <span>{{ $user->name }}</span>
                                        @if($user->company_name)
                                            <span class="block text-[10px] text-slate-400 font-normal">{{ $user->company_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 font-mono">
                                {{ $user->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->role === 'ADMINISTRATOR')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        Administrator
                                    </span>
                                @elseif($user->role === 'LOGISTICS_OPERATOR')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Logistics Operator
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        Customer
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->status === 'ACTIVE' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-400' }}">
                                    {{ $user->status === 'ACTIVE' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick='openEditUserModal(@json($user))' class="p-1 rounded text-slate-500 hover:text-cyan-500" title="Edit Pengguna">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-slate-500 hover:text-rose-500" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Edit Pengguna -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl">
        <h3 id="userModalTitle" class="text-base font-bold text-slate-900 dark:text-white">Tambah Pengguna Baru</h3>
        
        <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" class="mt-4 space-y-3">
            @csrf
            <div id="userMethodContainer"></div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="u_name" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" id="u_name" name="name" required placeholder="Budi Santoso" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="u_email" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" id="u_email" name="email" required placeholder="budi@perusahaan.com" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="u_role" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Peran (Role)</label>
                    <select id="u_role" name="role" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                        <option value="CUSTOMER">Customer (Pelanggan)</option>
                        <option value="LOGISTICS_OPERATOR">Logistics Operator</option>
                        <option value="ADMINISTRATOR">Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="u_status" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Status Akun</label>
                    <select id="u_status" name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                        <option value="ACTIVE">Aktif</option>
                        <option value="INACTIVE">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="u_company" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Perusahaan / Entitas</label>
                    <input type="text" id="u_company" name="company_name" placeholder="PT Global Logistik" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="u_phone" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">No. Telepon</label>
                    <input type="text" id="u_phone" name="phone" placeholder="+62 812-3456-7890" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label for="u_password" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Kata Sandi <span id="passHint" class="text-slate-400 font-normal">(Wajib untuk pengguna baru)</span>
                </label>
                <input type="password" id="u_password" name="password" placeholder="••••••••" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeUserModal()" class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateUserModal() {
        document.getElementById('userModalTitle').textContent = 'Tambah Pengguna Baru';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('userMethodContainer').innerHTML = '';
        document.getElementById('u_name').value = '';
        document.getElementById('u_email').value = '';
        document.getElementById('u_role').value = 'CUSTOMER';
        document.getElementById('u_status').value = 'ACTIVE';
        document.getElementById('u_company').value = '';
        document.getElementById('u_phone').value = '';
        document.getElementById('u_password').value = '';
        document.getElementById('u_password').required = true;
        document.getElementById('passHint').textContent = '(Wajib diisi)';
        document.getElementById('userModal').classList.remove('hidden');
    }

    function openEditUserModal(user) {
        document.getElementById('userModalTitle').textContent = 'Edit Pengguna: ' + user.name;
        document.getElementById('userForm').action = '/admin/users/' + user.id;
        document.getElementById('userMethodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('u_name').value = user.name;
        document.getElementById('u_email').value = user.email;
        document.getElementById('u_role').value = user.role;
        document.getElementById('u_status').value = user.status;
        document.getElementById('u_company').value = user.company_name || '';
        document.getElementById('u_phone').value = user.phone || '';
        document.getElementById('u_password').value = '';
        document.getElementById('u_password').required = false;
        document.getElementById('passHint').textContent = '(Kosongkan jika tidak ingin mengubah kata sandi)';
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }
</script>
@endpush

