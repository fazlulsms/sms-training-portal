<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('resource_type', 80);
            $table->string('category', 100);
            $table->string('standard_framework', 150);
            $table->string('version', 50)->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('notes')->nullable();

            $table->string('file_disk', 30)->default('local');
            $table->string('file_path');
            $table->string('original_file_name');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            // Reserved metadata keeps the record extensible for future text extraction
            // and AI source selection without coupling this phase to an AI workflow.
            $table->json('metadata')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'category']);
            $table->index(['resource_type', 'status']);
            $table->index('standard_framework');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_resources');
    }
};
