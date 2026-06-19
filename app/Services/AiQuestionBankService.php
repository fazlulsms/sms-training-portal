<?php

namespace App\Services;

use App\Models\AiQuestionBank;

class AiQuestionBankService
{
    public function store(array $attributes): ?AiQuestionBank
    {
        $fingerprint = hash('sha256', $this->normalize($attributes['question_text']).'|'.($attributes['knowledge_resource_id'] ?? ''));
        $existing = AiQuestionBank::where('knowledge_resource_id', $attributes['knowledge_resource_id'])
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($existing) {
            return $existing->status === 'approved' ? $existing : null;
        }

        return AiQuestionBank::create([...$attributes, 'fingerprint' => $fingerprint]);
    }

    private function normalize(string $text): string
    {
        return preg_replace('/[^a-z0-9]+/', ' ', strtolower(trim($text)));
    }
}
