<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('trainers')) {
            Schema::create('trainers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('designation')->nullable();
                $table->string('organization')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('qualification')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};