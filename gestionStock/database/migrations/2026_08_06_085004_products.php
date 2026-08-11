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
        Schema::create('products' , function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('reference')->unique();
            $table->decimal('selling_price' ,10,2);
            $table->decimal('purchase_price',10,2);
            $table->unsignedInteger('alert_threshold')->default(0);
            $table->unsignedBigInteger('quantity')->default(0);
            $table->enum('status' , ['disponible','rupture'])->default('disponible');
            $table->string('photo_lieu')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
