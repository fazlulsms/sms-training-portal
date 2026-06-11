<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('courses', 'cpd_hours')) {
                $table->integer('cpd_hours')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['duration', 'cpd_hours']);
        });
    }
};