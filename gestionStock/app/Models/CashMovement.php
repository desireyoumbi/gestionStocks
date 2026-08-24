<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'boutique_id',
        'user_id',
        'type',
        'amount',
        'category',
        'motif',
        'is_unjustified',
    ];

    // Relation avec la boutique
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    // Relation avec l'utilisateur qui a fait l'action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}