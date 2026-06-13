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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('gen_status', 20)->default('none')->after('ai_generated');
            $table->json('gen_progress')->nullable()->after('gen_status');
            $table->timestamp('gen_started_at')->nullable()->after('gen_progress');
            $table->timestamp('gen_completed_at')->nullable()->after('gen_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['gen_status', 'gen_progress', 'gen_started_at', 'gen_completed_at']);
        });
    }
};
