<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();

            $table->string('participant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('BDT');

            $table->string('payment_method')->default('manual');
            $table->string('payment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('gateway_name')->nullable();
            $table->longText('gateway_response')->nullable();

            $table->string('access_status')->default('locked');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('completion_status')->default('not_started');
            $table->string('certificate_status')->default('not_issued');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_enrollments');
    }
};