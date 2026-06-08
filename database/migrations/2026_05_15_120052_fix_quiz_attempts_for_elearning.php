<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'elearning_enrollment_id')) {
                $table->unsignedBigInteger('elearning_enrollment_id')->nullable()->after('enrollment_id');
            }
        });

        DB::statement('ALTER TABLE quiz_attempts MODIFY enrollment_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'elearning_enrollment_id')) {
                $table->dropColumn('elearning_enrollment_id');
            }
        });

        DB::statement('ALTER TABLE quiz_attempts MODIFY enrollment_id BIGINT UNSIGNED NOT NULL');
    }
};