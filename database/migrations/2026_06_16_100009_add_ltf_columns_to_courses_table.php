<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Layer 1 — course type (nullable: existing courses not yet classified)
            $table->unsignedBigInteger('ltf_course_type_id')->nullable()->after('category_id');
            $table->foreign('ltf_course_type_id')->references('id')->on('ltf_course_types')->nullOnDelete();

            // Layer 2 — learning design framework
            $table->unsignedBigInteger('ltf_learning_framework_id')->nullable()->after('ltf_course_type_id');
            $table->foreign('ltf_learning_framework_id')->references('id')->on('ltf_learning_frameworks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['ltf_course_type_id']);
            $table->dropForeign(['ltf_learning_framework_id']);
            $table->dropColumn(['ltf_course_type_id', 'ltf_learning_framework_id']);
        });
    }
};
