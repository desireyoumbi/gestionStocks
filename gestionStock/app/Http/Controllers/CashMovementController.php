<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashMovementController extends Controller
{
    // Enregistrer ou ajuster le fonds initial / capital de la boutique
    public function storeFund(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'motif' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $boutiqueId = $user->boutique_id;

        // On enregistre le mouvement de fonds (Ouverture / Ajustement de capital)
        CashMovement::create([
            'boutique_id' => $boutiqueId,
            'user_id' => $user->id,
            'type' => 'in', // C'est une entrée d'argent dans la structure de caisse
            'category' => 'initial', // Catégorie fonds initial ou capital
            'amount' => $request->amount,
            'motif' => $request->motif ?? 'Initialisation ou ajustement du fonds de caisse',
            'is_unjustified' => false,
        ]);

        return redirect()->back()->with('success', 'Fonds de caisse mis à jour avec succès.');
    }
}