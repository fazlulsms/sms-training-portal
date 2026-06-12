<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('feedback_templates')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['rating_5', 'yes_no', 'text', 'select'])->default('rating_5');
            $table->enum('category', ['overall', 'content', 'trainer', 'platform', 'open', 'elearning'])->default('overall');
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_questions');
    }
};
