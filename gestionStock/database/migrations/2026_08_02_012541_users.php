<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boutique_id')->constrained()->cascadeOnDelete();
            $table->string('password');
            $table->string('name', 255);
            $table->string('phone', 100);
            $table->string('email')->unique();

            $table->unique(['boutique_id', 'phone']);
            $table->unique(['boutique_id', 'email']);

            $table->enum('role', ['administrateur', 'vice_administrateur', 'vendeur'])->default('vendeur');
            $table->enum('statuts', ['actifs', 'passif'])->default('actifs');
            $table->rememberToken();
            //oblige un user a change son mot de passe
            $table->boolean('doit_changer_mdp')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};