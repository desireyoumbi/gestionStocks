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
    /**
     * Affiche la liste des commandes fournisseurs de la boutique connectée.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = PurchaseOrder::where('boutique_id', Auth::user()->boutique_id)
            ->with(['supplier', 'user']);

        if ($status) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->latest()->paginate(10)->appends([
            'status' => $status,
        ]);

        return view('purchase_orders.index', compact('purchaseOrders', 'status'));
    }

    /**
     * Affiche le formulaire de création d'une commande.
     */
    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer une commande.");
        }

        $suppliers = Supplier::where('boutique_id', Auth::user()->boutique_id)->get();
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    /**
     * Enregistre une nouvelle commande fournisseur, avec ses lignes de produits.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer une commande.");
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Vérifie que le fournisseur choisi appartient bien à la boutique de l'utilisateur
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

        return redirect()->route('purchase_orders.index')
                         ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Affiche le détail d'une commande, avec ses produits.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        $purchaseOrder->load(['supplier', 'user', 'products']);

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Affiche le formulaire de modification (uniquement si la commande est encore en attente).
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à modifier cette commande.");
        }

        if ($purchaseOrder->status !== 'en_attente') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Seules les commandes en attente peuvent être modifiées.');
        }

        $suppliers = Supplier::where('boutique_id', Auth::user()->boutique_id)->get();
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();
        $purchaseOrder->load('products');

        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    /**
     * Met à jour une commande encore en attente (fournisseur et lignes de produits).
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à modifier cette commande.");
        }

        if ($purchaseOrder->status !== 'en_attente') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Seules les commandes en attente peuvent être modifiées.');
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

        DB::transaction(function () use ($validated, $supplier, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $supplier->id,
                'updated_by' => Auth::id(),
            ]);

            // On resynchronise entièrement les lignes de produits
            $syncData = [];
            foreach ($validated['products'] as $line) {
                $syncData[$line['id']] = [
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                ];
            }
            $purchaseOrder->products()->sync($syncData);
        });

        return redirect()->route('purchase_orders.show', $purchaseOrder)
                         ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Valide la réception d'une commande : crée le mouvement de stock associé
     * et met à jour la quantité de chaque produit.
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à valider une réception.");
        }

        if ($purchaseOrder->status !== 'en_attente') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Cette commande a déjà été traitée.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->load('products');

            $movement = Movement::create([
                'boutique_id' => $purchaseOrder->boutique_id,
                'type' => 'entree',
                'date' => now(),
                'motif' => 'Réception commande #' . $purchaseOrder->id,
                'user_id' => Auth::id(),
                'purchase_order_id' => $purchaseOrder->id,
            ]);

            foreach ($purchaseOrder->products as $product) {
                $movement->products()->attach($product->id, [
                    'qty' => $product->pivot->qty,
                ]);

                $product->increment('quantity', $product->pivot->qty);

                // Repasse le produit en "disponible" si son stock était à 0
                if ($product->status === 'rupture' && $product->quantity > 0) {
                    $product->update(['status' => 'disponible']);
                }
            }

            $purchaseOrder->update([
                'status' => 'recue',
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('purchase_orders.show', $purchaseOrder)
                         ->with('success', 'Commande reçue, stock mis à jour.');
    }

    /**
     * Annule une commande encore en attente.
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à annuler cette commande.");
        }

        if ($purchaseOrder->status !== 'en_attente') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        $purchaseOrder->update([
            'status' => 'annulee',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('purchase_orders.index')
                         ->with('success', 'Commande annulée.');
    }

    /**
     * Supprime définitivement une commande (réservé aux administrateurs, uniquement si non reçue).
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de supprimer cette commande.");
        }

        if ($purchaseOrder->status === 'recue') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Impossible de supprimer une commande déjà reçue.');
        }

        $purchaseOrder->products()->detach();
        $purchaseOrder->delete();

        return redirect()->route('purchase_orders.index')
                         ->with('success', 'Commande supprimée.');
    }
}