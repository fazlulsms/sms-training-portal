<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pre-flight: abort if duplicates exist so the migration fails loudly rather than silently
        $dupes = DB::select(
            'SELECT course_id, email, COUNT(*) as cnt
             FROM elearning_enrollments
             GROUP BY course_id, email
             HAVING cnt > 1'
        );

        if (!empty($dupes)) {
            $list = collect($dupes)->map(fn ($d) => "course_id={$d->course_id} email={$d->email} ({$d->cnt}x)")->implode(', ');
            throw new \RuntimeException("Cannot add unique constraint — duplicate enrollments exist: {$list}. Resolve duplicates manually before re-running this migration.");
        }

        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->unique(['course_id', 'email'], 'elearning_enrollments_course_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->dropUnique('elearning_enrollments_course_email_unique');
        });
    }
};
