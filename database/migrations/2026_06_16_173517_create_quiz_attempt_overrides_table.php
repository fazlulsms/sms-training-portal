<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempt_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');  // elearning_enrollments.id
            $table->unsignedBigInteger('quiz_id');         // elearning_quizzes.id
            $table->unsignedTinyInteger('extra_attempts')->default(1);
            $table->unsignedBigInteger('admin_user_id');
            $table->text('reason');
            $table->timestamps();

            $table->unique(['enrollment_id', 'quiz_id']);
            $table->index('enrollment_id');
            $table->index('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_overrides');
    }
};
