<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_session_id')->constrained('corporate_sessions')->cascadeOnDelete();
            $table->enum('type', ['Training Photo', 'Group Photo', 'Presentation', 'Document', 'Other']);
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_evidences');
    }
};
