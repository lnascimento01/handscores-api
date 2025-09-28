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
        Schema::create('players', function (Blueprint $t) {
            $t->id();
            $t->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $t->foreignId('position_id')->nullable()->constrained('player_positions')->nullOnDelete();
            $t->string('first_name');
            $t->string('last_name');
            $t->date('birthdate')->nullable();
            $t->unsignedSmallInteger('number')->nullable();
            $t->string('nationality', 2)->nullable();
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
        Schema::dropIfExists('players');
    }
};
