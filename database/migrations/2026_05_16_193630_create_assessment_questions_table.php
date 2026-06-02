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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->text('prompt');
            $table->text('context')->nullable();
            $table->string('riasec_category', 1)->nullable();
            $table->string('big_five_trait', 30)->nullable();
            $table->decimal('weight', 4, 2)->default(1.00);
            $table->enum('type', ['single_choice', 'scale', 'text_reflection'])->default('single_choice');
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
