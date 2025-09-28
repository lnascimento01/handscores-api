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
        Schema::create('mfa_challenges', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('factor_id')->nullable()->constrained('mfa_factors')->nullOnDelete();
            $t->string('channel');
            $t->string('code');               // hash
            $t->dateTime('expires_at');
            $t->unsignedSmallInteger('attempts')->default(0);
            $t->string('ip')->nullable();
            $t->string('user_agent')->nullable();
            $t->boolean('consumed')->default(false);
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_challenges');
    }
};
