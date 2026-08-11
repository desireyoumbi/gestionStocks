<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'addresse',
        'users_id',
        'updated_by',
    ];

    public $casts = [
        'update_by' => 'integer'
    ];
    
    public function users(){
        return $this->belongsTo(User::class);

    }

    public function purchase_order(){
        return $this->hasMany(Purchase_order::class);
    }
}
