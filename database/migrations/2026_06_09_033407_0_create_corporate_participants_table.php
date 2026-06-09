<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_project_id')->constrained('corporate_projects')->cascadeOnDelete();
            $table->foreignId('corporate_session_id')->constrained('corporate_sessions')->cascadeOnDelete();
            $table->string('participant_name');
            $table->string('employee_id')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_participants');
    }
};
