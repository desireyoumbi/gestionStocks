<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche le centre de notifications de la boutique.
     */
    public function index()
    {
        $boutiqueId = Auth::user()->boutique_id;

        // Supprimer automatiquement les notifications lues de la boutique qui datent de plus d'un jour
        Notification::where('boutique_id', $boutiqueId)
            ->whereNotNull('read_at')
            ->where('created_at', '<', now()->subDay())
            ->delete();
            
        // Récupérer les notifications de la boutique avec pagination
        $notifications = Notification::where('boutique_id', $boutiqueId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalNotifications = Notification::where('boutique_id', $boutiqueId)->count();

        // Messages non lus pour la boutique
        $unreadMessages = Notification::where('boutique_id', $boutiqueId)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'totalNotifications', 'unreadMessages'));
    }

    /**
     * Marquer une notification spécifique comme lue.
     */
    public function markAsRead($id)
    {
        // On s'assure que la notification appartient bien à la boutique de l'utilisateur
        $notification = Notification::where('boutique_id', Auth::user()->boutique_id)->findOrFail($id);

        $notification->update([
            'read_at' => now()
        ]);

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications de la boutique comme lues.
     */
    public function markAllAsRead()
    {
        Notification::where('boutique_id', Auth::user()->boutique_id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return back()->with('success', 'Toutes les notifications ont été lues.');
    }
}