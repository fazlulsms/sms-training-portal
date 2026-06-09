<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateAttendance extends Model
{
    protected $table = 'corporate_attendance';

    protected $fillable = [
        'corporate_session_id', 'corporate_participant_id', 'status', 'remarks',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CorporateSession::class, 'corporate_session_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(CorporateParticipant::class, 'corporate_participant_id');
    }
}
