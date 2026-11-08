@extends('layouts.admin')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Pusat Notifikasi (Notification Center)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Peringatan operasional real-time, keterlambatan rute, dan eskalasi penanganan masalah kargo</p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 hover:bg-cyan-100 transition-colors">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    <span>Tandai Semua Telah Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Tabs (Mockup 20 style: Semua, Belum Dibaca, Dibaca) -->
    <div class="flex gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs">
        <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $filter === 'all' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Semua ({{ $allCount }})
        </a>
        <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $filter === 'unread' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Belum Dibaca ({{ $unreadCount }})
        </a>
        <a href="{{ route('admin.notifications.index', ['filter' => 'read']) }}" class="px-3 py-1.5 rounded-lg font-semibold transition-colors {{ $filter === 'read' ? 'bg-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400' }}">
            Dibaca ({{ $readCount }})
        </a>
    </div>

    <!-- Notification Cards List -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="p-4 rounded-2xl border transition-all {{ $notif->is_read ? 'bg-white dark:bg-[#0A1128] border-slate-200 dark:border-slate-800 opacity-75' : 'bg-cyan-500/5 dark:bg-[#0A1128] border-cyan-500/30 ring-1 ring-cyan-500/10' }} shadow-sm flex items-start gap-4">
                <!-- Icon -->
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $notif->severity === 'DANGER' ? 'bg-rose-500/10 text-rose-500 border border-rose-500/20' : ($notif->severity === 'WARNING' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-cyan-500/10 text-cyan-500 border border-cyan-500/20') }}">
                    @if($notif->type === 'DELAY_ALERT')
                        <i data-lucide="clock-alert" class="w-5 h-5"></i>
                    @elseif($notif->type === 'EXCEPTION_ALERT')
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    @else
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white">{{ $notif->title }}</h3>
                            @if(! $notif->is_read)
                                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                            @endif
                        </div>
                        <span class="text-[11px] font-mono text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300">{{ $notif->message }}</p>

                    <div class="flex items-center gap-4 mt-3 text-[11px]">
                        @if($notif->shipment)
                            <a href="{{ route('admin.shipments.show', $notif->shipment_id) }}" class="font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                                Periksa Kargo {{ $notif->shipment->shipment_number }}
                            </a>
                        @endif

                        @if(! $notif->is_read)
                            <form action="{{ route('admin.notifications.mark-read', $notif) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    Tandai telah dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-400">
                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-slate-400"></i>
                <p class="text-xs">Tidak ada notifikasi dalam kategori ini.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

