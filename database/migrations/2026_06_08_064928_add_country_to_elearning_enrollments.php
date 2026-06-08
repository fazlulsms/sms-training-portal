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
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('elearning_enrollments', 'country')) {
                $table->string('country', 100)->nullable()->after('designation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('elearning_enrollments', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};
