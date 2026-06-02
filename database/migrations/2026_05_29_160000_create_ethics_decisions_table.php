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
        Schema::create('ethics_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('context');
            $table->text('decision')->nullable();
            $table->enum('status', ['proposed', 'voting', 'approved', 'rejected'])->default('proposed');
            $table->integer('votes_for')->default(0);
            $table->integer('votes_against')->default(0);
            $table->date('implementation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethics_decisions');
    }
};
