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
        Schema::create('training_question_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_schedule_id');
            $table->unsignedBigInteger('question_set_id');
            $table->unsignedInteger('allowed_attempts')->nullable();
            $table->boolean('exam_active_after_attendance')->default(true);
            $table->timestamps();

            $table->unique('training_schedule_id');
            $table->foreign('training_schedule_id')->references('id')->on('training_schedules')->cascadeOnDelete();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_question_assignments');
    }
};
