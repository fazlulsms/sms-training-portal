<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_audio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->enum('audio_type', ['narration', 'ai_explanation']);
            $table->string('voice', 20)->default('nova');
            $table->string('language', 10)->default('en');
            $table->string('file_path')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->enum('status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'audio_type', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_audio');
    }
};
