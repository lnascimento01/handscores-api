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
        Schema::create('match_team_stats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $t->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $t->unsignedSmallInteger('goals')->default(0);
            $t->unsignedSmallInteger('shots_on_goal')->default(0);
            $t->unsignedSmallInteger('seven_meter_throws')->default(0);
            $t->unsignedSmallInteger('saves')->default(0);
            $t->unsignedSmallInteger('turnovers')->default(0);
            $t->unsignedSmallInteger('assists')->default(0);
            $t->unsignedSmallInteger('blocks')->default(0);
            $t->unsignedSmallInteger('steals')->default(0);
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->unique(['match_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_team_stats');
    }
};
