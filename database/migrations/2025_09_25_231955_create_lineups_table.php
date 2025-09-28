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
        Schema::create('lineups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('match_id')->constrained()->cascadeOnDelete();
            $t->foreignId('player_id')->constrained()->cascadeOnDelete();
            $t->enum('role', ['starter', 'bench'])->default('starter');
            $t->foreignId('position_id')->nullable()->constrained('player_positions')->nullOnDelete();
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineups');
    }
};
