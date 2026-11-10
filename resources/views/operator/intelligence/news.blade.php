@extends('layouts.operator')

@section('title', 'Warta & Berita Logistik Global')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="newspaper" class="w-5 h-5 text-cyan-500"></i>
                <span>Warta & Berita Global (Global Logistics News)</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan berita visual maritim, dinamika rantai pasok dunia, dan analisis sentimen Lexicon-based</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Feed</span>
            </span>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs">
        <a href="{{ route('operator.intelligence.news', ['category' => 'all']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'all' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }}">
            Semua Berita
        </a>
        <a href="{{ route('operator.intelligence.news', ['category' => 'shipping']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'shipping' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }}">
            ⚓ Perkapalan (Shipping)
        </a>
        <a href="{{ route('operator.intelligence.news', ['category' => 'logistics']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'logistics' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }}">
            📦 Logistik & Pelabuhan
        </a>
        <a href="{{ route('operator.intelligence.news', ['category' => 'economy']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'economy' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }}">
            📈 Ekonomi & Perdagangan
        </a>
        <a href="{{ route('operator.intelligence.news', ['category' => 'customs']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $category === 'customs' ? 'bg-cyan-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }}">
            🛡️ Kepabeanan (Customs)
        </a>
    </div>

    <!-- News Articles List with Rich Images -->
    <div class="space-y-4">
        @forelse($news as $article)
            <div class="group p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-start gap-4 hover:border-cyan-500/40 hover:shadow-md transition-all duration-300">
                <!-- News Thumbnail Image -->
                <div class="w-full sm:w-64 md:w-72 h-48 sm:h-44 rounded-xl overflow-hidden relative flex-shrink-0 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                    <img src="{{ $article['image'] ?? 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=700&q=80' }}"
                         alt="{{ $article['headline'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=700&q=80';">
                    <div class="absolute top-2.5 left-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-white border border-white/20 shadow-xs">
                            {{ $article['category'] }}
                        </span>
                    </div>
                </div>

                <!-- Article Details -->
                <div class="space-y-2 flex-1 flex flex-col justify-between self-stretch">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $article['source'] }}</span>
                                <span>&bull;</span>
                                <span>{{ $article['date'] }}</span>
                            </span>
                        </div>

                        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                            {{ $article['headline'] }}
                        </h2>

                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-1.5 line-clamp-2 sm:line-clamp-3">
                            {{ $article['text'] }}
                        </p>
                    </div>

                    <!-- Sentiment Badge & External Link -->
                    <div class="flex items-center justify-between gap-3 pt-3 mt-2 border-t border-slate-100 dark:border-slate-800/80">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                            {{ ($article['sentiment']['color'] ?? '') === 'rose' ? 'bg-rose-500/10 text-rose-500 border border-rose-500/20' : 
                               (($article['sentiment']['color'] ?? '') === 'emerald' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ ($article['sentiment']['color'] ?? '') === 'rose' ? 'bg-rose-500' : (($article['sentiment']['color'] ?? '') === 'emerald' ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                            Sentimen: {{ $article['sentiment']['label'] ?? 'Netral' }}
                        </span>
                        
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="text-xs text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 font-semibold inline-flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-cyan-50 dark:hover:bg-cyan-950/40 transition-colors">
                            <span>Baca Selengkapnya</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-400 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800">
                <i data-lucide="newspaper" class="w-8 h-8 text-slate-400 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm font-medium">Tidak ada berita dalam kategori ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
