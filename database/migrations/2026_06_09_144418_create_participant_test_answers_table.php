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
        Schema::create('participant_test_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer_text')->nullable();
            $table->json('answer_options')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('marks_awarded')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->boolean('manual_graded')->default(false);
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();

            $table->foreign('attempt_id')->references('id')->on('participant_test_attempts')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_test_answers');
    }
};
