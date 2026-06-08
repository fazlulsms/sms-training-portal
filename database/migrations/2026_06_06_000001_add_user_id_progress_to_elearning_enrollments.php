<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedTinyInteger('progress_percentage')
                ->default(0)
                ->after('completion_status');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'progress_percentage']);
        });
    }
};
