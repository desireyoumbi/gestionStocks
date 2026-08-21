<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche le centre de notifications.
     */
    public function index()
    {
        $userId = Auth::id();

        // Récupérer les notifications de l'utilisateur connecté avec pagination
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalNotifications = Notification::where('user_id', $userId)->count();
        
        // Messages non lus (basé sur read_at qui est null)
        $unreadMessages = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'totalNotifications', 'unreadMessages'));
    }

    /**
     * Marquer une notification spécifique comme lue.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        
        $notification->update([
            'read_at' => now()
        ]);

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return back()->with('success', 'Toutes les notifications ont été lues.');
    }
}