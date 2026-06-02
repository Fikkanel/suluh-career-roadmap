<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pseudonym_id to analytic tables for pseudonymization (Constitution P2)
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->string('pseudonym_id', 64)->nullable()->unique()->after('user_id');
        });

        Schema::table('impact_surveys', function (Blueprint $table) {
            $table->string('pseudonym_id', 64)->nullable()->unique()->after('user_id');
        });

        Schema::table('context_scores', function (Blueprint $table) {
            $table->string('pseudonym_id', 64)->nullable()->unique()->after('user_id');
        });

        // Add last_login_at to users for behavior tracking (Context Score dynamic)
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('work_experience');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->dropColumn('pseudonym_id');
        });

        Schema::table('impact_surveys', function (Blueprint $table) {
            $table->dropColumn('pseudonym_id');
        });

        Schema::table('context_scores', function (Blueprint $table) {
            $table->dropColumn('pseudonym_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
