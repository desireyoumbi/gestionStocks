<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Ventes</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .total-row { font-weight: bold; background-color: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div style="text-align: center; font-size: 18px; font-weight: bold;">Rapport des Ventes</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Produit</th>
                <th>Qté</th>
                <th>Prix Unit.</th>
                <th>Total Ligne</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                @foreach($sale->products as $product)
                    @php 
                        $prixUnitaire = $product->price ?? 0; 
                        $quantite = $product->pivot->qty ?? 0;
                        $totalLigne = $quantite * $prixUnitaire;
                    @endphp
                    <tr>
                        <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $quantite }}</td>
                        <td>{{ number_format($prixUnitaire, 0, ',', ' ') }}</td>
                        <td>{{ number_format($totalLigne, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL GÉNÉRAL :</td>
                <td>{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>