<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('training_schedules')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->date('session_date');
            $table->string('session_label');             // "Day 1", "Day 2", etc.
            $table->string('status')->default('Pending'); // Present | Absent | Late | Excused | Pending
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['schedule_id', 'enrollment_id', 'session_date'], 'ta_schedule_enrollment_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_attendance');
    }
};
