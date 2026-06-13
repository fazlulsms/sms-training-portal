<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('correct_answer');
            $table->string('difficulty', 10)->default('medium')->after('explanation');
            $table->unsignedTinyInteger('module_index')->nullable()->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->dropColumn(['explanation', 'difficulty', 'module_index']);
        });
    }
};
