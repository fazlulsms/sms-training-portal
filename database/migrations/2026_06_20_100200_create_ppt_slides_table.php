<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppt_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppt_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ppt_module_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('slide_number');
            $table->unsignedInteger('slide_order')->default(0);
            $table->string('title')->nullable();
            $table->text('content_text')->nullable();
            $table->text('speaker_notes')->nullable();
            $table->string('image_path')->nullable();
            $table->text('discussion_points')->nullable();
            $table->text('ai_explanation')->nullable();
            $table->text('ai_narration_script')->nullable();
            $table->json('ai_key_points')->nullable();
            $table->text('ai_trainer_notes')->nullable();
            $table->timestamp('ai_generated_at')->nullable();
            $table->string('audio_path')->nullable();
            $table->unsignedInteger('audio_duration')->nullable();
            $table->enum('audio_status', ['none', 'processing', 'ready', 'failed'])->default('none');
            $table->timestamp('audio_generated_at')->nullable();
            $table->json('knowledge_check')->nullable();
            $table->text('trainer_notes')->nullable();
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppt_slides');
    }
};
