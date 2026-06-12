<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('type', ['ilt', 'elearning', 'webinar', 'workshop', 'trainer'])->default('ilt');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('require_for_certificate')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_templates');
    }
};
