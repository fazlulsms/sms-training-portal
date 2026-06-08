<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'course_fee')) {
                $table->decimal('course_fee', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('courses', 'access_days')) {
                $table->integer('access_days')->nullable();
            }

            if (!Schema::hasColumn('courses', 'passing_score')) {
                $table->integer('passing_score')->default(70);
            }

            if (!Schema::hasColumn('courses', 'certificate_template')) {
                $table->string('certificate_template')->nullable();
            }

            if (!Schema::hasColumn('courses', 'lesson_count')) {
                $table->integer('lesson_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'course_fee')) {
                $table->dropColumn('course_fee');
            }

            if (Schema::hasColumn('courses', 'access_days')) {
                $table->dropColumn('access_days');
            }

            if (Schema::hasColumn('courses', 'passing_score')) {
                $table->dropColumn('passing_score');
            }

            if (Schema::hasColumn('courses', 'certificate_template')) {
                $table->dropColumn('certificate_template');
            }

            if (Schema::hasColumn('courses', 'lesson_count')) {
                $table->dropColumn('lesson_count');
            }
        });
    }
};