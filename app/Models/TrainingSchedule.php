<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    protected $fillable = [
        'course_id', 'trainer_id', 'batch_code', 'training_title',
        'start_date', 'end_date', 'duration', 'currency',
        'physical_fee', 'online_fee', 'discount_fee',
        'venue', 'zoom_link', 'training_mode',
        'max_participants', 'available_seats', 'fee', 'status',
        'is_public', 'schedule_status',
        'registration_deadline', 'time_start', 'time_end',
    ];

    protected $casts = [
        'is_public'             => 'boolean',
        'start_date'            => 'date',
        'end_date'              => 'date',
        'registration_deadline' => 'date',
    ];

    public function getSeatsLeftAttribute(): ?int
    {
        if (is_null($this->available_seats)) return null;
        $enrolled = $this->enrollments()->count();
        return max(0, $this->available_seats - $enrolled);
    }

    public function getIsOpenAttribute(): bool
    {
        if (!$this->is_public) return false;
        if (!in_array($this->schedule_status, ['Upcoming', 'Running'])) return false;
        if ($this->registration_deadline && $this->registration_deadline->isPast()) return false;
        if (!is_null($this->available_seats) && $this->seats_left <= 0) return false;
        return true;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'training_schedule_id');
    }
}