@extends('layouts.customer')

@section('title', 'Profil Akun Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Profil & Pengaturan Akun</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola identitas perusahaan kargo dan keamanan akun Customer Anda.</p>
</div>

{{-- Profile Card (Mockup 13) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sisi Kiri: Profil Singkat --}}
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col items-center text-center">
        <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-cyan-600 to-blue-700 text-white font-extrabold text-3xl flex items-center justify-center shadow-lg ring-4 ring-cyan-500/20 mb-4">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <h2 class="text-base font-bold text-slate-800 dark:text-white">{{ $user->company_name ?: $user->name }}</h2>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mt-1">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
            Verified Customer
        </span>
        <p class="text-xs text-slate-400 mt-2">{{ $user->email }}</p>

        <div class="w-full mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3 text-xs text-left">
            <div class="flex justify-between">
                <span class="text-slate-400">ID Pelanggan:</span>
                <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">CUST-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Total Kargo:</span>
                <span class="font-semibold text-cyan-500">{{ $user->shipments()->count() }} Shipment</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Bergabung:</span>
                <span class="text-slate-600 dark:text-slate-300">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Sisi Kanan: Form Ubah Profil (Mockup 13) --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-5 flex items-center gap-2">
            <i data-lucide="user-check" class="w-4 h-4 text-cyan-500"></i>
            Informasi Akun Pelanggan
        </h3>

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Perusahaan / Entitas Bisnis</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="cth. PT IndoNusa Logistik" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Lengkap Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Alamat Email Terdaftar</label>
                    <input type="email" value="{{ $user->email }}" readonly class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 cursor-not-allowed">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+62 21 5558 9000" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Keamanan (Ganti Kata Sandi)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan bila tidak ingin diubah" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Ubah Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

