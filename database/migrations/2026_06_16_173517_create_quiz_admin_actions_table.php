<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_admin_actions', function (Blueprint $table) {
            $table->id();
            // 'reset_attempts' | 'add_extra_attempt' | 'mark_passed'
            $table->string('action', 50);
            $table->unsignedBigInteger('admin_user_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('quiz_id');
            $table->text('reason');
            $table->decimal('previous_score', 5, 2)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['enrollment_id', 'quiz_id']);
            $table->index('admin_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_admin_actions');
    }
};
