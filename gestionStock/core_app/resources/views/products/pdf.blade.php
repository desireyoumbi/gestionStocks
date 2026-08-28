    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Liste des Produits - Inventaire</title>
        <style>
            body {
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                color: #333;
                font-size: 12px;
                line-height: 1.4;
                margin: 0;
                padding: 20px;
            }
            .header {
                width: 100%;
                border-bottom: 2px solid #f59e0b;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .header table {
                width: 100%;
                border: none;
                margin: 0;
            }
            .header td {
                border: none;
                padding: 0;
            }
            .title h1 {
                font-size: 20px;
                color: #111;
                margin: 0;
                text-transform: uppercase;
            }
            .title p {
                font-size: 10px;
                color: #666;
                margin: 3px 0 0 0;
            }
            .date {
                text-align: right;
                font-size: 10px;
                color: #666;
            }
            table.content-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .content-table th {
                background-color: #f9fafb;
                color: #374151;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 9px;
                letter-spacing: 0.05em;
                padding: 10px 8px;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
            }
            .content-table td {
                padding: 10px 8px;
                border-bottom: 1px solid #f3f4f6;
                color: #4b5563;
            }
            .content-table tr:nth-child(even) {
                background-color: #fafafa;
            }
            .text-right {
                text-align: right;
            }
            .badge {
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 9px;
                font-weight: bold;
            }
            .badge-success {
                background-color: #d1fae5;
                color: #065f46;
            }
            .badge-danger {
                background-color: #fee2e2;
                color: #991b1b;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 9px;
                color: #9ca3af;
                border-top: 1px solid #e5e7eb;
                padding-top: 10px;
            }
        </style>
    </head>
    <body>

        <!-- En-tête du document (Utilisation d'un tableau HTML interne pour un rendu PDF stable sans flexbox) -->
        <div class="header">
            <table>
                <tr>
                    <td class="title">
                        <h1>Catalogue des Produits</h1>
                        <p>Rapport d'inventaire de la boutique</p>
                    </td>
                    <td class="date">
                        <strong>Date :</strong> {{ date('d/m/Y à H:i') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tableau principal -->
        <table class="content-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Nom du Produit</th>
                    <th>Catégorie</th>
                    <th>Prix Vente</th>
                    @if(Auth::user()->isManager())
                        <th>Prix Achat</th>
                    @endif
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->reference ?? 'N/A' }}</td>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ number_format($product->selling_price, 0, ',', ' ') }} FCFA</td>
                    
                    @if(Auth::user()->isManager())
                        <td>{{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</td>
                    @endif
                    
                    <td class="text-right">
                        <span class="badge {{ $product->quantity <= $product->alert_threshold ? 'badge-danger' : 'badge-success' }}">
                            {{ $product->quantity }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #9ca3af;">
                        Aucun produit enregistré pour le moment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="footer">
            Généré par le système de gestion de stock — Page 1
        </div>

    </body>
    </html>