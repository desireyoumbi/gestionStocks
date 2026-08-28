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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
        $table->foreignId('boutique_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a enregistré la dépense
        $table->string('title'); // Ex: Loyer, Transport, Facture d'électricité...
        $table->decimal('amount', 12, 2); // Montant de la dépense
        $table->date('expense_date'); // Date de la dépense
        $table->text('description')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
