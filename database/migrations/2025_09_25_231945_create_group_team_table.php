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
        Schema::create('group_team', function (Blueprint $t) {
            $t->id();
            $t->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $t->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $t->unsignedInteger('order')->default(0);
            $t->timestamps();
            $t->unique(['group_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_team');
    }
};
