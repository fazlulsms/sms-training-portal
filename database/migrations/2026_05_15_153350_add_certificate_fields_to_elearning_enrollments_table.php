<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->string('certificate_number')->nullable()->after('status');
            $table->date('certificate_issue_date')->nullable()->after('certificate_number');
            $table->boolean('certificate_generated')->default(false)->after('certificate_issue_date');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_number',
                'certificate_issue_date',
                'certificate_generated',
            ]);
        });
    }
};