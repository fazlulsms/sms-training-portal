<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lesson_id')
                  ->constrained('elearning_lessons')
                  ->cascadeOnDelete();

            // Block type: rich_text | video | accordion | image | gallery |
            //             slides | audio | pdf | knowledge_check | scenario |
            //             matching | download
            $table->string('block_type');

            $table->string('title')->nullable();

            // Primary content field.
            // - rich_text  : HTML string
            // - video      : URL string
            // - audio      : URL string
            // - pdf        : URL / storage path string
            // - image      : URL / storage path string
            // - accordion  : JSON array  [{title, body}]
            // - gallery    : JSON array  [{url, caption}]
            // - slides     : JSON array  [{title, text, image_url}]
            // - knowledge_check : JSON   {question, type, options:[{text,correct}], explanation}
            // - scenario   : JSON        {text, options:[{text,explanation,correct}]}
            // - matching   : JSON        {pairs:[{left,right}]}
            // - download   : JSON array  [{title, url, type}]
            $table->longText('content')->nullable();

            // Path for uploaded media (image block, audio block, pdf block)
            $table->string('media_path')->nullable();

            // Extra settings (caption, required_for_completion, auto_play, etc.)
            $table->json('settings_json')->nullable();

            $table->integer('sort_order')->default(0);

            // active | inactive
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_blocks');
    }
};
