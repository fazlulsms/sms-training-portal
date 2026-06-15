<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Remove old Layer 1 column (FK + column in one call)
            $table->dropConstrainedForeignId('ltf_course_type_id');

            // Add new multi-dimensional classification fields
            $table->foreignId('ltf_delivery_method_id')
                  ->nullable()
                  ->after('ltf_learning_framework_id')
                  ->constrained('ltf_delivery_methods')
                  ->nullOnDelete();

            $table->foreignId('ltf_training_model_id')
                  ->nullable()
                  ->after('ltf_delivery_method_id')
                  ->constrained('ltf_training_models')
                  ->nullOnDelete();

            $table->foreignId('ltf_program_purpose_id')
                  ->nullable()
                  ->after('ltf_training_model_id')
                  ->constrained('ltf_program_purposes')
                  ->nullOnDelete();

            $table->enum('ltf_competency_level', ['beginner', 'intermediate', 'advanced', 'expert'])
                  ->nullable()
                  ->after('ltf_program_purpose_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ltf_program_purpose_id');
            $table->dropConstrainedForeignId('ltf_training_model_id');
            $table->dropConstrainedForeignId('ltf_delivery_method_id');
            $table->dropColumn('ltf_competency_level');

            // Restore old column
            $table->foreignId('ltf_course_type_id')
                  ->nullable()
                  ->constrained('ltf_course_types')
                  ->nullOnDelete();
        });
    }
};
