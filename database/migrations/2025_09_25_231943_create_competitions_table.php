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
        Schema::create('competitions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('season')->index();   // "2025/26"
            $t->foreignId('type_id')->constrained('competition_types')->cascadeOnDelete();
            $t->string('country', 2)->nullable();
            $t->enum('scope', ['national', 'state', 'international'])->default('national');
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
        Schema::dropIfExists('competitions');
    }
};
