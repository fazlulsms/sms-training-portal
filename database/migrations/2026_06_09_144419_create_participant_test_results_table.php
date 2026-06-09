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
        Schema::create('participant_test_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id')->unique();
            $table->unsignedBigInteger('question_set_id');
            $table->enum('overall_status', [
                'not_started','in_progress','passed','failed',
                'attempt_limit_reached','pending_review'
            ])->default('not_started');
            $table->unsignedInteger('attempts_used')->default(0);
            $table->unsignedInteger('best_score')->nullable();
            $table->decimal('best_percentage', 5, 2)->nullable();
            $table->boolean('certificate_eligible')->default(false);
            $table->timestamp('passed_at')->nullable();
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
        Schema::dropIfExists('participant_test_results');
    }
};
