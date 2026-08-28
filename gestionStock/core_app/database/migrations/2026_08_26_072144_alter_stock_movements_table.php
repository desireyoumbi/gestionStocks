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
        Schema::table('stock_movements', function (Blueprint $table) {
            // 1. Supprimer les anciennes contraintes de clés étrangères
            // (Laravel nomme généralement la contrainte : nom_de_table_nom_colonne_foreign)
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);

            // 2. Recréer les clés étrangères avec restrictOnDelete()
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Pour revenir en arrière si besoin (remet en cascade)
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};