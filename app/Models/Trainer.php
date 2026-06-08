<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'organization',
        'email',
        'phone',
        'qualification',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(TrainingSchedule::class, 'trainer_id');
    }
}
