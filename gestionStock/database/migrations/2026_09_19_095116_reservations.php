<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('boutique_id')
                ->constrained('boutiques')
                ->cascadeOnDelete();

            $table->string('code', 20)->unique();

            $table->enum('status', [
                'active',
                'completed',
                'cancelled',
                'expired',
            ])->default('active');

            $table->timestamp('expires_at');

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->string('cancel_reason', 255)
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};