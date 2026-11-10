@extends('layouts.admin')

@section('title', 'Intelijen Risiko Rantai Pasok')

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Intelijen Risiko Rantai Pasok (Risk Intelligence)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penilaian indeks risiko multi-faktor berbasis Model Pembobotan Matematis (Weighted Risk Index)</p>
        </div>
        <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
            Pembaruan: {{ $risk['last_updated'] }}
        </span>
    </div>

    <!-- Main Risk Overview Card (Mockup 19 style) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Overall Score Card -->
        <div class="p-8 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Skor Risiko Global</span>
            <div class="relative w-40 h-40 flex items-center justify-center rounded-full bg-slate-50 dark:bg-slate-900 border-4 border-amber-500/40">
                <div>
                    <span class="text-5xl font-black text-slate-900 dark:text-white">{{ $risk['overall_score'] }}</span>
                    <span class="text-xs text-slate-400 block font-mono">/ 100</span>
                </div>
            </div>
            <div class="mt-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                    Status: Risiko {{ $risk['level'] }}
                </span>
            </div>
        </div>

        <!-- Breakdown Progress Bars -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm md:col-span-2 space-y-4 flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Rincian Komponen Pembobotan Risiko</h2>
                <p class="text-[11px] text-slate-400 mb-4">Setiap variabel berkontribusi terhadap skor agregat rantai pasok</p>

                <div class="space-y-4">
                    @foreach($risk['breakdown'] as $factor)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $factor['name'] }} <span class="text-[10px] text-slate-400 font-normal">({{ $factor['weight'] }})</span></span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $factor['score'] }} / 100</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div class="h-full rounded-full {{ $factor['score'] > 60 ? 'bg-amber-500' : 'bg-cyan-500' }}" style="width: {{ $factor['score'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mathematical Method Explanation -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400">
                <strong class="text-slate-700 dark:text-slate-300 block mb-0.5">Penjelasan Metodologi:</strong>
                {{ $risk['formula_explanation'] ?? ($risk['formula'] ?? 'Perhitungan indeks risiko komposit menggabungkan 4 parameter terverifikasi: Cuaca (35%), Kurs (25%), Inflasi (20%), dan Sentimen Berita (20%).') }} Indeks ini dihitung secara transparan dan objektif tanpa klaim estimasi fiktif.
            </div>
        </div>
    </div>
</div>
@endsection

