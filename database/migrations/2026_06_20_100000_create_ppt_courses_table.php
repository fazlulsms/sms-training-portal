<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppt_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'processing', 'ready', 'published'])->default('draft');
            $table->string('original_filename');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('total_slides')->default(0);
            $table->text('processing_error')->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('completion_mode', ['slide', 'module', 'assessment'])->default('slide');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppt_courses');
    }
};
