<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('training_schedule_id')->constrained('training_schedules')->cascadeOnDelete();

            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('full_address')->nullable();
            $table->string('selected_mode')->nullable();
            $table->decimal('applied_fee', 10, 2)->nullable();
            $table->string('payment_status')->default('Pending');
            $table->decimal('amount_received', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('registration_status')->default('Pending');
            $table->string('attendance_status')->default('Pending');
            $table->string('completion_status')->default('Pending');
            $table->string('certificate_number')->nullable();
            $table->date('certificate_issue_date')->nullable();
            $table->boolean('certificate_generated')->default(false);
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
