<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boutique_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('reference')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('purchase_price', 10, 2);
            $table->unsignedInteger('alert_threshold')->default(0);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->enum('status', ['disponible', 'rupture'])->default('disponible');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['boutique_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};