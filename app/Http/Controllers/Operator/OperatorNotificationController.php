<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorNotificationController extends Controller
{
    /** Mockup 15: Pusat Notifikasi */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'all');

        $query = Notification::orderBy('created_at', 'desc');

        if ($tab === 'unread') {
            $query->where('is_read', false);
        } elseif ($tab === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount   = Notification::where('is_read', false)->count();

        return view('operator.notifications.index', compact('notifications', 'unreadCount', 'tab'));
    }

    /** Tandai satu notifikasi sebagai sudah dibaca */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);

        return back()->with('status', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /** Tandai semua notifikasi sebagai sudah dibaca */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}

