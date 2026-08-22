<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Utilisateurs</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h2>Liste des utilisateurs de la boutique</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->phone }}</td>
                <td>{{ $user->email ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>