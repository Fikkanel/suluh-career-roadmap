<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL, change json to text because encrypted strings are not valid JSON
        Schema::table('users', function (Blueprint $table) {
            $table->text('personality_scores')->nullable()->change();
        });
        
        Schema::table('context_scores', function (Blueprint $table) {
            $table->text('factors')->nullable()->change();
        });
        
        Schema::table('impact_surveys', function (Blueprint $table) {
            $table->text('answers')->nullable()->change();
        });
        
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->text('riasec_scores')->nullable()->change();
            $table->text('big_five_scores')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert to json
        Schema::table('users', function (Blueprint $table) {
            $table->json('personality_scores')->nullable()->change();
        });
        
        Schema::table('context_scores', function (Blueprint $table) {
            $table->json('factors')->nullable()->change();
        });
        
        Schema::table('impact_surveys', function (Blueprint $table) {
            $table->json('answers')->nullable()->change();
        });
        
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->json('riasec_scores')->nullable()->change();
            $table->json('big_five_scores')->nullable()->change();
        });
    }
};
