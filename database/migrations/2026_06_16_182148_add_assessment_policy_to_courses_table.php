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
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('assessment_policy', ['normal', 'auditor', 'custom'])->default('normal')->after('cpd_hours');
            $table->unsignedTinyInteger('module_check_max_attempts')->default(3)->after('assessment_policy');
            $table->unsignedTinyInteger('final_exam_max_attempts')->default(3)->after('module_check_max_attempts');
            $table->boolean('require_module_review')->default(true)->after('final_exam_max_attempts');
            $table->boolean('require_admin_approval')->default(false)->after('require_module_review');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_policy',
                'module_check_max_attempts',
                'final_exam_max_attempts',
                'require_module_review',
                'require_admin_approval',
            ]);
        });
    }
};
