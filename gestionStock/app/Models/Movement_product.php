<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement_product extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_id',
        'product_id',
        'qty'
    ];

    public $casts = [
        'qty' => 'interger'
    ];

    
}
