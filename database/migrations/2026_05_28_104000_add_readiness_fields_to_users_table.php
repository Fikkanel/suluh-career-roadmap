<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('exploration_readiness', 20)->nullable()->after('province');
            $table->string('support_level', 20)->nullable()->after('exploration_readiness');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['exploration_readiness', 'support_level']);
        });
    }
};
