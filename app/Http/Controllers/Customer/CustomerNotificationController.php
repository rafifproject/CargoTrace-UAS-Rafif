<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerNotificationController extends Controller
{
    /**
     * Mockup 12: Pusat Notifikasi Customer
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $myShipmentIds = $user->shipments()->pluck('id');
        $tab = $request->input('tab', 'all');

        $query = Notification::where(function ($q) use ($user, $myShipmentIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('shipment_id', $myShipmentIds);
        })->orderBy('created_at', 'desc');

        if ($tab === 'unread') {
            $query->where('is_read', false);
        } elseif ($tab === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = Notification::where(function ($q) use ($user, $myShipmentIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('shipment_id', $myShipmentIds);
        })->where('is_read', false)->count();

        return view('customer.notifications.index', compact('notifications', 'unreadCount', 'tab'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        $user = $request->user();
        $myShipmentIds = $user->shipments()->pluck('id')->toArray();

        // Anti-IDOR check on notification
        if ($notification->user_id !== $user->id && ! in_array($notification->shipment_id, $myShipmentIds)) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('status', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $user = $request->user();
        $myShipmentIds = $user->shipments()->pluck('id');

        Notification::where(function ($q) use ($user, $myShipmentIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('shipment_id', $myShipmentIds);
        })->where('is_read', false)->update(['is_read' => true]);

        return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}

