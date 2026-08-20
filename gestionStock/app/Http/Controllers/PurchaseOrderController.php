<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = PurchaseOrder::where('boutique_id', Auth::user()->boutique_id)
            ->with(['supplier', 'user']);

        if ($status) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->latest()->paginate(10)->appends(['status' => $status]);

        return view('purchase_orders.index', compact('purchaseOrders', 'status'));
    }

    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer cet élément.");
        }

        $suppliers = Supplier::where('boutique_id', Auth::user()->boutique_id)->get();
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        $supplier = Supplier::where('boutique_id', Auth::user()->boutique_id)
            ->findOrFail($validated['supplier_id']);

        DB::transaction(function () use ($validated, $supplier) {
            $purchaseOrder = PurchaseOrder::create([
                'boutique_id' => Auth::user()->boutique_id,
                'supplier_id' => $supplier->id,
                'user_id' => Auth::id(),
                'status' => 'en_attente',
            ]);

            foreach ($validated['products'] as $line) {
                $purchaseOrder->products()->attach($line['id'], [
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase_orders.index')->with('success', 'Enregistré avec succès.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Élément introuvable.");
        }

        $purchaseOrder->load(['supplier', 'user', 'products']);

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Élément introuvable.");
        }

        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Action non autorisée.");
        }

        $purchaseOrder->products()->detach();
        $purchaseOrder->delete();

        return redirect()->route('purchase_orders.index')->with('success', 'Supprimé avec succès.');
    }

    /**
     * Valide une commande fournisseur : génère le mouvement d'entrée et met à jour les stocks.
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        // 1. Sécurités de base (Appartenance à la boutique et rôles)
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à valider cette commande.");
        }

        if ($purchaseOrder->status !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette commande a déjà été traitée.');
        }

        // 2. Transaction pour garantir l'intégrité
        DB::transaction(function () use ($purchaseOrder) {
            // Charger les produits liés via la table pivot
            $purchaseOrder->load('products');

            // Étape A : Créer le mouvement de stock d'entrée
            $movement = Movement::create([
                'boutique_id' => $purchaseOrder->boutique_id,
                'type'        => 'entree',
                'date'        => now(),
                'motif'       => 'Validation commande fournisseur #' . $purchaseOrder->id,
                'user_id'     => Auth::id(),
            ]);

            // Étape B : Parcourir les produits de la commande pour alimenter le mouvement et le stock
            foreach ($purchaseOrder->products as $product) {
                // Associer le produit au mouvement dans la table pivot des mouvements
                $movement->products()->attach($product->id, [
                    'qty' => $product->pivot->qty,
                ]);

                // Étape C : Mettre à jour le stock réel du produit (utilisation de 'quantity')
                $product->increment('quantity', $product->pivot->qty);
            }

            // Étape D : Mettre à jour le statut de la commande
            $purchaseOrder->update([
                'status'     => 'recue',
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Commande validée avec succès : le stock a été mis à jour et le mouvement d\'entrée a été généré.');
    }
}