<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElearningLesson extends Model
{
    protected $fillable = [
        'course_id',
        'blueprint_module_id',
        'title',
        'short_description',
        'learning_objectives',
        'lesson_order',
        'video_url',
        'lesson_content',
        'duration_minutes',
        'reading_minutes', 'activity_minutes', 'exercise_minutes', 'quiz_minutes',
        'reflection_minutes', 'estimated_learning_minutes',
        'lesson_type',
        'completion_rule',
        'require_audio_completion',
        'required_passing_score',
        'certificate_eligible',
        'status',
    ];

    protected $casts = [
        'certificate_eligible'    => 'boolean',
        'require_audio_completion'=> 'boolean',
        'lesson_order'            => 'integer',
        'duration_minutes'        => 'integer',
        'required_passing_score'  => 'integer',
    ];

    /**
     * Available lesson types for admin selection.
     */
    public static function lessonTypes(): array
    {
        return [
            'mixed'       => 'Mixed Content',
            'video'       => 'Video Lesson',
            'reading'     => 'Reading / Text',
            'audio'       => 'Audio Lesson',
            'interactive' => 'Interactive Activity',
            'assessment'  => 'Assessment / Quiz Only',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function blueprintModule(): BelongsTo
    {
        return $this->belongsTo(CourseBlueprintModule::class, 'blueprint_module_id');
    }

    public function knowledgeResources()
    {
        return $this->belongsToMany(KnowledgeResource::class, 'elearning_lesson_knowledge_resource');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ElearningLessonResource::class, 'lesson_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(ElearningQuiz::class, 'lesson_id');
    }

    /** Content blocks (new multi-block system) */
    public function blocks(): HasMany
    {
        return $this->hasMany(LessonBlock::class, 'lesson_id')
                    ->where('status', 'active')
                    ->orderBy('sort_order');
    }

    /** All blocks (including inactive) — for admin */
    public function allBlocks(): HasMany
    {
        return $this->hasMany(LessonBlock::class, 'lesson_id')->orderBy('sort_order');
    }

    /** All generated audio for this lesson (block + recap) */
    public function audios(): HasMany
    {
        return $this->hasMany(LessonAudio::class, 'lesson_id');
    }

    /** Ready audio only — the set used for completion enforcement */
    public function readyAudios(): HasMany
    {
        return $this->hasMany(LessonAudio::class, 'lesson_id')->where('status', 'ready');
    }

    /**
     * Whether this lesson has ready audio AND audio completion is required.
     * Controls whether the "Mark Complete" button gate is active.
     */
    public function needsAudioCompletion(): bool
    {
        if (!$this->require_audio_completion) {
            return false;
        }
        return $this->readyAudios()->exists();
    }

    /**
     * True if this lesson uses the new block-based system.
     * Falls back to legacy video_url / lesson_content otherwise.
     */
    public function hasBlocks(): bool
    {
        return $this->allBlocks()->exists();
    }

    /**
     * Completion rules available for admin selection.
     */
    public static function completionRules(): array
    {
        return [
            'manual'     => 'Manual — Participant clicks Mark Complete',
            'pass_quiz'  => 'Pass Quiz — Must pass all quizzes on this lesson',
        ];
    }
}
