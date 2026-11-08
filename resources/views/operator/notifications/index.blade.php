@extends('layouts.operator')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pusat Notifikasi Operasional</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pemberitahuan insiden, keterlambatan kargo, dan pembaruan sistem secara langsung.</p>
    </div>

    @if($unreadCount > 0)
    <form method="POST" action="{{ route('operator.notifications.mark-all-read') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
            <i data-lucide="check-check" class="w-4 h-4 text-cyan-500"></i>
            Tandai Semua Dibaca
        </button>
    </form>
    @endif
</div>

{{-- Tabs Filter (Mockup 15) --}}
<div class="flex items-center gap-2 mb-6 border-b border-slate-200 dark:border-slate-800 text-xs font-semibold">
    <a href="{{ route('operator.notifications.index', ['tab' => 'all']) }}" class="pb-3 px-3 transition-colors {{ $tab === 'all' ? 'text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500 font-bold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
        Semua Notifikasi ({{ $notifications->total() }})
    </a>
    <a href="{{ route('operator.notifications.index', ['tab' => 'unread']) }}" class="pb-3 px-3 transition-colors {{ $tab === 'unread' ? 'text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500 font-bold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
        Belum Dibaca ({{ $unreadCount }})
    </a>
    <a href="{{ route('operator.notifications.index', ['tab' => 'read']) }}" class="pb-3 px-3 transition-colors {{ $tab === 'read' ? 'text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500 font-bold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
        Sudah Dibaca
    </a>
</div>

{{-- Notifications List (Mockup 15) --}}
<div class="space-y-3">
    @forelse($notifications as $notif)
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border {{ $notif->is_read ? 'border-slate-200 dark:border-slate-800 opacity-80' : 'border-cyan-500/40 ring-1 ring-cyan-500/10' }} p-4 shadow-sm flex items-start justify-between gap-4 transition-all">
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 rounded-xl flex-shrink-0 mt-0.5 {{ $notif->severity === 'critical' || $notif->severity === 'high' ? 'bg-rose-500/10 text-rose-500' : 'bg-cyan-500/10 text-cyan-500' }}">
                <i data-lucide="{{ $notif->type === 'EXCEPTION' ? 'alert-triangle' : 'bell' }}" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-xs text-slate-800 dark:text-slate-100">{{ $notif->title }}</h3>
                    @if(!$notif->is_read)
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                    @endif
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $notif->message }}</p>
                <span class="text-[10px] text-slate-400 mt-2 block">{{ $notif->created_at->diffForHumans() }}</span>
            </div>
        </div>

        @if(!$notif->is_read)
        <form method="POST" action="{{ route('operator.notifications.mark-read', $notif) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-cyan-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Tandai sudah dibaca">
                <i data-lucide="check" class="w-4 h-4"></i>
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="py-12 text-center text-slate-400">
        <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-2 opacity-30"></i>
        <p class="text-sm">Tidak ada notifikasi pada kategori ini.</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

