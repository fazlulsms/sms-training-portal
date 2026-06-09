<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_project_id')->constrained('corporate_projects')->cascadeOnDelete();
            $table->string('course_name');
            $table->string('trainer_name')->nullable();
            $table->date('training_date');
            $table->date('training_date_end')->nullable();
            $table->string('duration')->nullable();          // e.g. "2 days", "8 hours"
            $table->string('venue')->nullable();
            $table->string('target_group')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Planned', 'Ongoing', 'Completed', 'Cancelled'])->default('Planned');
            $table->boolean('certificates_generated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_sessions');
    }
};
