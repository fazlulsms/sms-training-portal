<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('title');
            // completion_rule: manual | pass_quiz
            $table->string('completion_rule')->default('manual')->after('lesson_content');
            // whether this lesson counts toward certificate eligibility
            $table->boolean('certificate_eligible')->default(true)->after('completion_rule');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'completion_rule', 'certificate_eligible']);
        });
    }
};
