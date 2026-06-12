<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplate extends Model
{
    protected $fillable = [
        'template_name',
        'template_code',
        'category',
        'description',
        'system_prompt',
        'user_prompt_template',
        'output_format_instructions',
        'model_override',
        'temperature',
        'max_tokens',
        'is_active',
        'version_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'temperature' => 'float',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function versions()
    {
        return $this->hasMany(AiPromptTemplateVersion::class, 'template_id')->orderByDesc('version_number');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function effectiveModel(): string
    {
        return $this->model_override ?: config('ai.model', 'gpt-4o-mini');
    }

    public function effectiveMaxTokens(): int
    {
        return $this->max_tokens ?: 2000;
    }

    public function effectiveTemperature(): float
    {
        return $this->temperature ?? 0.7;
    }

    /** Build the full system prompt: master + template system prompt. */
    public function fullSystemPrompt(): string
    {
        $master = config('ai.master_system_prompt', '');
        return $master ? $master . "\n\n" . $this->system_prompt : $this->system_prompt;
    }

    /** Replace {input} placeholder in user_prompt_template. */
    public function buildUserPrompt(string $input): string
    {
        $prompt = str_replace('{input}', $input, $this->user_prompt_template);

        if ($this->output_format_instructions) {
            $prompt .= "\n\n" . $this->output_format_instructions;
        }

        return $prompt;
    }

    /** Save current state as a version snapshot before updating. */
    public function snapshotVersion(?int $savedBy = null): void
    {
        AiPromptTemplateVersion::create([
            'template_id'                => $this->id,
            'version_number'             => $this->version_number,
            'template_name'              => $this->template_name,
            'category'                   => $this->category,
            'description'                => $this->description,
            'system_prompt'              => $this->system_prompt,
            'user_prompt_template'       => $this->user_prompt_template,
            'output_format_instructions' => $this->output_format_instructions,
            'model_override'             => $this->model_override,
            'temperature'                => $this->temperature,
            'max_tokens'                 => $this->max_tokens,
            'saved_by'                   => $savedBy,
        ]);
    }
}
