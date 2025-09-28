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
        Schema::create('matches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $t->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $t->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('home_team_id')->constrained('teams');
            $t->foreignId('away_team_id')->constrained('teams');
            $t->dateTime('start_at')->index();
            $t->enum('status', ['scheduled', 'live', 'halftime', 'finished', 'postponed', 'cancelled'])->default('scheduled')->index();
            $t->tinyInteger('period')->default(0);
            $t->integer('clock_seconds')->default(0);
            $t->boolean('has_overtime')->default(false);
            $t->boolean('has_penalties')->default(false);
            $t->unsignedSmallInteger('home_score')->default(0);
            $t->unsignedSmallInteger('away_score')->default(0);
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
