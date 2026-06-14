<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'company',
        'designation',
        'country',
        'is_active',
        'last_login_at',
        'department',
        'linkedin_url',
        'preferred_language',
        'bio',
        'photo_path',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // ── Profile photo accessor ────────────────────────────

    public function photoUrl(): string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : '';
    }

    public function initials(): string
    {
        $parts = explode(' ', trim($this->name ?? 'U'));
        $first = strtoupper(substr($parts[0] ?? 'U', 0, 1));
        $last  = count($parts) > 1 ? strtoupper(substr(end($parts), 0, 1)) : '';
        return $first . $last;
    }

    // ── Role helpers ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function roleBadgeClass(): string
    {
        return match($this->role) {
            'admin'       => 'badge-admin',
            'trainer'     => 'badge-trainer',
            'participant' => 'badge-participant',
            default       => 'badge-secondary',
        };
    }

    // ── Relationships ─────────────────────────────────────

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'user_id');
    }

    public function elearningEnrollments()
    {
        return $this->hasMany(ElearningEnrollment::class, 'user_id');
    }
}
