<?php

namespace Tests\Feature;

use App\Jobs\GenerateModeBCourseJob;
use App\Models\Course;
use App\Models\CourseBlueprintModule;
use App\Models\ElearningLesson;
use App\Models\KnowledgeResource;
use App\Models\User;
use App\Services\KnowledgeResourceTextExtractor;
use App\Services\CourseQualityService;
use App\Services\AiQuestionBankService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiCourseGeneratorV2WorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Schema::disableForeignKeyConstraints();
        foreach ([
            'elearning_lesson_knowledge_resource', 'course_blueprint_module_knowledge_resource',
            'course_knowledge_resource', 'elearning_lessons', 'course_blueprint_modules',
            'elearning_quiz_questions', 'elearning_quizzes', 'lesson_blocks', 'ai_question_bank_course', 'ai_question_bank',
            'knowledge_resources', 'courses', 'users',
        ] as $table) Schema::dropIfExists($table);
        Schema::enableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->unique(); $table->timestamp('email_verified_at')->nullable(); $table->string('password');
            $table->string('role')->default('admin'); $table->boolean('is_active')->default(true);
            $table->rememberToken(); $table->timestamps();
        });
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->nullable();
            $table->unsignedTinyInteger('ai_generation_version')->default(1);
            $table->string('blueprint_status')->default('not_required');
            $table->timestamp('blueprint_approved_at')->nullable(); $table->unsignedBigInteger('blueprint_approved_by')->nullable();
            $table->string('gen_status')->default('none'); $table->timestamp('gen_started_at')->nullable();
            $table->string('ltf_competency_level')->nullable();
            $table->string('assessment_policy')->default('normal');
            $table->unsignedInteger('target_learning_minutes')->nullable(); $table->unsignedInteger('estimated_learning_minutes')->default(0);
            $table->unsignedTinyInteger('content_quality_score')->nullable(); $table->json('content_quality_report')->nullable();
            $table->timestamps();
        });
        Schema::create('knowledge_resources', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('resource_type'); $table->string('category');
            $table->string('standard_framework'); $table->string('status')->default('draft');
            $table->string('file_disk')->default('local'); $table->string('file_path'); $table->string('original_file_name');
            $table->string('extraction_status')->default('pending'); $table->longText('extracted_text')->nullable();
            $table->text('extraction_error')->nullable(); $table->timestamp('extracted_at')->nullable(); $table->timestamps();
        });
        Schema::create('course_blueprint_modules', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id'); $table->string('title');
            $table->text('learning_outcomes')->nullable(); $table->unsignedSmallInteger('module_order');
            $table->unsignedInteger('estimated_minutes')->default(0); $table->timestamps();
        });
        Schema::create('elearning_lessons', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id'); $table->unsignedBigInteger('blueprint_module_id')->nullable();
            $table->string('title'); $table->string('lesson_type')->default('mixed'); $table->unsignedInteger('lesson_order')->default(1);
            $table->text('learning_objectives')->nullable(); $table->unsignedInteger('estimated_learning_minutes')->default(0);
            $table->string('status')->default('draft'); $table->timestamps();
        });
        Schema::create('course_knowledge_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id'); $table->unsignedBigInteger('knowledge_resource_id');
            $table->string('role')->default('source'); $table->timestamps();
        });
        Schema::create('course_blueprint_module_knowledge_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('course_blueprint_module_id'); $table->unsignedBigInteger('knowledge_resource_id');
        });
        Schema::create('elearning_lesson_knowledge_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('elearning_lesson_id'); $table->unsignedBigInteger('knowledge_resource_id');
        });
        Schema::create('lesson_blocks', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lesson_id'); $table->string('block_type');
            $table->string('title')->nullable(); $table->text('content')->nullable(); $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active'); $table->boolean('audio_enabled')->default(false); $table->timestamps();
        });
        Schema::create('elearning_quizzes', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lesson_id'); $table->string('title');
            $table->unsignedInteger('pass_mark')->default(70); $table->unsignedInteger('max_attempt')->default(3);
            $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('elearning_quiz_questions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('quiz_id'); $table->text('question_text');
            $table->string('question_type')->default('mcq'); $table->unsignedInteger('module_index')->nullable();
            $table->unsignedBigInteger('knowledge_resource_id')->nullable(); $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('ai_question_bank', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->nullable(); $table->unsignedBigInteger('blueprint_module_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable(); $table->unsignedBigInteger('knowledge_resource_id');
            $table->text('question_text'); $table->char('fingerprint', 64); $table->string('question_type')->default('mcq');
            $table->string('difficulty')->default('medium'); $table->json('options')->nullable(); $table->text('correct_answer');
            $table->text('explanation')->nullable(); $table->string('status')->default('draft'); $table->timestamps();
        });
        Schema::create('ai_question_bank_course', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id'); $table->unsignedBigInteger('ai_question_bank_id'); $table->timestamps();
        });
    }

    public function test_blueprint_approval_is_required_and_revalidates_sources(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        [$course, $resource] = $this->sourcedCourse();

        $this->actingAs($admin)
            ->post(route('ai.course-generator.blueprint.approve', $course))
            ->assertRedirect();

        $this->assertSame('approved', $course->refresh()->blueprint_status);
        Queue::assertPushed(GenerateModeBCourseJob::class);

        [$secondCourse, $secondResource] = $this->sourcedCourse();
        $secondResource->update(['status' => 'archived']);
        $this->actingAs($admin)
            ->post(route('ai.course-generator.blueprint.approve', $secondCourse))
            ->assertStatus(422);
    }

    public function test_secondary_generation_endpoint_is_blocked_before_approval_but_legacy_is_not(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        [$course] = $this->sourcedCourse();

        $this->actingAs($admin)
            ->post(route('ai.course-generator.generate-next', $course), [])
            ->assertStatus(422);

        $legacy = Course::create(['name' => 'Legacy Course', 'ai_generation_version' => 1]);
        $this->actingAs($admin)
            ->post(route('ai.course-generator.generate-next', $legacy), [])
            ->assertStatus(404);
    }

    public function test_admin_can_review_v2_traceability_but_participant_cannot(): void
    {
        [$course] = $this->sourcedCourse();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $participant = User::factory()->create(['role' => 'participant', 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('ai.course-generator.blueprint', $course))
            ->assertOk()
            ->assertSee('Permanent Sources', false);

        $this->actingAs($participant)
            ->get(route('ai.course-generator.blueprint', $course))
            ->assertRedirect(route('participant.my-courses'));
    }

    public function test_txt_extraction_succeeds_and_invalid_docx_fails_safely(): void
    {
        Storage::disk('local')->put('knowledge/test.txt', "Clause 4.1\nContext of the organization");
        $txt = KnowledgeResource::create($this->resourceData('knowledge/test.txt', 'test.txt'));
        app(KnowledgeResourceTextExtractor::class)->extract($txt);
        $this->assertSame('ready', $txt->refresh()->extraction_status);
        $this->assertStringContainsString('Clause 4.1', $txt->extracted_text);

        Storage::disk('local')->put('knowledge/broken.docx', 'not a zip');
        $docx = KnowledgeResource::create($this->resourceData('knowledge/broken.docx', 'broken.docx'));
        app(KnowledgeResourceTextExtractor::class)->extract($docx);
        $this->assertContains($docx->refresh()->extraction_status, ['unsupported', 'failed']);
        $this->assertNotEmpty($docx->extraction_error);
    }

    public function test_quality_publish_gate_requires_duration_and_full_traceability(): void
    {
        [$course, $resource] = $this->sourcedCourse();
        $course->update([
            'blueprint_status' => 'approved', 'gen_status' => 'completed',
            'target_learning_minutes' => 60, 'estimated_learning_minutes' => 60,
        ]);
        $lesson = $course->elearningLessons()->whereNotNull('blueprint_module_id')->first();
        $lesson->update(['learning_objectives' => 'Interpret the approved source']);
        $lesson->allBlocks()->create(['block_type' => 'rich_text', 'content' => '<p>Grounded content</p>', 'status' => 'active']);
        $lesson->allBlocks()->create(['block_type' => 'workplace_example', 'content' => '{"examples":["Example"]}', 'status' => 'active']);

        $moduleCheck = ElearningLesson::create(['course_id' => $course->id, 'title' => 'Module 1 Knowledge Check', 'lesson_type' => 'assessment', 'lesson_order' => 2]);
        $moduleQuiz = $moduleCheck->quizzes()->create(['title' => 'Module Check', 'status' => 'active']);
        $moduleQuiz->questions()->create(['question_text' => 'Module question', 'knowledge_resource_id' => $resource->id, 'module_index' => 1]);

        $final = ElearningLesson::create(['course_id' => $course->id, 'title' => 'Final Course Assessment: QA', 'lesson_type' => 'assessment', 'lesson_order' => 3]);
        $finalQuiz = $final->quizzes()->create(['title' => 'Final', 'status' => 'active']);
        foreach (range(1, 15) as $number) {
            $finalQuiz->questions()->create(['question_text' => "Final {$number}", 'knowledge_resource_id' => $resource->id, 'module_index' => 1]);
        }

        $report = app(CourseQualityService::class)->evaluate($course->refresh());
        $this->assertTrue($report['publishable']);

        $course->update(['estimated_learning_minutes' => 20]);
        $report = app(CourseQualityService::class)->evaluate($course->refresh());
        $this->assertFalse($report['publishable']);
        $this->assertFalse($report['checks']['duration_target']);
    }

    public function test_question_bank_reuses_approved_source_question_and_rejects_archived_item(): void
    {
        [$course, $resource] = $this->sourcedCourse();
        $attributes = [
            'course_id' => $course->id, 'knowledge_resource_id' => $resource->id,
            'question_text' => 'What does clause 4.1 require?', 'question_type' => 'mcq',
            'options' => ['a' => 'Context'], 'correct_answer' => 'a', 'status' => 'approved',
        ];
        $service = app(AiQuestionBankService::class);
        $first = $service->store($attributes);
        $second = $service->store([...$attributes, 'course_id' => null]);
        $this->assertSame($first->id, $second->id);
        $course->questionBank()->syncWithoutDetaching([$first->id]);
        $this->assertTrue($course->questionBank()->whereKey($first->id)->exists());

        $first->update(['status' => 'archived']);
        $this->assertNull($service->store($attributes));
    }

    private function sourcedCourse(): array
    {
        $course = Course::create(['name' => 'V2 QA Course', 'ai_generation_version' => 2, 'blueprint_status' => 'awaiting_approval']);
        $resource = KnowledgeResource::create([
            ...$this->resourceData('knowledge/source.txt', 'source.txt'),
            'status' => 'approved', 'extraction_status' => 'ready', 'extracted_text' => 'Approved source text',
        ]);
        $course->knowledgeResources()->attach($resource->id);
        $module = CourseBlueprintModule::create(['course_id' => $course->id, 'title' => 'Module 1', 'module_order' => 1]);
        $module->knowledgeResources()->attach($resource->id);
        $lesson = ElearningLesson::create(['course_id' => $course->id, 'blueprint_module_id' => $module->id, 'title' => 'Lesson 1', 'learning_objectives' => 'Objective']);
        $lesson->knowledgeResources()->attach($resource->id);
        return [$course, $resource];
    }

    private function resourceData(string $path, string $name): array
    {
        return [
            'title' => $name, 'resource_type' => 'Standard', 'category' => 'ISO Standards',
            'standard_framework' => 'ISO 9001', 'file_disk' => 'local', 'file_path' => $path,
            'original_file_name' => $name,
        ];
    }
}
