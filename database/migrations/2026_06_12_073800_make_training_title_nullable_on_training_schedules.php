<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->string('training_title')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->string('training_title')->nullable(false)->change();
        });
    }
};
