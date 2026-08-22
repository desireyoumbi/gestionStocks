<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boutique extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function stockMovemnts()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

}