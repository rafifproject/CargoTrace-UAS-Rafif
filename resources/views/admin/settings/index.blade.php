@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Pengaturan Sistem (System Settings)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfigurasi preferensi bahasa, zona waktu, ambang batas keparahan keterlambatan, dan status integrasi API</p>
        </div>
    </div>

    <!-- General Settings Form -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="sliders" class="w-4 h-4 text-cyan-500"></i>
            <span>Preferensi Umum & Algoritma Keterlambatan</span>
        </h2>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="app_language" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bahasa Aplikasi Default</label>
                    <select id="app_language" name="app_language" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-cyan-500">
                        <option value="id" {{ ($settings['app_language'] ?? 'id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia (Default)</option>
                        <option value="en" {{ ($settings['app_language'] ?? '') === 'en' ? 'selected' : '' }}>English (Alternative)</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Mengubah bahasa antarmuka label, tombol, dan navigasi.</p>
                </div>

                <div>
                    <label for="app_timezone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Zona Waktu Sistem</label>
                    <select id="app_timezone" name="app_timezone" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-cyan-500">
                        <option value="Asia/Jakarta" {{ ($settings['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB - UTC+7)</option>
                        <option value="Asia/Makassar" {{ ($settings['app_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA - UTC+8)</option>
                        <option value="Asia/Jayapura" {{ ($settings['app_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT - UTC+9)</option>
                        <option value="UTC" {{ ($settings['app_timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date_format" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Format Tampilan Tanggal</label>
                    <input type="text" id="date_format" name="date_format" value="{{ $settings['date_format'] ?? 'd M Y, H:i' }}" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Contoh: d M Y, H:i (28 Jan 2026, 14:30)</p>
                </div>

                <div>
                    <label for="delay_threshold_hours" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ambang Batas Keterlambatan (Jam)</label>
                    <input type="number" id="delay_threshold_hours" name="delay_threshold_hours" value="{{ $settings['delay_threshold_hours'] ?? '6' }}" required min="1" max="168" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Keterlambatan melebihi nilai ini akan memicu Peringatan Operasional & Notifikasi.</p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-xl shadow-md shadow-cyan-600/20 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- API Integration Status Table -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
            <i data-lucide="network" class="w-4 h-4 text-cyan-500"></i>
            <span>Status Layanan Integrasi API Eksternal</span>
        </h2>
        <p class="text-xs text-slate-400 mb-4">Pemantauan gateway konektivitas data cuaca, valuta, makro ekonomi, dan direktori internasional</p>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($apiStatuses as $key => $api)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $api['name'] }}</h4>
                        <p class="text-[11px] text-slate-400">{{ $api['type'] }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $api['status'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

