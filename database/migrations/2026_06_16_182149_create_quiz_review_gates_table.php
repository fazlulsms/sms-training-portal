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
        Schema::create('quiz_review_gates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('quiz_id');
            $table->json('required_lesson_ids');
            $table->json('reviewed_lesson_ids')->default('[]');
            $table->unsignedTinyInteger('extra_attempts_granted')->default(3);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->index(['enrollment_id', 'quiz_id', 'status']);
            $table->index('enrollment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_review_gates');
    }
};
