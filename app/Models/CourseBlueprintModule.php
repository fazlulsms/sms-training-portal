<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBlueprintModule extends Model
{
    protected $fillable = ['course_id', 'title', 'learning_outcomes', 'module_order', 'estimated_minutes'];

    public function course() { return $this->belongsTo(Course::class); }
    public function lessons() { return $this->hasMany(ElearningLesson::class, 'blueprint_module_id'); }
    public function knowledgeResources() { return $this->belongsToMany(KnowledgeResource::class, 'course_blueprint_module_knowledge_resource'); }
}
