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
        Schema::table('movement_products', function (Blueprint $table) {
            // Modifie la colonne pour qu'elle accepte les nombres négatifs
            $table->integer('qty')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movement_products', function (Blueprint $table) {
            $table->unsignedInteger('qty')->change();
        });
    }
};
