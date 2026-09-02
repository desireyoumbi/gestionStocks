<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'reference',
        'selling_price',
        'purchase_price',
        'alert_threshold',
        'quantity',
        'category_id',
        'user_id',
        'updated_by',
        'boutique_id',
        'status',
        'description',
        'image',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'alert_threshold' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function movements()
    {
        return $this->belongsToMany(Movement::class, 'movement_products')
            ->withPivot('qty','unit_price')
            ->withTimestamps();
    }

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_order_products')
            ->withPivot('qty', 'unit_price')
            ->withTimestamps();
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    public function stockMovemnts()
    {
        return $this->hasMany(StockMovement::class);
    }
}
