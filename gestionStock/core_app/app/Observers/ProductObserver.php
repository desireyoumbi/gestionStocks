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
        $this->checkStock($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->checkStock($product);
    }

    /**
     * Logique centralisée pour créer ou nettoyer l'alerte selon le stock réel.
     */
    protected function checkStock(Product $product): void
    {
        $threshold = $product->alert_threshold ?? 0;

        // Si la quantité est en dessous ou égale au seuil
        if ($product->quantity <= $threshold) {
            
            // Vérifie si une alerte non lue existe déjà pour ce produit
            $existingAlert = Notification::where('boutique_id', $product->boutique_id)
                ->whereNull('read_at')
                ->where('message', 'like', "%{$product->name}%")
                ->exists();

            // Si aucune alerte active n'existe, on en crée une
            if (!$existingAlert) {
                Notification::create([
                    'boutique_id' => $product->boutique_id,
                    'user_id'     => $product->user_id ?? null,
                    'title'       => 'Stock critique',
                    'message'     => "Le produit {$product->name} a atteint son seuil d'alerte (Stock actuel : {$product->quantity})",
                    'read_at'     => null,
                ]);
            }
        } else {
            // Si le stock est remonté au-dessus du seuil (ex: 7 pour un seuil de 5), 
            // on supprime ou désactive l'ancienne alerte pour synchroniser la cloche !
            Notification::where('boutique_id', $product->boutique_id)
                ->whereNull('read_at')
                ->where('message', 'like', "%{$product->name}%")
                ->delete();
        }
    }
}