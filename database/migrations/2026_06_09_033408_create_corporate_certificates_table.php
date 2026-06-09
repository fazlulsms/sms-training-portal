<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_project_id')->constrained('corporate_projects')->cascadeOnDelete();
            $table->foreignId('corporate_session_id')->constrained('corporate_sessions')->cascadeOnDelete();
            $table->foreignId('corporate_participant_id')->constrained('corporate_participants')->cascadeOnDelete();
            $table->string('certificate_number')->unique();   // SMS-TR-YYYY-XXXXX
            $table->date('issue_date');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_certificates');
    }
};
