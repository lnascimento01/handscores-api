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
        Schema::create('mfa_factors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['totp', 'sms', 'email', 'whatsapp']);
            $t->string('label')->nullable();
            $t->string('secret')->nullable();
            $t->string('destination')->nullable();
            $t->boolean('verified')->default(false);
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_factors');
    }
};
