<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiTrainerProfileTemplateSeeder extends Seeder
{
    public function run(): void
    {
        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'trainer_profile_generator_v1'],
            [
                'template_name'=> 'Trainer Profile Generator v1',
                'description' => 'Generates a professional trainer profile from uploaded CV, resume, or portfolio documents.',
                'template_code'=> 'trainer_profile_generator_v1',
                'category'    => 'trainer',
                'is_active'   => true,
                'version_number'=> 1,

                'system_prompt' => <<<'SYSTEM'
You are an expert professional profile writer specialising in training, consulting, and adult education sectors. Your role is to extract key professional information from provided documents (CVs, resumes, LinkedIn exports, bios, certificates) and produce a structured, polished trainer profile in JSON format.

RULES:
- Only include information that is clearly stated or strongly implied in the source documents
- Do NOT invent qualifications, certifications, clients, or years of experience
- Write the professional_bio in third person, 150-250 words, professional but engaging
- All array fields must be proper arrays of strings
- Output ONLY valid JSON — no markdown fences, no extra text
- If a field cannot be determined from the documents, use null for strings or [] for arrays
SYSTEM,

                'user_prompt_template' => <<<'USER'
Based on the following trainer information and documents, generate a comprehensive professional trainer profile JSON.

{input}

Return ONLY a valid JSON object with exactly these fields:

{
  "designation": "Most recent or most relevant job title",
  "organization": "Current or most recent organization",
  "qualification": "Highest or most relevant academic qualification (e.g. MBA, MSc Engineering)",
  "years_experience": "e.g. 15+ years or 12 years",
  "professional_bio": "Third-person professional biography, 150-250 words. Highlight expertise, industries served, key achievements, and training philosophy.",
  "expertise_areas": ["Area 1", "Area 2", "..."],
  "certifications": ["Cert 1 (Issuing Body)", "Cert 2 (Issuing Body)", "..."],
  "professional_highlights": ["Key achievement or credential 1", "Key achievement or credential 2", "..."],
  "industries_served": ["Industry 1", "Industry 2", "..."],
  "countries_covered": ["Country 1", "Country 2", "..."],
  "languages_spoken": ["Language 1", "Language 2"],
  "training_specializations": ["Training topic 1", "Training topic 2", "..."],
  "audit_specializations": ["Audit/assessment topic 1", "..."],
  "seo_title": "SEO-optimised page title for this trainer (60-70 chars)",
  "seo_description": "Meta description 150-160 chars, includes trainer name, expertise, and location if known",
  "seo_keywords": "comma-separated keywords: trainer name, specializations, industry, location",
  "confidence_notes": "Brief note on data quality — e.g. 'All fields extracted from comprehensive CV' or 'Bio and certifications confirmed; industries inferred'"
}
USER,

                'model_override' => null,
                'temperature'    => 0.4,
                'max_tokens'     => 3000,
            ]
        );

        $this->command->info('Trainer Profile template seeded: trainer_profile_generator_v1');
    }
}
