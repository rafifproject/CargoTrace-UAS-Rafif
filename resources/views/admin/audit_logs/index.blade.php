@extends('layouts.admin')

@section('title', 'Log Audit Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Log Audit Sistem (Audit Trails)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jejak rekam historis aktivitas otentikasi, mutasi data, dan perubahan konfigurasi sistem</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <select name="user_id" onchange="this.form.submit()" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
                    <option value="">Semua Pengguna (Actor)</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="action" onchange="this.form.submit()" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200">
                    <option value="">Semua Aksi</option>
                    <option value="LOGIN" {{ request('action') === 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                    <option value="LOGOUT" {{ request('action') === 'LOGOUT' ? 'selected' : '' }}>LOGOUT</option>
                    <option value="CREATE" {{ request('action') === 'CREATE' ? 'selected' : '' }}>CREATE</option>
                    <option value="UPDATE" {{ request('action') === 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                    <option value="DELETE" {{ request('action') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
                    <option value="STATUS_CHANGE" {{ request('action') === 'STATUS_CHANGE' ? 'selected' : '' }}>STATUS_CHANGE</option>
                    <option value="CONFIG_CHANGE" {{ request('action') === 'CONFIG_CHANGE' ? 'selected' : '' }}>CONFIG_CHANGE</option>
                    <option value="EXCEPTION_RESOLVED" {{ request('action') === 'EXCEPTION_RESOLVED' ? 'selected' : '' }}>EXCEPTION_RESOLVED</option>
                </select>
            </div>

            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200" title="Dari Tanggal">
            </div>

            <div class="flex items-center gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200" title="Sampai Tanggal">
                <button type="submit" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs rounded-lg">Filter</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="p-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-200" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Pengguna (Aktor)</th>
                        <th class="py-3 px-4">Aksi</th>
                        <th class="py-3 px-4">Entitas Terkait</th>
                        <th class="py-3 px-4">Alamat IP</th>
                        <th class="py-3 px-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3 px-4 font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $log->user->name ?? 'Tamu (Guest)' }}
                                <span class="block text-[10px] text-cyan-600 dark:text-cyan-400 font-normal">{{ $log->user->role ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase
                                    {{ in_array($log->action, ['DELETE', 'EXCEPTION_RAISED']) ? 'bg-rose-500/10 text-rose-500' : 
                                       (in_array($log->action, ['CREATE', 'LOGIN', 'EXCEPTION_RESOLVED']) ? 'bg-emerald-500/10 text-emerald-500' : 'bg-cyan-500/10 text-cyan-400') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ $log->entity_type }}
                                @if($log->entity_id)
                                    <span class="block text-[10px] text-slate-400 font-mono">ID: {{ $log->entity_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-sm">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada riwayat audit log tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

