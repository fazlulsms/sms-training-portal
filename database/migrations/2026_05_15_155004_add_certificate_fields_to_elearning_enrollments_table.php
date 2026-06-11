<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('elearning_enrollments', 'certificate_number')) {
                $table->string('certificate_number')->nullable()->after('certificate_status');
            }

            if (!Schema::hasColumn('elearning_enrollments', 'completion_date')) {
                $table->dateTime('completion_date')->nullable()->after('certificate_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('elearning_enrollments', 'completion_date')) {
                $table->dropColumn('completion_date');
            }

            if (Schema::hasColumn('elearning_enrollments', 'certificate_number')) {
                $table->dropColumn('certificate_number');
            }
        });
    }
};