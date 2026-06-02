<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roadmap_archives', function (Blueprint $table) {
            $table->string('career_name')->nullable()->after('career_id');
            $table->unsignedSmallInteger('completed_skills')->default(0)->after('career_name');
            $table->unsignedSmallInteger('total_skills')->default(0)->after('completed_skills');
        });
    }

    public function down(): void
    {
        Schema::table('roadmap_archives', function (Blueprint $table) {
            $table->dropColumn(['career_name', 'completed_skills', 'total_skills']);
        });
    }
};
