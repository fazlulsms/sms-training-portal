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
    ];

    protected $casts = [
        'settings_json' => 'array',
        'sort_order'    => 'integer',
        'certificate_eligible' => 'boolean',
    ];

    // ── Types ──────────────────────────────────────────────
    public const TYPES = [
        'rich_text'       => ['label' => 'Rich Text',          'icon' => 'text',     'color' => '#1e3a8a'],
        'accordion'       => ['label' => 'Accordion / FAQ',    'icon' => 'list',     'color' => '#0f766e'],
        'video'           => ['label' => 'Video',              'icon' => 'video',    'color' => '#7c3aed'],
        'audio'           => ['label' => 'Audio',              'icon' => 'audio',    'color' => '#0891b2'],
        'image'           => ['label' => 'Image',              'icon' => 'image',    'color' => '#15803d'],
        'gallery'         => ['label' => 'Image Gallery',      'icon' => 'gallery',  'color' => '#065f46'],
        'pdf'             => ['label' => 'PDF Viewer',         'icon' => 'pdf',      'color' => '#b91c1c'],
        'download'        => ['label' => 'Download Resources', 'icon' => 'download', 'color' => '#92400e'],
        'slides'          => ['label' => 'Slide Presentation', 'icon' => 'slides',   'color' => '#1d4ed8'],
        'knowledge_check' => ['label' => 'Knowledge Check',    'icon' => 'quiz',     'color' => '#d97706'],
        'scenario'        => ['label' => 'Scenario Exercise',  'icon' => 'scenario', 'color' => '#9333ea'],
        'matching'        => ['label' => 'Matching Activity',  'icon' => 'match',    'color' => '#0d9488'],
    ];

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
        $jsonTypes = ['accordion', 'gallery', 'slides', 'knowledge_check', 'scenario', 'matching', 'download'];

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
