<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateSession extends Model
{
    protected $fillable = [
        'corporate_project_id', 'course_name', 'trainer_name',
        'training_date', 'training_date_end', 'duration', 'venue',
        'target_group', 'description', 'status', 'certificates_generated',
    ];

    protected $casts = [
        'training_date'     => 'date',
        'training_date_end' => 'date',
        'certificates_generated' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(CorporateProject::class, 'corporate_project_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(CorporateParticipant::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(CorporateAttendance::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(CorporateCertificate::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(CorporateEvidence::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(CorporateEvaluation::class);
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendance()->where('status', 'Present')->count();
    }

    public static function statusColors(): array
    {
        return [
            'Planned'   => '#6b7280',
            'Ongoing'   => '#d97706',
            'Completed' => '#16a34a',
            'Cancelled' => '#dc2626',
        ];
    }
}
