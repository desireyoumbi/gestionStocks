<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use Illuminate\Http\Request;

class PlatformAdminController extends Controller
{
    public function dashboard()
    {
        $boutiques = Boutique::withCount('users')->orderByDesc('created_at')->get();

        return view('platform-admin.dashboard', [
            'boutiques' => $boutiques,
            'totalBoutiques' => $boutiques->count(),
            'totalActives' => $boutiques->where('statut', 'active')->count(),
            'totalSuspendues' => $boutiques->where('statut', 'suspendue')->count(),
        ]);
    }

    public function suspendre(Boutique $boutique)
    {
        $boutique->update(['statut' => 'suspendue']);

        return back()->with('success', "Boutique « {$boutique->nom} » suspendue.");
    }

    public function reactiver(Boutique $boutique)
    {
        $boutique->update(['statut' => 'active']);

        return back()->with('success', "Boutique « {$boutique->nom} » réactivée.");
    }
}