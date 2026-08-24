<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $role
 * @method bool isManager()
 * @method bool isAdmin()
 */


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'statuts',
        'role',
        'boutique_id',
        'doit_changer_mdp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'doit_changer_mdp' => 'boolean',
        'password' => 'hashed',
    ];

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Vérifie si l'utilisateur est un Administrateur ou un Vice-Administrateur.
     */
    public function isManager()
    {
        return in_array($this->role, ['administrateur', 'vice_administrateur']);
    }

    /**
     * Vérifie si l'utilisateur est un Administrateur principal.
     */
    public function isAdmin()
    {
        return $this->role === 'administrateur';
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at')->latest();
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
}
