@extends('layouts.admin')

@section('title', __('Pengaturan Sistem'))

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Pengaturan Sistem (System Settings)') }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Konfigurasi preferensi bahasa, zona waktu, ambang batas keparahan keterlambatan, dan status integrasi API') }}</p>
        </div>
    </div>

    <!-- General Settings Form -->
    <div class="p-6 lg:p-8 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm w-full">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
            <i data-lucide="sliders" class="w-4 h-4 text-cyan-500"></i>
            <span>{{ __('Preferensi Umum & Algoritma Keterlambatan') }}</span>
        </h2>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label for="app_language" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Bahasa Aplikasi Default') }}</label>
                    <select id="app_language" name="app_language" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-cyan-500">
                        <option value="id" {{ ($settings['app_language'] ?? 'id') === 'id' ? 'selected' : '' }}>{{ __('Bahasa Indonesia (Default)') }}</option>
                        <option value="en" {{ ($settings['app_language'] ?? '') === 'en' ? 'selected' : '' }}>{{ __('English (Alternative)') }}</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1.5">{{ __('Mengubah bahasa antarmuka label, tombol, dan navigasi.') }}</p>
                </div>

                <div>
                    <label for="app_timezone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Zona Waktu Sistem') }}</label>
                    <select id="app_timezone" name="app_timezone" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-cyan-500">
                        <option value="Asia/Jakarta" {{ ($settings['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB - UTC+7)</option>
                        <option value="Asia/Makassar" {{ ($settings['app_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA - UTC+8)</option>
                        <option value="Asia/Jayapura" {{ ($settings['app_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT - UTC+9)</option>
                        <option value="UTC" {{ ($settings['app_timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1.5">{{ __('Sinkronisasi stempel waktu seluruh logistik.') }}</p>
                </div>

                <div>
                    <label for="date_format" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Format Tampilan Tanggal') }}</label>
                    <input type="text" id="date_format" name="date_format" value="{{ $settings['date_format'] ?? 'd M Y, H:i' }}" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                    <p class="text-[10px] text-slate-400 mt-1.5">{{ __('Contoh: d M Y, H:i (28 Jan 2026, 14:30)') }}</p>
                </div>

                <div>
                    <label for="delay_threshold_hours" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Ambang Batas Keterlambatan (Jam)') }}</label>
                    <input type="number" id="delay_threshold_hours" name="delay_threshold_hours" value="{{ $settings['delay_threshold_hours'] ?? '6' }}" required min="1" max="168" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                    <p class="text-[10px] text-slate-400 mt-1.5">{{ __('Memicu peringatan jika kargo terlambat melebihi jam ini.') }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 rounded-xl shadow-md shadow-cyan-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>{{ __('Simpan Perubahan Pengaturan') }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- API Integration Status Table -->
    <div class="p-6 lg:p-8 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm w-full">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="network" class="w-4 h-4 text-cyan-500"></i>
                    <span>{{ __('Status Layanan Integrasi API Eksternal') }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">{{ __('Pemantauan gateway konektivitas data cuaca, valuta, makro ekonomi, dan direktori internasional') }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ __('Semua 8 Gateway Aktif') }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($apiStatuses as $key => $api)
                <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/40 flex flex-col justify-between hover:border-cyan-500/30 transition-all">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center shrink-0">
                                <i data-lucide="cpu" class="w-4 h-4"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $api['name'] }}</h4>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $api['status'] }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 pl-10">{{ __($api['type']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

