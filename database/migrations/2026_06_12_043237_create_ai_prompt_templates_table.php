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
        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('template_code')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->text('output_format_instructions')->nullable();
            $table->string('model_override')->nullable();
            $table->decimal('temperature', 3, 2)->nullable();
            $table->unsignedInteger('max_tokens')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version_number')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompt_templates');
    }
};
