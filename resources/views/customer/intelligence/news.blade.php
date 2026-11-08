@extends('layouts.customer')

@section('title', 'Berita Logistik Global')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Berita Logistik & Rantai Pasok Global</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Informasi terkini mengenai arus perdagangan, kondisi pelabuhan internasional, dan dinamika maritim.</p>
    </div>
</div>

{{-- Category Tabs (Mockup 10) --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 text-xs">
    <a href="{{ route('customer.intelligence.news', ['category' => 'all']) }}" class="px-4 py-2 rounded-xl font-semibold transition-colors {{ $category === 'all' ? 'bg-cyan-600 text-white shadow-sm' : 'bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        Semua
    </a>
    <a href="{{ route('customer.intelligence.news', ['category' => 'shipping']) }}" class="px-4 py-2 rounded-xl font-semibold transition-colors {{ $category === 'shipping' ? 'bg-cyan-600 text-white shadow-sm' : 'bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        Pelayaran
    </a>
    <a href="{{ route('customer.intelligence.news', ['category' => 'customs']) }}" class="px-4 py-2 rounded-xl font-semibold transition-colors {{ $category === 'customs' ? 'bg-cyan-600 text-white shadow-sm' : 'bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        Kepabeanan
    </a>
    <a href="{{ route('customer.intelligence.news', ['category' => 'ports']) }}" class="px-4 py-2 rounded-xl font-semibold transition-colors {{ $category === 'ports' ? 'bg-cyan-600 text-white shadow-sm' : 'bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        Pelabuhan
    </a>
</div>

{{-- News Cards Grid (Mockup 10) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($news as $item)
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between hover:border-cyan-500/40 transition-colors">
        <div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800/40">
                    {{ $item['category'] ?? 'Global' }}
                </span>
                <span class="text-[11px] text-slate-400">{{ $item['time'] ?? 'Hari ini' }}</span>
            </div>
            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 mb-2 leading-snug line-clamp-2">
                {{ $item['title'] ?? 'Laporan Arus Peti Kemas Global' }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 mb-4 leading-relaxed">
                {{ $item['description'] ?? 'Optimalisasi jalur kargo internasional untuk mendukung kelancaran distribusi logistik.' }}
            </p>
        </div>
        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-slate-400 font-medium text-[11px]">{{ $item['source'] ?? 'Global Logistics Watch' }}</span>
            <span class="text-cyan-600 dark:text-cyan-400 font-semibold flex items-center gap-1">
                Info <i data-lucide="external-link" class="w-3 h-3"></i>
            </span>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-12 text-center text-slate-400">
        <i data-lucide="newspaper" class="w-10 h-10 mx-auto mb-2 opacity-30"></i>
        <p>Tidak ada berita untuk kategori ini saat ini.</p>
    </div>
    @endforelse
</div>
@endsection
