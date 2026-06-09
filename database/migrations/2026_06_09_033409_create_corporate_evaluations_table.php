<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_session_id')->constrained('corporate_sessions')->cascadeOnDelete();
            $table->foreignId('corporate_participant_id')->nullable()->constrained('corporate_participants')->nullOnDelete();
            $table->string('evaluator_name', 150)->nullable();
            $table->unsignedTinyInteger('feedback_score')->nullable();   // 1-5
            $table->text('comments')->nullable();
            $table->text('effectiveness_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_evaluations');
    }
};
