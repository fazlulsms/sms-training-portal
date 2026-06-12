<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplateVersion extends Model
{
    protected $fillable = [
        'template_id',
        'version_number',
        'template_name',
        'category',
        'description',
        'system_prompt',
        'user_prompt_template',
        'output_format_instructions',
        'model_override',
        'temperature',
        'max_tokens',
        'saved_by',
    ];

    protected $casts = [
        'temperature' => 'float',
    ];

    public function template()
    {
        return $this->belongsTo(AiPromptTemplate::class, 'template_id');
    }

    public function savedBy()
    {
        return $this->belongsTo(User::class, 'saved_by');
    }
}
