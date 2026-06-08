<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_lessons', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('elearning_lessons', 'lesson_type')) {
                $table->string('lesson_type')->default('mixed')->after('status');
            }
            if (!Schema::hasColumn('elearning_lessons', 'learning_objectives')) {
                $table->text('learning_objectives')->nullable()->after('lesson_type');
            }
            if (!Schema::hasColumn('elearning_lessons', 'required_passing_score')) {
                $table->unsignedSmallInteger('required_passing_score')->nullable()->after('learning_objectives');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->dropColumn(['lesson_type', 'learning_objectives', 'required_passing_score']);
        });
    }
};
