<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    protected $table = 'training_attendance';

    protected $fillable = [
        'schedule_id',
        'enrollment_id',
        'session_date',
        'session_label',
        'status',
        'check_in_time',
        'check_out_time',
        'marked_by',
        'remarks',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(TrainingSchedule::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function isPresent(): bool
    {
        return in_array($this->status, ['Present', 'Late']);
    }
}
