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
        Schema::create('player_career_stats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $t->unsignedInteger('matches')->default(0);
            $t->unsignedInteger('goals')->default(0);
            $t->unsignedInteger('assists')->default(0);
            $t->unsignedInteger('seven_meter')->default(0);
            $t->unsignedInteger('seven_meter_scored')->default(0);
            $t->unsignedInteger('yellow_cards')->default(0);
            $t->unsignedInteger('red_cards')->default(0);
            $t->unsignedInteger('blue_cards')->default(0);
            $t->unsignedInteger('suspensions_2min')->default(0);
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->unique('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_career_stats');
    }
};
