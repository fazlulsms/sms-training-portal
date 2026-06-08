<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'designation', 'company',
        'course_id', 'course_name', 'training_date',
        'rating', 'feedback', 'photo', 'consent',
        'status', 'admin_notes',
    ];

    protected $casts = [
        'consent' => 'boolean',
        'rating'  => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'featured']);
    }

    public function scopeFeatured($query)
    {
        return $query->where('status', 'featured');
    }

    public function getStarsAttribute(): array
    {
        return range(1, 5);
    }
}
