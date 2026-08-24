<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            // Pour l'isolation par boutique si ton app est multi-boutique
            $table->foreignId('boutique_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a fait l'opération
            
            $table->string('type'); // 'in' (entrée), 'out' (sortie), 'opening' (fond de caisse initial)
            $table->decimal('amount', 12, 2); // Le montant
            
            // Catégorie pour savoir si c'est une vente, une dépense, un réapprovisionnement ou un ajustement
            $table->string('category'); // 'sale', 'expense', 'restock', 'initial', 'unjustified_or_personal'
            
            $table->text('motif')->nullable(); // Description ou commentaire
            
            // Indicateur si l'argent venait d'une source non justifiée / poche personnelle lors d'un dépassement
            $table->boolean('is_unjustified')->default(false); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};