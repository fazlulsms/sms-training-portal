<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_schedule_id');
            $table->string('media_type')->default('gallery'); // cover|gallery|group|trainer|venue|activity
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->text('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('ai_captions_generated')->default(false);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('training_schedule_id')->references('id')->on('training_schedules')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['training_schedule_id', 'media_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_media');
    }
};
