<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_audio_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('elearning_enrollments')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->foreignId('audio_id')->constrained('lesson_audio')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Furthest position the learner has legitimately reached.
            // Used for resume and as the forward-seek boundary.
            $table->decimal('high_water_mark', 10, 2)->default(0);

            // Union of unique seconds actually heard (interval-merge algorithm on frontend).
            $table->decimal('seconds_listened', 10, 2)->default(0);

            // Cached from lesson_audio.duration_seconds for quick percentage calc.
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_listened_at')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'audio_id']);
            $table->index(['enrollment_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_audio_progress');
    }
};
