<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('feedback_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('feedback_questions')->cascadeOnDelete();
            $table->unsignedTinyInteger('answer_rating')->nullable();
            $table->boolean('answer_bool')->nullable();
            $table->text('answer_text')->nullable();
            $table->timestamps();

            $table->unique(['response_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_answers');
    }
};
