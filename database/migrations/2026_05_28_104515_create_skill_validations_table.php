<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['scenario', 'reflection', 'behavior'])->default('reflection');
            $table->text('response')->nullable();
            $table->unsignedTinyInteger('self_assessed_level')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'skill_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_validations');
    }
};
