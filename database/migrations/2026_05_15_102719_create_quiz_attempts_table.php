<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('enrollment_id')->nullable(); // nullable — made nullable by fix_quiz_attempts_for_elearning migration
            $table->unsignedBigInteger('quiz_id')->nullable(); // no db-level FK: no standalone quizzes table

            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->decimal('score', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};