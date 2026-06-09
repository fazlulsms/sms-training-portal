<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateCertificate extends Model
{
    protected $fillable = [
        'corporate_project_id', 'corporate_session_id', 'corporate_participant_id',
        'certificate_number', 'issue_date', 'pdf_path',
    ];

    protected $casts = ['issue_date' => 'date'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(CorporateProject::class, 'corporate_project_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CorporateSession::class, 'corporate_session_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(CorporateParticipant::class, 'corporate_participant_id');
    }

    /** Generate next sequential certificate number: SMS-TR-YYYY-XXXXX */
    public static function generateNumber(): string
    {
        $year  = now()->year;
        $last  = static::whereYear('created_at', $year)
                        ->orderByDesc('id')
                        ->value('certificate_number');

        $seq = 1;
        if ($last) {
            $seq = (int) substr($last, -5) + 1;
        }

        return 'SMS-TR-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /** Safe filename for PDF/ZIP download */
    public function getSafeFilenameAttribute(): string
    {
        $name = str_replace([' ', '/', '\\', '.', ','], '_', $this->participant->participant_name ?? 'Participant');
        return $this->certificate_number . '_' . $name . '.pdf';
    }
}
