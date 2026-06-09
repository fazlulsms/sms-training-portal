<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateProject extends Model
{
    protected $fillable = [
        'project_name', 'company_name', 'address',
        'contact_person', 'contact_designation', 'email', 'phone',
        'status', 'remarks',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(CorporateSession::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(CorporateParticipant::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(CorporateCertificate::class);
    }

    // Computed stats
    public function getTotalTrainingsAttribute(): int
    {
        return $this->sessions()->count();
    }

    public function getTotalParticipantsAttribute(): int
    {
        return $this->participants()->count();
    }

    public function getTotalCertificatesAttribute(): int
    {
        return $this->certificates()->count();
    }

    public static function statusColors(): array
    {
        return [
            'Active'    => '#16a34a',
            'Completed' => '#2563eb',
            'On Hold'   => '#d97706',
            'Cancelled' => '#dc2626',
        ];
    }
}
