<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateEvidence extends Model
{
    protected $table = 'corporate_evidences';

    protected $fillable = [
        'corporate_session_id', 'type', 'file_path', 'original_name', 'caption',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CorporateSession::class, 'corporate_session_id');
    }

    public function isImage(): bool
    {
        return str_starts_with(mime_content_type(storage_path('app/public/' . $this->file_path)) ?? '', 'image/');
    }

    public static function types(): array
    {
        return ['Training Photo', 'Group Photo', 'Presentation', 'Document', 'Other'];
    }
}
