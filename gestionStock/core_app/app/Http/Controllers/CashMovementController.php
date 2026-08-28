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
            'amount' => 'required|numeric|min:0|max:999999999999999999.99',
            'motif' => 'nullable|string|max:255',
        ], [
            'amount.max' => 'Le montant saisi est trop élevé pour la capacité de la caisse (maximum 10 milliards).',
            'amount.numeric' => 'Le montant doit être un nombre valide.',
            'amount.min' => 'Le montant ne peut pas être négatif.',
        ]);

        $user = Auth::user();

        // Sécurité : Vérifier que l'utilisateur a bien une boutique assignée
        if (!$user->boutique_id) {
            return redirect()->back()->with('error', 'Aucune boutique associée à votre compte.');
        }

        // On enregistre le mouvement de fonds (Ouverture / Ajustement de capital)
        CashMovement::create([
            'boutique_id' => $user->boutique_id,
            'user_id' => $user->id,
            'type' => 'in', // C'est une entrée d'argent dans la structure de caisse
            'category' => 'initial', // Catégorie fonds initial ou capital
            'amount' => $request->amount,
            'motif' => $request->filled('motif') ? $request->motif : 'Initialisation ou ajustement du fonds de caisse',
            'is_unjustified' => false,
        ]);

        return redirect()->back()->with('success', 'Fonds de caisse mis à jour avec succès.');
    }
}