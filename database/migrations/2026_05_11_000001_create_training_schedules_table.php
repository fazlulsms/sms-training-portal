<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();

            $table->string('batch_code')->nullable();
            $table->string('training_title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();
            $table->string('currency')->default('BDT');
            $table->decimal('physical_fee', 10, 2)->nullable();
            $table->decimal('online_fee', 10, 2)->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->string('venue')->nullable();
            $table->string('zoom_link')->nullable();
            $table->string('training_mode')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->string('status')->default('Open');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
