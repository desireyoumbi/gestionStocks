<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'date',
        'motif',
        'user_id',
        'purchase_order_id',
        'update_by'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }
    public function purchase_orders(){
        return $this->belongsTo(Purchase_order::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
