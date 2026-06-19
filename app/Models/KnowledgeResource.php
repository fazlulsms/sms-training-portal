<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KnowledgeResource extends Model
{
    public const RESOURCE_TYPES = [
        'Standard',
        'Guidance Document',
        'SMS Training Note',
        'Procedure / SOP',
        'Policy',
        'Case Study',
        'Audit Scenario',
        'NCR Example',
        'Audit Checklist',
        'Worker Interview Example',
        'Management Interview Example',
        'Presentation / Slides',
        'Template / Form',
        'Video',
        'Image',
        'Other',
    ];

    public const CATEGORIES = [
        'ISO Standards',
        'Social Compliance',
        'Environmental Management',
        'Quality Management',
        'Occupational Health & Safety',
        'SA8000',
        'SLCP',
        'Higg FEM',
        'Grievance Management',
        'Living Wage',
        'ESG & Sustainability',
        'Auditor Development',
        'Internal SMS Procedures',
        'General Training Resources',
    ];

    public const STATUSES = ['draft', 'approved', 'archived'];

    protected $fillable = [
        'title',
        'resource_type',
        'category',
        'subcategory',
        'standard_framework',
        'clause_number',
        'version',
        'difficulty_level',
        'status',
        'notes',
        'learning_objectives',
        'source_references',
        'extracted_text',
        'extraction_status',
        'extraction_error',
        'extracted_at',
        'file_disk',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
        'metadata',
        'approved_at',
        'archived_at',
        'uploaded_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'approved_at' => 'datetime',
            'archived_at' => 'datetime',
            'extracted_at' => 'datetime',
        ];
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function courses() { return $this->belongsToMany(Course::class, 'course_knowledge_resource'); }
    public function lessons() { return $this->belongsToMany(ElearningLesson::class, 'elearning_lesson_knowledge_resource'); }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }
}
