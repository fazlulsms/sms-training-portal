<?php

namespace App\Support;

use App\Models\Course;
use App\Models\LtfDeliveryMethod;
use App\Models\LtfLearningFramework;
use App\Models\LtfProgramPurpose;
use App\Models\LtfTrainingModel;

class LtfContextBuilder
{
    /**
     * Build context from an existing Course (eager-loads all LTF relations).
     * Safe to call from queue jobs — loads fresh from DB inside handle().
     */
    public static function fromCourse(Course $course): LtfGenerationContext
    {
        $course->loadMissing([
            'ltfLearningFramework',
            'ltfDeliveryMethod',
            'ltfTrainingModel',
            'ltfProgramPurpose',
            'ltfStandards',
            'ltfIndustries',
            'ltfAudiences',
        ]);

        return new LtfGenerationContext(
            frameworkHint:   $course->ltfLearningFramework?->ai_block_hint,
            frameworkName:   $course->ltfLearningFramework?->name,
            deliveryMethod:  $course->ltfDeliveryMethod?->name,
            trainingModel:   $course->ltfTrainingModel?->name,
            programPurpose:  $course->ltfProgramPurpose?->name,
            competencyLevel: $course->ltf_competency_level ?: null,
            standards:       $course->ltfStandards?->pluck('name')->toArray() ?? [],
            industries:      $course->ltfIndustries?->pluck('name')->toArray() ?? [],
            audiences:       $course->ltfAudiences?->pluck('name')->toArray() ?? [],
        );
    }

    /**
     * Build context from validated form data before the Course record is created.
     * Resolves model names from IDs using DB lookups.
     */
    public static function fromFormData(array $data): LtfGenerationContext
    {
        $frameworkId      = $data['ltf_learning_framework_id']  ?? null;
        $deliveryId       = $data['ltf_delivery_method_id']     ?? null;
        $trainingModelId  = $data['ltf_training_model_id']      ?? null;
        $programPurposeId = $data['ltf_program_purpose_id']     ?? null;
        $competencyLevel  = $data['ltf_competency_level']       ?? null;

        $framework = $frameworkId     ? LtfLearningFramework::find($frameworkId) : null;
        $delivery  = $deliveryId      ? LtfDeliveryMethod::find($deliveryId)     : null;
        $model     = $trainingModelId ? LtfTrainingModel::find($trainingModelId) : null;
        $purpose   = $programPurposeId? LtfProgramPurpose::find($programPurposeId): null;

        return new LtfGenerationContext(
            frameworkHint:   $framework?->ai_block_hint,
            frameworkName:   $framework?->name,
            deliveryMethod:  $delivery?->name,
            trainingModel:   $model?->name,
            programPurpose:  $purpose?->name,
            competencyLevel: $competencyLevel ?: null,
            standards:       [],
            industries:      [],
            audiences:       [],
        );
    }

    /**
     * Build context from explicit named components (useful in tests or admin tools).
     */
    public static function fromComponents(
        ?string $frameworkHint   = null,
        ?string $frameworkName   = null,
        ?string $deliveryMethod  = null,
        ?string $trainingModel   = null,
        ?string $programPurpose  = null,
        ?string $competencyLevel = null,
        array   $standards       = [],
        array   $industries      = [],
        array   $audiences       = [],
    ): LtfGenerationContext {
        return new LtfGenerationContext(
            frameworkHint:   $frameworkHint,
            frameworkName:   $frameworkName,
            deliveryMethod:  $deliveryMethod,
            trainingModel:   $trainingModel,
            programPurpose:  $programPurpose,
            competencyLevel: $competencyLevel,
            standards:       $standards,
            industries:      $industries,
            audiences:       $audiences,
        );
    }
}
