<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_question_bank', function (Blueprint $table) {
            $table->char('fingerprint', 64)->nullable()->after('question_text');
            $table->unique(['course_id', 'fingerprint'], 'aqb_course_fingerprint_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ai_question_bank', function (Blueprint $table) {
            $table->dropUnique('aqb_course_fingerprint_unique');
            $table->dropColumn('fingerprint');
        });
    }
};
