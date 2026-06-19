<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->unique(['quiz_id', 'question_bank_id'], 'quiz_question_bank_unique');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->dropUnique('quiz_question_bank_unique');
        });
    }
};
