@extends('layouts.admin')

@section('title', 'Berita Logistik Global')

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Warta & Berita Global (Global Logistics News)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan berita perkapalan, rantai pasok dunia, dan analisis sentimen Lexicon-based (GNews API)</p>
        </div>
    </div>

    <!-- Category Filter Tabs (Mockup 16: Semua, Logistik, Perdagangan, Ekonomi, Geopolitik) -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs">
        <a href="{{ route('admin.intelligence.news', ['category' => 'all']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'all' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Semua Berita
        </a>
        <a href="{{ route('admin.intelligence.news', ['category' => 'logistics']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'logistics' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Logistik & Pelabuhan
        </a>
        <a href="{{ route('admin.intelligence.news', ['category' => 'shipping']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'shipping' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Perkapalan (Shipping)
        </a>
        <a href="{{ route('admin.intelligence.news', ['category' => 'economy']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'economy' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Ekonomi & Perdagangan
        </a>
    </div>

    <!-- News Articles List -->
    <div class="space-y-4">
        @forelse($news as $article)
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-start justify-between gap-4 hover:border-cyan-500/40 transition-colors">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $article['category'] }}
                        </span>
                        <span class="text-xs text-slate-400">&bull; {{ $article['source'] }} &bull; {{ $article['date'] }}</span>
                    </div>

                    <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                        {{ $article['headline'] }}
                    </h2>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $article['text'] }}
                    </p>
                </div>

                <!-- Sentiment Badge (Lexicon-Based PHP: Negatif / Positif / Netral) -->
                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                        {{ $article['sentiment']['color'] === 'rose' ? 'bg-rose-500/10 text-rose-500 border border-rose-500/20' : 
                           ($article['sentiment']['color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20') }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $article['sentiment']['color'] === 'rose' ? 'bg-rose-500' : ($article['sentiment']['color'] === 'emerald' ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                        Sentimen: {{ $article['sentiment']['label'] }}
                    </span>
                    <a href="{{ $article['url'] }}" target="_blank" class="text-xs text-cyan-500 hover:underline flex items-center gap-1 font-semibold">
                        <span>Baca Berita</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-400 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800">
                Tidak ada berita dalam kategori ini.
            </div>
        @endforelse
    </div>
</div>
@endsection

