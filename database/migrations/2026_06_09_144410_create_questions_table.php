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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_set_id');
            $table->text('question_text');
            $table->enum('question_type', [
                'mcq_single','mcq_multiple','true_false',
                'short_answer','paragraph','date','file_upload','declaration'
            ])->default('mcq_single');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('marks')->default(0);
            $table->string('correct_answer')->nullable();
            $table->boolean('exact_match_required')->default(false);
            $table->boolean('manual_review_required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('question_set_id')->references('id')->on('question_sets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
