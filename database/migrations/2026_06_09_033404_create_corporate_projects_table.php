<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['Active', 'Completed', 'On Hold', 'Cancelled'])->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_projects');
    }
};
