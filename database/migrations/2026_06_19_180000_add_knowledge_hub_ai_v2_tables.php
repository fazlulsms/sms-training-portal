<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_resources', function (Blueprint $table) {
            $table->string('subcategory', 120)->nullable()->after('category');
            $table->string('clause_number', 80)->nullable()->after('standard_framework');
            $table->string('difficulty_level', 20)->default('intermediate')->after('version');
            $table->text('learning_objectives')->nullable()->after('notes');
            $table->text('source_references')->nullable()->after('learning_objectives');
            $table->longText('extracted_text')->nullable()->after('source_references');
            $table->string('extraction_status', 20)->default('pending')->after('extracted_text');
            $table->text('extraction_error')->nullable()->after('extraction_status');
            $table->timestamp('extracted_at')->nullable()->after('extraction_error');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_generation_version')->default(1)->after('ai_generated');
            $table->string('blueprint_status', 30)->default('not_required')->after('ai_generation_version');
            $table->timestamp('blueprint_approved_at')->nullable()->after('blueprint_status');
            $table->foreignId('blueprint_approved_by')->nullable()->after('blueprint_approved_at')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('target_learning_minutes')->nullable()->after('duration');
            $table->unsignedInteger('estimated_learning_minutes')->default(0)->after('target_learning_minutes');
            $table->unsignedTinyInteger('content_quality_score')->nullable()->after('estimated_learning_minutes');
            $table->json('content_quality_report')->nullable()->after('content_quality_score');
        });

        Schema::create('course_blueprint_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('learning_outcomes')->nullable();
            $table->unsignedSmallInteger('module_order');
            $table->unsignedInteger('estimated_minutes')->default(0);
            $table->timestamps();
            $table->unique(['course_id', 'module_order']);
        });

        Schema::create('course_knowledge_resource', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('knowledge_resource_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('source');
            $table->timestamps();
            $table->primary(['course_id', 'knowledge_resource_id']);
        });

        Schema::create('course_blueprint_module_knowledge_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('course_blueprint_module_id');
            $table->unsignedBigInteger('knowledge_resource_id');
            $table->foreign('course_blueprint_module_id', 'cbmkr_module_fk')->references('id')->on('course_blueprint_modules')->cascadeOnDelete();
            $table->foreign('knowledge_resource_id', 'cbmkr_resource_fk')->references('id')->on('knowledge_resources')->cascadeOnDelete();
            $table->primary(['course_blueprint_module_id', 'knowledge_resource_id'], 'cbm_knowledge_primary');
        });

        Schema::create('elearning_lesson_knowledge_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('elearning_lesson_id');
            $table->unsignedBigInteger('knowledge_resource_id');
            $table->foreign('elearning_lesson_id', 'elkr_lesson_fk')->references('id')->on('elearning_lessons')->cascadeOnDelete();
            $table->foreign('knowledge_resource_id', 'elkr_resource_fk')->references('id')->on('knowledge_resources')->cascadeOnDelete();
            $table->primary(['elearning_lesson_id', 'knowledge_resource_id'], 'lesson_knowledge_primary');
        });

        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->foreignId('blueprint_module_id')->nullable()->after('course_id')->constrained('course_blueprint_modules')->nullOnDelete();
            $table->unsignedInteger('reading_minutes')->default(0)->after('duration_minutes');
            $table->unsignedInteger('activity_minutes')->default(0)->after('reading_minutes');
            $table->unsignedInteger('exercise_minutes')->default(0)->after('activity_minutes');
            $table->unsignedInteger('quiz_minutes')->default(0)->after('exercise_minutes');
            $table->unsignedInteger('reflection_minutes')->default(0)->after('quiz_minutes');
            $table->unsignedInteger('estimated_learning_minutes')->default(0)->after('reflection_minutes');
        });

        Schema::create('ai_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('blueprint_module_id')->nullable()->constrained('course_blueprint_modules')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('elearning_lessons')->nullOnDelete();
            $table->foreignId('knowledge_resource_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type', 30)->default('mcq');
            $table->string('difficulty', 20)->default('medium');
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->index(['course_id', 'status']);
        });

        Schema::table('elearning_quiz_questions', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('quiz_id')->constrained('ai_question_bank')->nullOnDelete();
            $table->foreignId('knowledge_resource_id')->nullable()->after('source_lesson_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('elearning_quiz_questions', fn (Blueprint $table) => $table->dropConstrainedForeignId('question_bank_id'));
        Schema::table('elearning_quiz_questions', fn (Blueprint $table) => $table->dropConstrainedForeignId('knowledge_resource_id'));
        Schema::dropIfExists('ai_question_bank');
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blueprint_module_id');
            $table->dropColumn(['reading_minutes', 'activity_minutes', 'exercise_minutes', 'quiz_minutes', 'reflection_minutes', 'estimated_learning_minutes']);
        });
        Schema::dropIfExists('elearning_lesson_knowledge_resource');
        Schema::dropIfExists('course_blueprint_module_knowledge_resource');
        Schema::dropIfExists('course_knowledge_resource');
        Schema::dropIfExists('course_blueprint_modules');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blueprint_approved_by');
            $table->dropColumn(['ai_generation_version', 'blueprint_status', 'blueprint_approved_at', 'target_learning_minutes', 'estimated_learning_minutes', 'content_quality_score', 'content_quality_report']);
        });
        Schema::table('knowledge_resources', function (Blueprint $table) {
            $table->dropColumn(['subcategory', 'clause_number', 'difficulty_level', 'learning_objectives', 'source_references', 'extracted_text', 'extraction_status', 'extraction_error', 'extracted_at']);
        });
    }
};
