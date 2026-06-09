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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('exam_email_sent')->default(false)->after('certificate_generated_at');
            $table->timestamp('exam_email_sent_at')->nullable()->after('exam_email_sent');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['exam_email_sent', 'exam_email_sent_at']);
        });
    }
};
