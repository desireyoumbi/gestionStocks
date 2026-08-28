<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // On passe la colonne amount à 20 chiffres au total dont 2 après la virgule
            $table->decimal('amount', 20, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change();
        });
    }
};