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
        Schema::create('stock_movements', function (Blueprint $table) {
           $table->id();
           $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade');
           $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Le produit concerné
           $table->foreignId('user_id')->constrained()->onDelete('cascade');    // L'utilisateur qui a fait l'action
           $table->string('type'); // Ex: 'entry' (entrée), 'exit' (sortie), 'sale' (vente), 'adjustment' (ajustement)
           $table->integer('quantity'); // La quantité impactée (+10 ou -5)
           $table->integer('old_stock'); // Le stock avant l'opération
           $table->integer('new_stock'); // Le stock après l'opération
           $table->string('reason')->nullable(); // Un motif ou commentaire optionnel
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
