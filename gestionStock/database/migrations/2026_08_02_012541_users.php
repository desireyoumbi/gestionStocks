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
        Schema::create('users' , function(Blueprint $table){
            $table->id();
            $table->string('clerk_id')->unique();
            $table->string('name' , 255);
            $table->string('phone' , 100)->nullable();
            $table->string('email')->unique();
            $table->enum('statuts',['actifs' , 'passif']);
            $table->enum('role',['admin' ,'viceAdmin' , 'vendeur'])->default('vendeur');
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
