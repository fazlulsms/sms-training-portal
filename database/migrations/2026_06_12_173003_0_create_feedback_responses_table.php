<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('feedback_assignments')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->unsignedBigInteger('elearning_enrollment_id')->nullable();
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('respondent_name', 150)->nullable();
            $table->string('respondent_email', 150)->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->boolean('testimonial_consent')->default(false);
            $table->boolean('testimonial_approved')->default(false);
            $table->text('testimonial_text')->nullable();
            $table->decimal('overall_rating', 3, 2)->nullable();
            $table->timestamps();

            $table->index('enrollment_id');
            $table->index('elearning_enrollment_id');
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_responses');
    }
};
