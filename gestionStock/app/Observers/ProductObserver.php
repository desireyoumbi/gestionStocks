<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Notification;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
       // Si la quantité est à 0 ou en dessous du seuil d'alerte
        if ($product->quantity <= ($product->alert_threshold ?? 0)) {
            
            // Vérifie d'abord si une alerte non lue existe déjà pour ce produit en cherchant son nom dans le message
            $existingAlert = Notification::where('boutique_id', $product->boutique_id)
                ->whereNull('read_at')
                ->where('message', 'like', "%{$product->name}%")
                ->exists();

            // Si aucune alerte active n'existe, on en crée une nouvelle
            if (!$existingAlert) {
                Notification::create([
                    'boutique_id' => $product->boutique_id,
                    'user_id'     => $product->user_id,
                    'title'       => 'Stock critique',
                    'message'     => "Le produit {$product->name} a atteint son seuil d'alerte (Stock actuel : {$product->quantity})",
                    'read_at'     => null,
                ]);
            }
        }
    }
}