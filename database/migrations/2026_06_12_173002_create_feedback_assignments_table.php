<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('feedback_templates')->cascadeOnDelete();
            $table->string('assignable_type', 60);
            $table->unsignedBigInteger('assignable_id');
            $table->boolean('is_required')->default(false);
            $table->boolean('require_for_certificate')->default(false);
            $table->unsignedTinyInteger('due_days_after_completion')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['assignable_type', 'assignable_id']);
            $table->unique(['template_id', 'assignable_type', 'assignable_id'], 'fa_template_assignable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_assignments');
    }
};
