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
        Schema::create('player_match_stats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $t->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $t->unsignedSmallInteger('goals')->default(0);
            $t->unsignedSmallInteger('shots')->default(0);
            $t->unsignedSmallInteger('shots_on_goal')->default(0);
            $t->unsignedSmallInteger('assists')->default(0);
            $t->unsignedSmallInteger('seven_meter')->default(0);
            $t->unsignedSmallInteger('seven_meter_scored')->default(0);
            $t->unsignedSmallInteger('yellow_cards')->default(0);
            $t->unsignedSmallInteger('red_cards')->default(0);
            $t->unsignedSmallInteger('blue_cards')->default(0);
            $t->unsignedSmallInteger('suspensions_2min')->default(0);
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->unique(['player_id', 'match_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};
