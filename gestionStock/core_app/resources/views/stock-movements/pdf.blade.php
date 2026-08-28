<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal des Mouvements de Stock</title>
    <style>
        body {
            font-family: 'Helvetica, Arial, sans-serif';
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #111;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 10px;
        }
        .meta-info {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #555;
        }
        .week-block {
            margin-bottom: 20px;
        }
        .week-title {
            background-color: #f3f4f6;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #1f2937;
            border-left: 3px solid #d97706; /* Couleur ambre */
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            border-bottom: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            color: #4b5563;
        }
        .text-emerald {
            color: #059669;
            font-weight: bold;
        }
        .text-red {
            color: #dc2626;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-entry { background-color: #d1fae5; color: #065f46; }
        .badge-sale { background-color: #dbeafe; color: #1e40af; }
        .badge-other { background-color: #fef3c7; color: #92400e; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- En-tête du document -->
    <div class="header">
        <div class="meta-info">
            Date d'édition : {{ date('d/m/Y H:i') }}<br>
            Généré par : {{ Auth::user()->name ?? 'Système' }}
        </div>
        <h1>Journal des Mouvements</h1>
        <p>Traçabilité complète des entrées et sorties de stock</p>
    </div>

    <!-- Contenu groupé par semaine -->
    @forelse($groupedByWeek as $weekTitle => $weekMovements)
        <div class="week-block">
            <div class="week-title">
                {{ $weekTitle }} ({{ $weekMovements->count() }} mouvement(s))
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Date / Heure</th>
                        <th style="width: 15%;">Utilisateur</th>
                        <th style="width: 22%;">Produit</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 10%;">Qté</th>
                        <th style="width: 12%;">Avant ➔ Après</th>
                        <th style="width: 14%;">Motif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weekMovements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $movement->user->name ?? 'Inconnu' }}</strong></td>
                        <td>{{ $movement->product->name ?? 'Produit supprimé' }}</td>
                        <td>
                            @if($movement->type == 'entry')
                                <span class="badge badge-entry">Entrée</span>
                            @elseif($movement->type == 'sale')
                                <span class="badge badge-sale">Vente</span>
                            @else
                                <span class="badge badge-other">{{ ucfirst($movement->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $movement->type == 'entry' || ($movement->type == 'adjustment' && $movement->quantity > 0) ? 'text-emerald' : 'text-red' }}">
                                {{ $movement->type == 'entry' || ($movement->type == 'adjustment' && $movement->quantity > 0) ? '+' . $movement->quantity : '-' . abs($movement->quantity) }}
                            </span>
                        </td>
                        <td><code>{{ $movement->old_stock }} ➔ {{ $movement->new_stock }}</code></td>
                        <td><em>{{ $movement->reason ?? '-' }}</em></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p style="text-align: center; color: #666; margin-top: 40px;">Aucun mouvement enregistré pour le moment.</p>
    @endforelse

    <!-- Pied de page -->
    <div class="footer">
        e-Stocky - Gestion de stock et inventaire &copy; {{ date('Y') }}
    </div>

</body>
</html>