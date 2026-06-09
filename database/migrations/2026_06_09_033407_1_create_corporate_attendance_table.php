<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_session_id')->constrained('corporate_sessions')->cascadeOnDelete();
            $table->foreignId('corporate_participant_id')->constrained('corporate_participants')->cascadeOnDelete();
            $table->enum('status', ['Present', 'Absent', 'Partial'])->default('Absent');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Short custom index name to stay under MySQL 64-char limit
            $table->unique(['corporate_session_id', 'corporate_participant_id'], 'corp_att_session_participant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_attendance');
    }
};
