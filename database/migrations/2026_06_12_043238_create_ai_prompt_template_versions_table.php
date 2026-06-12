<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_prompt_template_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedInteger('version_number');
            $table->string('template_name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->text('output_format_instructions')->nullable();
            $table->string('model_override')->nullable();
            $table->decimal('temperature', 3, 2)->nullable();
            $table->unsignedInteger('max_tokens')->nullable();
            $table->unsignedBigInteger('saved_by')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('ai_prompt_templates')->cascadeOnDelete();
            $table->foreign('saved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_template_versions');
    }
};
