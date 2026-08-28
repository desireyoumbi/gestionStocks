<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des fournisseurs</title>
    <style>
        /* Ajoute ici tes styles de base pour le PDF (tableau, polices, etc.) */
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 10px; background: #fef3c7; color: #92400e; border-radius: 4px; margin-right: 2px; }
    </style>
</head>
<body>
    <h2>Liste des fournisseurs</h2>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Produits livrés</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Adresse</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                <tr>
                    <td><strong>{{ $supplier->name }}</strong></td>
                    
                    {{-- Affichage des produits livrés --}}
                    <td>
                        @foreach($supplier->products as $product)
                            <span class="badge">{{ $product->name }}</span>
                        @endforeach
                        @if($supplier->products->isEmpty())
                            <em>Aucun produit</em>
                        @endif
                    </td>

                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->email ?? '—' }}</td>
                    <td>{{ $supplier->adresse ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>