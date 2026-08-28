<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_movements', function (Blueprint $table) {
            // On passe à 20 chiffres au total dont 2 pour les centimes (supporte jusqu'à 99 999 999 999 999 99,99)
            $table->decimal('amount', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_movements', function (Blueprint $table) {
            // Retour en arrière si besoin (remets l'ancienne taille si c'était 12 par exemple)
            $table->decimal('amount', 12, 2)->change();
        });
    }
};