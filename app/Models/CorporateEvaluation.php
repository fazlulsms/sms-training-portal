<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateEvaluation extends Model
{
    protected $fillable = [
        'corporate_session_id', 'corporate_participant_id',
        'evaluator_name', 'feedback_score', 'comments', 'effectiveness_notes',
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
