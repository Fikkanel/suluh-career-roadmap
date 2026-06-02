<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->enum('validation_type', ['none', 'scenario', 'reflection', 'behavior'])->default('none')->after('order');
            $table->text('scenario_question')->nullable()->after('validation_type');
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['validation_type', 'scenario_question']);
        });
    }
};
