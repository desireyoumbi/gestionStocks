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
}
