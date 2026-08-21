<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'boutique_id',
        'user_id',
        'title',
        'message',
        'type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Relation avec la boutique
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    // Relation avec l'utilisateur (si la notification lui est dédiée)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
