<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonBlock extends Model
{
    protected $fillable = [
        'lesson_id',
        'block_type',
        'title',
        'content',
        'media_path',
        'settings_json',
        'sort_order',
        'status',
        'audio_enabled',
    ];

    protected $casts = [
        'settings_json'        => 'array',
        'sort_order'           => 'integer',
        'certificate_eligible' => 'boolean',
        'audio_enabled'        => 'boolean',
    ];

    // ── Types ──────────────────────────────────────────────
    public const TYPES = [
        // ── Core content ──────────────────────────────────────────
        'rich_text'          => ['label' => 'Rich Text',           'icon' => 'text',      'color' => '#1e3a8a'],
        'accordion'          => ['label' => 'Accordion / FAQ',     'icon' => 'list',      'color' => '#0f766e'],
        'slides'             => ['label' => 'Slide Presentation',  'icon' => 'slides',    'color' => '#1d4ed8'],
        // ── Interactive ───────────────────────────────────────────
        'knowledge_check'    => ['label' => 'Knowledge Check',     'icon' => 'quiz',      'color' => '#d97706'],
        'scenario'           => ['label' => 'Scenario Exercise',   'icon' => 'scenario',  'color' => '#9333ea'],
        'matching'           => ['label' => 'Matching Activity',   'icon' => 'match',     'color' => '#0d9488'],
        'click_reveal'       => ['label' => 'Click to Reveal',     'icon' => 'reveal',    'color' => '#0ea5e9'],
        // ── Engagement ───────────────────────────────────────────
        'fun_fact'           => ['label' => 'Fun Fact',            'icon' => 'funfact',   'color' => '#f59e0b'],
        'reflection'         => ['label' => 'Reflection',          'icon' => 'reflect',   'color' => '#8b5cf6'],
        'myth_fact'          => ['label' => 'Myth vs Fact',        'icon' => 'myth',      'color' => '#ef4444'],
        'workplace_example'  => ['label' => 'Workplace Example',   'icon' => 'workplace', 'color' => '#10b981'],
        'case_study'         => ['label' => 'Case Study',          'icon' => 'case',      'color' => '#6366f1'],
        // ── Media & resources ─────────────────────────────────────
        'video'              => ['label' => 'Video',               'icon' => 'video',     'color' => '#7c3aed'],
        'audio'              => ['label' => 'Audio',               'icon' => 'audio',     'color' => '#0891b2'],
        'image'              => ['label' => 'Image',               'icon' => 'image',     'color' => '#15803d'],
        'gallery'            => ['label' => 'Image Gallery',       'icon' => 'gallery',   'color' => '#065f46'],
        'pdf'                => ['label' => 'PDF Viewer',          'icon' => 'pdf',       'color' => '#b91c1c'],
        'download'           => ['label' => 'Download Resources',  'icon' => 'download',  'color' => '#92400e'],
    ];

    // Types typically suitable for audio — used as a hint in the admin UI only.
    // Actual audio delivery is controlled by the per-block audio_enabled flag.
    public const AUDIO_SUITABLE_TYPES = [
        'rich_text', 'case_study', 'scenario', 'workplace_example', 'fun_fact',
    ];

    public function isAudioEligible(): bool
    {
        return (bool) $this->audio_enabled;
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->block_type]['label'] ?? ucfirst($this->block_type);
    }

    public function getTypeColor(): string
    {
        return self::TYPES[$this->block_type]['color'] ?? '#6b7280';
    }

    /**
     * Decode JSON content (accordion, gallery, slides, knowledge_check, scenario, matching, download)
     */
    public function getDecodedContent(): mixed
    {
        $jsonTypes = [
            'accordion', 'gallery', 'slides', 'knowledge_check', 'scenario', 'matching', 'download',
            'fun_fact', 'reflection', 'click_reveal', 'myth_fact', 'workplace_example', 'case_study',
        ];

        if (in_array($this->block_type, $jsonTypes) && !empty($this->content)) {
            $decoded = json_decode($this->content, true);
            return is_array($decoded) ? $decoded : [];
        }

        return $this->content;
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(ElearningLesson::class, 'lesson_id');
    }
}
