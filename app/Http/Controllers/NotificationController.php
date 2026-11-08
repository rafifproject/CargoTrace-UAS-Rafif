<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->input('filter', 'all');

        $query = Notification::with('shipment');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $unreadCount = Notification::where('is_read', false)->count();
        $readCount = Notification::where('is_read', true)->count();
        $allCount = Notification::count();

        return view('admin.notifications.index', compact(
            'notifications',
            'filter',
            'unreadCount',
            'readCount',
            'allCount'
        ));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('status', 'Notifikasi telah ditandai dibaca.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}

