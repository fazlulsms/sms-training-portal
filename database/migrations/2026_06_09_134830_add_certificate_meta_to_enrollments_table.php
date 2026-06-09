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
            $table->string('certificate_template')->nullable()->after('certificate_generated');
            $table->boolean('certificate_email_sent')->default(false)->after('certificate_template');
            $table->timestamp('certificate_email_sent_at')->nullable()->after('certificate_email_sent');
            $table->string('certificate_generated_by')->nullable()->after('certificate_email_sent_at');
            $table->timestamp('certificate_generated_at')->nullable()->after('certificate_generated_by');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_template',
                'certificate_email_sent',
                'certificate_email_sent_at',
                'certificate_generated_by',
                'certificate_generated_at',
            ]);
        });
    }
};
