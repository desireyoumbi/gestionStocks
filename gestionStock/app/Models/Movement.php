<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'boutique_id',
        'type',
        'date',
        'motif',
        'user_id',
        'purchase_order_id',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
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

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'movement_products')
                    ->withPivot('qty')
                    ->withTimestamps();
    }
}