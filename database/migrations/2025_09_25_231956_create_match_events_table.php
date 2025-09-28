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
        Schema::create('match_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('match_id')->constrained()->cascadeOnDelete();
            $t->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $t->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $t->foreignId('type_id')->nullable()->constrained('event_types')->nullOnDelete(); // ou enum
            $t->integer('match_time_seconds')->default(0);
            $t->json('payload')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['match_id', 'match_time_seconds']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_events');
    }
};
