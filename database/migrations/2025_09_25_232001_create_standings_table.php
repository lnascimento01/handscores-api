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
        Schema::create('standings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $t->foreignId('team_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('played')->default(0);
            $t->unsignedSmallInteger('wins')->default(0);
            $t->unsignedSmallInteger('draws')->default(0);
            $t->unsignedSmallInteger('losses')->default(0);
            $t->unsignedSmallInteger('goals_for')->default(0);
            $t->unsignedSmallInteger('goals_against')->default(0);
            $t->integer('goal_diff')->default(0);
            $t->unsignedSmallInteger('points')->default(0);
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->unique(['competition_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
