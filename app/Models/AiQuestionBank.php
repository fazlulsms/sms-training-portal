<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuestionBank extends Model
{
    protected $table = 'ai_question_bank';
    protected $fillable = [
        'course_id', 'blueprint_module_id', 'lesson_id', 'knowledge_resource_id',
        'question_text', 'fingerprint', 'question_type', 'difficulty', 'options', 'correct_answer',
        'explanation', 'status',
    ];
    protected $casts = ['options' => 'array'];

    public function resource() { return $this->belongsTo(KnowledgeResource::class, 'knowledge_resource_id'); }
    public function lesson() { return $this->belongsTo(ElearningLesson::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function courses() { return $this->belongsToMany(Course::class, 'ai_question_bank_course')->withTimestamps(); }
}
