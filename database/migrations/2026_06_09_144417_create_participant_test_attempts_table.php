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
        Schema::create('participant_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('question_set_id');
            $table->string('exam_token', 64)->unique();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->enum('status', [
                'not_started','in_progress','submitted',
                'pending_review','passed','failed','attempt_limit_reached'
            ])->default('not_started');
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('total_marks')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('pass_fail')->nullable();
            $table->boolean('manual_review_pending')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_test_attempts');
    }
};
