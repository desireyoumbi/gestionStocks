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
        'logo',
        'primary_color',
        'statut',
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

    // Dans le modèle Boutique.php
    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class);
    }

    // argent en caisse
    public function getCashBalance()
    {
        $totalIn = $this->cashMovements()->where('type', 'in')->sum('amount');
        $totalOut = $this->cashMovements()->where('type', 'out')->sum('amount');

        return $totalIn - $totalOut;
    }
}
