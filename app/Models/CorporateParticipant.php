<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateParticipant extends Model
{
    protected $fillable = [
        'corporate_project_id', 'corporate_session_id',
        'participant_name', 'employee_id', 'position', 'department',
        'email', 'contact_number',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(CorporateProject::class, 'corporate_project_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CorporateSession::class, 'corporate_session_id');
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(CorporateAttendance::class, 'corporate_participant_id')
                    ->latestOfMany('id');
    }

    public function attendanceForSession(int $sessionId): HasOne
    {
        return $this->hasOne(CorporateAttendance::class, 'corporate_participant_id')
                    ->where('corporate_session_id', $sessionId);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(CorporateCertificate::class, 'corporate_participant_id');
    }

    public function certificateForSession(int $sessionId): HasOne
    {
        return $this->hasOne(CorporateCertificate::class, 'corporate_participant_id')
                    ->where('corporate_session_id', $sessionId);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(CorporateEvaluation::class);
    }
}
