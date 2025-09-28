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
        Schema::create('news', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->text('body');
            $t->string('lang', 5)->default('pt-BR');
            $t->json('tags')->nullable();
            $t->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
