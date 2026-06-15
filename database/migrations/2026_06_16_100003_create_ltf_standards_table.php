<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ltf_standards', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 50)->index();   // fixed constant in model, no separate table
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('slug')->unique();
            $table->string('version', 20)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ltf_standards');
    }
};
