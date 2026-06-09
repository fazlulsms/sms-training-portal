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
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedInteger('total_marks')->default(100);
            $table->unsignedInteger('pass_mark')->nullable();
            $table->unsignedInteger('pass_percentage')->nullable();
            $table->unsignedInteger('allowed_attempts')->default(1);
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->boolean('show_result_to_participant')->default(true);
            $table->boolean('allow_certificate_after_pass')->default(true);
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_sets');
    }
};
