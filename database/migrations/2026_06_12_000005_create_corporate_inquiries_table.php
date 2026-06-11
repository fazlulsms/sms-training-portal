<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('corporate_inquiries')) {
            Schema::create('corporate_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('company_name');
                $table->string('contact_person');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('country')->nullable();
                $table->text('training_requirement');
                $table->unsignedInteger('participants_count')->nullable();
                $table->date('preferred_date')->nullable();
                $table->string('preferred_mode')->default('Physical');
                $table->text('message')->nullable();
                $table->string('status')->default('New');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_inquiries');
    }
};
