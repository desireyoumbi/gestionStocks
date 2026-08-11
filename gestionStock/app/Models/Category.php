<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'updated_by',
    ];
    public $casts = [
        'updated_by' => 'integer',
    ];
    public function users(){
        return $this->belongsTo(User::class);
    }
  
}
