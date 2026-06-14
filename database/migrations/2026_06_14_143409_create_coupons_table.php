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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage', 'complimentary']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('training_type', ['both', 'elearning', 'ilt'])->default('both');
            $table->json('course_ids')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedTinyInteger('per_user_limit')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
