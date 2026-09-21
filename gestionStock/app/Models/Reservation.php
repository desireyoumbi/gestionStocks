<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id',
        'boutique_id',
        'code',
        'status',
        'expires_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function boutique(): BelongsTo
    {
        return $this->belongsTo(Boutique::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'reservation_items')
            ->withPivot([
                'quantity',
                'unit_price',
            ])
            ->withTimestamps();
    }

    public function movement(): HasOne
    {
        return $this->hasOne(Movement::class);
    }
}