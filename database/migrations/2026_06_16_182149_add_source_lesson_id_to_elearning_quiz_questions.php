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
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('source_lesson_id')->nullable()->after('module_index');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->dropColumn('source_lesson_id');
        });
    }
};
