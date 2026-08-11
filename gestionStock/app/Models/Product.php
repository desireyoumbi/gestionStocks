<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'selling_price',
        'purchase_price',
        'alert_threshold',
        'quantity',
        'photo_lieu',
        'category_id',
        'user_id',
        'updated_by'
    ];

    public $casts = [
        'update_by' => 'interger',
        'quantity' => 'integer',
        'alert_threshold' => 'integer',
        'purchase_price' =>'decimal',
        'selling_price' => 'decimal'
    ];

    //relation avec les tables
     public function movements(){
        return $this->hasMany(Movement::class);
     }

     public function categories(){
        return $this->hasMany(Category::class);
     }
     public function users(){
        return $this->hasMany(User::class);
     }
     public function purchase_orders(){
        return $this->hasMany(Purchase_order::class);
     }
}
