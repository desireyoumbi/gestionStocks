<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            $table->string('ville', 100)->after('nom');
            $table->string('quartier', 150)->after('ville');
            $table->string('indication', 255)->after('quartier');

            $table->boolean('marketplace_enabled')
                ->default(false)
                ->after('indication');

            $table->timestamp('marketplace_published_at')
                ->nullable()
                ->after('marketplace_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            $table->dropColumn([
                'ville',
                'quartier',
                'indication',
                'marketplace_enabled',
                'marketplace_published_at',
            ]);
        });
    }
};