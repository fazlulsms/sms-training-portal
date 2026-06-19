<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_question_bank_course', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ai_question_bank_id')->constrained('ai_question_bank')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['course_id', 'ai_question_bank_id'], 'aqb_course_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_question_bank_course');
    }
};
