<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForcedPasswordController extends Controller
{
    public function showForm()
    {
        return view('Auth.change-password');
    }

  public function update(Request $request)
{
    $request->validate([
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = Auth::user();
    /** @var \App\Models\User $user */
    $user->update([
        'password' => Hash::make($request->password),
        'doit_changer_mdp' => false,  // ← LA CORRECTION ESSENTIELLE
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
    }

    return redirect()->intended(route('dashboard'));
}
}