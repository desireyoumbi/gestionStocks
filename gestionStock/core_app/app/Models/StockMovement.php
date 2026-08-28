<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'boutique_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'reason',
    ];

    // Relation avec la boutique
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    // Relation avec le produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}