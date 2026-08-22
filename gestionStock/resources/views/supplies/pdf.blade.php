<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Approvisionnements</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; color: #111; text-transform: uppercase; }
        .header p { font-size: 10px; color: #666; margin: 5px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; font-size: 11px; text-transform: uppercase; color: #555; }
        td { font-size: 11px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-box { margin-top: 20px; float: right; width: 250px; border: 1px solid #ccc; padding: 10px; background: #fdfdfd; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rapport des Approvisionnements</h1>
        <p>{{ $title }} | Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date / Heure</th>
                <th>Fournisseur</th>
                <th>Produit</th>
                <th class="text-right">Qté</th>
                <th class="text-right">P.U. Achat</th>
                <th class="text-right">Total</th>
                <th>Motif</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($supplies as $supply)
                @foreach($supply->products as $product)
                    @php 
                        $lineTotal = $product->pivot->qty * $product->purchase_price;
                        $grandTotal += $lineTotal;
                    @endphp
                    <tr>
                        <td>{{ $supply->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $supply->purchaseOrder->supplier->name ?? 'Direct / Aucun' }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="text-right font-bold">+{{ $product->pivot->qty }}</td>
                        <td class="text-right">{{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right font-bold">{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $supply->motif ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #777;">Aucun approvisionnement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        <strong>Montant Total Général :</strong> 
        <span style="float: right; font-weight: bold; color: #059669;">
            {{ number_format($grandTotal, 0, ',', ' ') }} FCFA
        </span>
    </div>

</body>
</html>