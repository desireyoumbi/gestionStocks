<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'supplier_id',
        'user_id',
        'updated_by',
        'boutique_id',
    ];

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movement()
    {
        return $this->hasOne(Movement::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_order_products')
                    ->withPivot('qty', 'unit_price')
                    ->withTimestamps();
    }
}