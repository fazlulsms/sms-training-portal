<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_lesson_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->string('resource_type')->default('file');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_lesson_resources');
    }
};