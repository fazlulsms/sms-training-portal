<?php

namespace App\Services;

use App\Models\Course;

class CourseQualityService
{
    public function evaluate(Course $course): array
    {
        $course->load(['knowledgeResources', 'blueprintModules.knowledgeResources', 'elearningLessons.allBlocks', 'elearningLessons.knowledgeResources', 'elearningLessons.quizzes.questions']);
        $lessons = $course->elearningLessons->filter(fn ($lesson) => $lesson->lesson_type !== 'assessment' && $lesson->blueprint_module_id);
        $blocks = $lessons->flatMap->allBlocks;
        $contentHashes = $blocks->map(fn ($block) => md5(strtolower(trim(strip_tags((string) $block->content)))))->filter();
        $questions = $course->elearningLessons->flatMap->quizzes->flatMap->questions;
        $finalQuestions = $course->elearningLessons
            ->filter(fn ($lesson) => str_starts_with($lesson->title, 'Final Course Assessment'))
            ->flatMap->quizzes->flatMap->questions;
        $coveredModules = $finalQuestions->pluck('module_index')->filter()->unique();
        $expectedFinalQuestions = $course->assessment_policy === 'auditor'
            ? [30, 50]
            : [15, 20];
        $checks = [
            'approved_blueprint' => $course->blueprint_status === 'approved',
            'approved_sources_only' => $course->knowledgeResources->isNotEmpty() && $course->knowledgeResources->every(fn ($resource) => $resource->status === 'approved' && filled($resource->extracted_text)),
            'all_modules_sourced' => $course->blueprintModules->every(fn ($module) => $module->knowledgeResources->isNotEmpty()),
            'all_lessons_sourced' => $lessons->every(fn ($lesson) => $lesson->knowledgeResources->isNotEmpty()),
            'learning_objectives' => $lessons->every(fn ($lesson) => filled($lesson->learning_objectives)),
            'lesson_content' => $lessons->every(fn ($lesson) => $lesson->allBlocks->isNotEmpty()),
            'practical_examples' => $lessons->every(fn ($lesson) => $lesson->allBlocks->contains(fn ($block) => in_array($block->block_type, ['workplace_example', 'case_study', 'scenario']))),
            'no_duplicate_content' => $contentHashes->isEmpty() || $contentHashes->unique()->count() / $contentHashes->count() >= .9,
            'module_checks' => $course->elearningLessons->filter(fn ($lesson) => $lesson->lesson_type === 'assessment' && str_starts_with($lesson->title, 'Module'))->count() >= $course->blueprintModules->count(),
            'final_exam' => $course->elearningLessons->contains(fn ($lesson) => str_starts_with($lesson->title, 'Final Course Assessment')),
            'final_exam_module_coverage' => $course->blueprintModules->isNotEmpty() && $coveredModules->count() === $course->blueprintModules->count(),
            'final_exam_question_count' => $finalQuestions->count() >= $expectedFinalQuestions[0] && $finalQuestions->count() <= $expectedFinalQuestions[1],
            'question_traceability' => $questions->isNotEmpty() && $questions->every(fn ($question) => filled($question->knowledge_resource_id)),
            'duration_target' => $course->target_learning_minutes === null || $course->estimated_learning_minutes >= (int) round($course->target_learning_minutes * .94),
        ];
        $score = (int) round((collect($checks)->filter()->count() / count($checks)) * 100);
        $mandatory = [
            'approved_blueprint', 'approved_sources_only', 'all_modules_sourced', 'all_lessons_sourced',
            'module_checks', 'final_exam', 'final_exam_module_coverage', 'final_exam_question_count',
            'question_traceability', 'duration_target',
        ];
        $publishable = $score >= 90 && collect($mandatory)->every(fn ($check) => $checks[$check] ?? false);
        return compact('score', 'checks', 'publishable');
    }
}
