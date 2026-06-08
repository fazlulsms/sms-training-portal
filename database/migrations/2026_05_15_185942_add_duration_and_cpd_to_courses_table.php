<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_courses', function (Blueprint $table) {
            $table->string('duration')->nullable();
            $table->integer('cpd_hours')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('elearning_courses', function (Blueprint $table) {
            $table->dropColumn(['duration', 'cpd_hours']);
        });
    }
};