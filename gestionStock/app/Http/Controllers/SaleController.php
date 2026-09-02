<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\StockMovement;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Affiche la liste des ventes effectuées.
     */
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        // 1. La requête principale (pour la liste paginée)
        $query = Movement::with(['products', 'user'])
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sortie');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // On pagine les résultats
        $sales = $query->latest()->paginate(15);

        // 2. GROUPEMENT PAR DATE (pour l'affichage)
        $groupedSales = collect($sales->items())->groupBy(function($sale) {
            return $sale->created_at->format('Y-m-d');
        });

        // 3. CALCULS POUR LES CARTES STATISTIQUES
        // On exclut systématiquement les ventes annulées (annule_at non nul) de tous les totaux.
        $baseVentesQuery = Movement::where('boutique_id', $boutiqueId)
            ->where('type', 'sortie')
            ->whereNull('annule_at');

        $totalVentesCount = (clone $baseVentesQuery)->count();

        $totalArticlesVendus = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->sum(DB::raw('ABS(qty)')) ?? 0;

        // Correction du calcul du CA en allant chercher le prix unitaire fixé dans la table pivot (movement_products)
        $chiffreAffairesTotal = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->sum(DB::raw('ABS(movement_products.qty) * movement_products.unit_price')) ?? 0;

        $panierMoyen = $totalVentesCount > 0 ? number_format($chiffreAffairesTotal / $totalVentesCount, 0, ',', ' ') . ' FCFA' : '0 FCFA';

        $produitPhareData = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->join('products', 'movement_products.product_id', '=', 'products.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->select('products.name', DB::raw('SUM(ABS(movement_products.qty)) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();

        $produitPhare = $produitPhareData ? $produitPhareData->name : 'Aucun';

        return view('sales.index', compact(
            'sales',
            'groupedSales',
            'panierMoyen',
            'totalArticlesVendus',
            'produitPhare'
        ));
    }

    /** 
     * Affiche le formulaire de création d'une vente.
     */
    public function create()
    {
        $boutiqueId = Auth::user()->boutique_id;
        $products = Product::where('boutique_id', $boutiqueId)->get();
        
        return view('sales.create', compact('products'));
    }

    /**
     * Enregistre la vente, met à jour le stock, crédite la caisse et redirige vers l'index.
     */
    public function store(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $request->validate([
            'motif' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', $boutiqueId),
            ],
            'items.*.quantity' => 'required|integer|min:1|max:2147483647',
        ], [
            'items.*.product_id.exists' => 'Un ou plusieurs produits sélectionnés sont invalides.',
            'items.*.quantity.max' => 'La quantité demandée est trop élevée.',
        ]);

        try {
            DB::transaction(function () use ($request, $boutiqueId) {
                $user = Auth::user();
                $totalSaleAmount = 0;

                // 1. Créer le mouvement global de vente
                $movement = Movement::create([
                    'boutique_id' => $boutiqueId,
                    'type' => 'sortie',
                    'date' => now(),
                    'user_id' => $user->id,
                    'motif' => $request->motif ?? 'Vente',
                ]);

                // 2. Parcourir chaque produit du panier
                foreach ($request->items as $item) {
                    $product = Product::where('boutique_id', $boutiqueId)
                        ->where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit : {$product->name}. Stock actuel : {$product->quantity}");
                    }

                    $oldStock = $product->quantity;
                    $quantitySold = $item['quantity'];
                    $newStock = $oldStock - $quantitySold;
                    
                    // Utilisation et fixation du prix de vente actuel dans l'historique de la vente
                    $unitPrice = (float) $product->selling_price;
                    $lineTotal = $quantitySold * $unitPrice;
                    $totalSaleAmount += $lineTotal;

                    // Attacher dans la table pivot avec stockage du prix unitaire figé
                    $movement->products()->attach($product->id, [
                        'qty' => $quantitySold,
                        'unit_price' => $unitPrice
                    ]);

                    // Décrémenter le stock et mettre à jour le statut
                    $product->update([
                        'quantity' => $newStock,
                        'status' => $newStock > 0 ? 'disponible' : 'rupture'
                    ]);

                    // Historique global des stocks
                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id'  => $product->id,
                        'user_id'     => $user->id,
                        'type'        => 'sale',
                        'quantity'    => $quantitySold,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => $request->motif ?? 'Vente de produits',
                    ]);
                }

                // 3. Enregistrer l'entrée dans la caisse
                CashMovement::create([
                    'boutique_id' => $boutiqueId,
                    'movement_id' => $movement->id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'category' => 'sale',
                    'amount' => $totalSaleAmount,
                    'motif' => $request->motif ?? 'Encaissement vente #' . $movement->id,
                    'is_unjustified' => false,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('sales.index')
                         ->with('success', 'Vente et encaissement enregistrés avec succès.');
    }

    /**
     * Annule une vente : restitue le stock, supprime l'entrée de caisse liée,
     * et marque le mouvement comme annulé (annule_at).
     */
    public function destroy($id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        DB::transaction(function () use ($id, $boutiqueId) {
            $sale = Movement::where('boutique_id', $boutiqueId)
                ->where('type', 'sortie')
                ->findOrFail($id);

            if ($sale->annule_at) {
                return;
            }

            foreach ($sale->products as $product) {
                $productToUpdate = Product::where('id', $product->id)
                    ->where('boutique_id', $boutiqueId)
                    ->lockForUpdate()
                    ->first();
                
                if ($productToUpdate) {
                    $pivotQty = $product->pivot->qty ?? ($product->pivot->quantity ?? 1);
                    $oldStock = $productToUpdate->quantity;
                    $newStock = $oldStock + $pivotQty;

                    $productToUpdate->update([
                        'quantity' => $newStock,
                        'status' => 'disponible'
                    ]);

                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id'  => $productToUpdate->id,
                        'user_id'     => Auth::id(),
                        'type'        => 'adjustment',
                        'quantity'    => $pivotQty,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => 'Annulation de la vente #' . $sale->id,
                    ]);
                }
            }

            CashMovement::where('boutique_id', $boutiqueId)
                ->where('movement_id', $sale->id)
                ->delete();

            $sale->update(['annule_at' => now()]);
        });

        return back()->with('success', 'Vente annulée, stock restitué et caisse mise à jour avec succès.');
    }

    public function exportPdf()
    {
        $boutiqueId = Auth::user()->boutique_id; 
        
        $sales = Movement::where('boutique_id', $boutiqueId)
            ->where('type', 'sortie')
            ->whereNull('annule_at')
            ->with(['products', 'user'])
            ->latest()
            ->get();

        $totalGeneral = $sales->sum(function ($sale) {
            return $sale->products->sum(function ($product) {
                $qte = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                // Utilisation du prix unitaire figé dans le pivot
                $prix = $product->pivot->unit_price ?? ($product->selling_price ?? 0);
                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView('sales.pdf-global', compact('sales', 'totalGeneral'));
        return $pdf->download('toutes-les-ventes-' . date('Y-m-d') . '.pdf');
    }

    public function exportPdfByDate($date)
    {
        $boutiqueId = Auth::user()->boutique_id;
        
        $sales = Movement::where('boutique_id', $boutiqueId)
            ->where('type', 'sortie')
            ->whereNull('annule_at')
            ->whereDate('created_at', $date)
            ->with(['products', 'user'])
            ->get();

        $totalGeneral = $sales->sum(function ($sale) {
            return $sale->products->sum(function ($product) {
                $qte = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                // Utilisation du prix unitaire figé dans le pivot
                $prix = $product->pivot->unit_price ?? ($product->selling_price ?? 0);
                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView('sales.pdf-date', compact('sales', 'date', 'totalGeneral'));
        return $pdf->download('ventes-' . $date . '.pdf');
    }
}