<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_ltf_standards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('ltf_standard_id');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('ltf_standard_id')->references('id')->on('ltf_standards')->onDelete('cascade');
            $table->unique(['course_id', 'ltf_standard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_ltf_standards');
    }
};
