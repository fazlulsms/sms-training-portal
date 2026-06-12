<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiCourseGeneratorTemplateSeeder extends Seeder
{
    public function run(): void
    {
        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'course_generator_json_v1'],
            [
                'template_name' => 'Course Generator (JSON API)',
                'category'      => 'Training AI',
                'description'   => 'Generates a complete training course structure as structured JSON. Used by the AI Course Generator feature. Do not delete — required for course auto-generation.',

                'system_prompt' => <<<'PROMPT'
You are an expert curriculum designer and structured JSON data generator for SMS Training Academy.
Your sole task is to generate professional training course content and return it as a single valid JSON object.

Rules:
- Output ONLY valid JSON — no explanations, no markdown, no code fences, no surrounding text.
- Start your response with { and end with }.
- Every field must be fully populated with professional, specific, workplace-relevant content.
- Learning objectives must use Bloom's Taxonomy action verbs (Identify, Explain, Apply, Analyse, Design, Evaluate).
- Modules must be logically sequenced from foundational to advanced.
- Each module must contain 3–5 lessons with clear, descriptive titles.
- Generate between 4 and 6 modules total.
- Lesson titles must be specific (not generic like "Introduction" alone) — include the concept being taught.
- All content must be directly relevant to the specified course topic and industry.
PROMPT,

                'user_prompt_template' => <<<'PROMPT'
Generate a complete professional training course for the following:

{input}

Return ONLY a valid JSON object using exactly this structure (no extra fields, no missing fields):

{
  "course_description": "Comprehensive 3–4 sentence description of what this course covers, who it is for, and what participants will achieve. Must be professional and suitable for a public course catalogue.",
  "learning_objectives": [
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]"
  ],
  "target_audience": "Specific description of who should attend this training (roles, industries, experience level).",
  "prerequisites": [
    "Prerequisite or prior knowledge 1",
    "Prerequisite or prior knowledge 2"
  ],
  "modules": [
    {
      "module_number": 1,
      "title": "Module 1: [Specific descriptive title]",
      "duration": "X hours",
      "lessons": [
        {"lesson_number": 1, "title": "Lesson 1.1: [Specific lesson title — what concept is covered]"},
        {"lesson_number": 2, "title": "Lesson 1.2: [Specific lesson title]"},
        {"lesson_number": 3, "title": "Lesson 1.3: [Specific lesson title]"}
      ]
    }
  ],
  "assessment_plan": "Detailed description of how participants will be assessed: types of assessment (MCQ, practical, written), passing criteria, number of attempts allowed, and any practical components.",
  "certificate_criteria": "Clear statement of what participants must achieve to receive a certificate: minimum score, attendance requirement, and any practical sign-off needed.",
  "public_summary": "2–3 sentence compelling summary for the public website course listing page. Focus on benefits and outcomes for the learner.",
  "seo_title": "SEO-optimised page title under 60 characters",
  "seo_meta_description": "SEO meta description summarising the course for search engines, under 160 characters, including the course name and key benefit."
}
PROMPT,

                'output_format_instructions' => 'CRITICAL: Return ONLY the JSON object. No text before {. No text after }. No markdown. No code blocks. No explanation. The entire response must be parseable by json_decode().',

                'model_override' => 'gpt-4o-mini',
                'temperature'    => 0.55,
                'max_tokens'     => 3500,
                'is_active'      => true,
                'version_number' => 1,
            ]
        );

        $this->command->info('✓ AI Course Generator JSON template seeded (course_generator_json_v1)');
    }
}
