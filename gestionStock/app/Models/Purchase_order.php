<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase_order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'supplier_id',
        'user_id',
        'update_by'
    ];

    public function movements(){
        return $this->belongsTo(Movement::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function suppliers(){
        return $this->belongsTo(Supplier::class);
    }
}
