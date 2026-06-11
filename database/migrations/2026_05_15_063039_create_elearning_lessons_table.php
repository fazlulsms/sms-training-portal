<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();

            $table->string('title');
            $table->integer('lesson_order')->default(0);
            $table->string('video_url')->nullable();
            $table->longText('lesson_content')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_lessons');
    }
};
