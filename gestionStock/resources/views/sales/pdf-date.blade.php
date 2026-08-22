<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ventes du {{ $date }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #f4f4f4; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="title">Rapport des Ventes du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>
    <table>
        <thead>
            <tr>
                <th>Heure</th>
                <th>Utilisateur</th>
                <th>Produit</th>
                <th>Qté</th>
                <th>Prix Unit.</th>
                <th>Total Ligne</th>
                <th>Motif</th>
            </tr>
        </thead>
        <tbody>
            @php $totalJour = 0; @endphp
            @foreach($sales as $movement)
                @if($movement->products && $movement->products->count() > 0)
                    @foreach($movement->products as $product)
                        @php 
                            $prixUnitaire = $product->selling_price ?? 0; 
                            $quantite = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                            $totalLigne = $quantite * $prixUnitaire;
                            $totalJour += $totalLigne;
                        @endphp
                        <tr>
                            <td>{{ $movement->created_at->format('H:i') }}</td>
                            <td>{{ $movement->user->name ?? 'N/A' }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $quantite }}</td>
                            <td>{{ number_format($prixUnitaire, 0, ',', ' ') }}</td>
                            <td>{{ number_format($totalLigne, 0, ',', ' ') }}</td>
                            <td>{{ $movement->motif }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL GÉNÉRAL :</td>
                <td colspan="2">{{ number_format($totalGeneral ?? $totalJour, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>