<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('pass_mark')->default(70);
            $table->integer('max_attempt')->default(3);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_quizzes');
    }
};