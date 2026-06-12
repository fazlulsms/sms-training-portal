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
                'description'   => 'Generates a 90–95% complete training course as structured JSON. Used by the AI Course Generator feature. Do not delete — required for course auto-generation.',

                'system_prompt' => <<<'PROMPT'
You are an expert curriculum designer and structured JSON generator for SMS Training Academy.
Your sole task is to generate a professional, publication-ready training course and return it as a single valid JSON object.

Rules:
- Output ONLY valid JSON — no explanations, no markdown, no code fences, no surrounding text.
- Start your response with { and end with }.
- Every field must be fully populated with professional, specific, workplace-relevant content.
- Learning objectives must use Bloom's Taxonomy action verbs (Identify, Explain, Apply, Analyse, Design, Evaluate).
- Modules must be logically sequenced from foundational to advanced. Generate 4–6 modules total.
- Each module must contain 3–5 lessons with specific, descriptive titles (not generic like "Introduction" alone).
- All content must be directly relevant to the specified course topic, industry, and audience.
- Do NOT include any pricing information (fees, costs, discounts) anywhere in the output.
PROMPT,

                'user_prompt_template' => <<<'PROMPT'
Generate a complete, publication-ready professional training course for the following:

{input}

Return ONLY a valid JSON object using exactly this structure:

{
  "course_code": "SMS-ABBREV-LEVEL — where ABBREV is a short uppercase code for the topic (e.g. ISO14001, QMS, HSE, OSHA, EHS, LEAN, GMP, HACCP, GDPR) and LEVEL is one of: LA (Lead Auditor), IA (Internal Auditor), F (Foundation), P (Practitioner), A (Advanced), M (Management). Example: SMS-ISO14001-IA",
  "category_text": "The training category this course belongs to (e.g. ISO Standards, Health & Safety, Environmental Management, Quality Management, Leadership, HR & Compliance). Use a well-known category name.",
  "cpd_hours": 16,
  "course_description": "Comprehensive 3–4 sentence description of what this course covers, who it is for, and what participants will achieve. Professional and suitable for a public course catalogue.",
  "learning_objectives": [
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]",
    "Upon completion, participants will be able to [Bloom's verb] [specific outcome]"
  ],
  "target_audience": "One-sentence description of who should attend (roles, industries, experience level).",
  "target_market": [
    "Specific job role or title 1",
    "Specific job role or title 2",
    "Specific job role or title 3",
    "Specific job role or title 4",
    "Specific job role or title 5"
  ],
  "prerequisites": [
    "Prerequisite or prior knowledge required 1",
    "Prerequisite or prior knowledge required 2"
  ],
  "suggested_delivery_method": "Brief delivery recommendation (e.g. Instructor-Led (Classroom or Virtual), Blended Learning, eLearning (Self-Paced)). Informational only.",
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
  "assessment_plan": "Detailed description of assessments: types (MCQ, practical, written), passing criteria, number of attempts, and any practical components.",
  "certificate_criteria": "Clear criteria for certificate award: minimum score, attendance requirement, and any practical sign-off needed.",
  "certification_information": "Full certification details: certificate title, issuing body (SMS Training Academy), accreditation or recognition (if applicable), certificate validity period, CPD recognition, how it is issued (digital/physical), and its professional value.",
  "public_summary": "Compelling 150–250 word public website summary written in third person, marketing tone. Cover: what the course is, who it is designed for, key skills gained, why it matters professionally, and the certification awarded. Do not include any pricing. End with a call-to-action sentence.",
  "faq": [
    {"question": "Who should attend this course?", "answer": "Detailed answer."},
    {"question": "What certification will I receive upon completion?", "answer": "Detailed answer."},
    {"question": "What are the entry requirements or prerequisites?", "answer": "Detailed answer."},
    {"question": "How is this course assessed?", "answer": "Detailed answer."},
    {"question": "Is this course accredited or formally recognised?", "answer": "Detailed answer."},
    {"question": "How long do I have to complete the course?", "answer": "Detailed answer."},
    {"question": "Can I attend this course online or virtually?", "answer": "Detailed answer."},
    {"question": "What career benefits does this certification provide?", "answer": "Detailed answer."}
  ],
  "seo_title": "SEO-optimised page title under 60 characters including the course name",
  "seo_meta_description": "SEO meta description under 160 characters: course name, key benefit, and target audience.",
  "seo_keywords": "keyword1, keyword2, keyword3, keyword4, keyword5, keyword6, keyword7"
}

Important rules for specific fields:
- cpd_hours: integer. Derive from duration (1 hour = 1 CPD, 1 day = 8 CPD). Must match the course duration.
- course_code: must follow SMS-ABBREV-LEVEL format exactly. No spaces.
- public_summary: must be 150–250 words. Count carefully.
- faq: include all 8 items with specific, helpful answers relevant to this exact course.
- target_market: list 4–6 specific job titles (not generic phrases).
- seo_keywords: 6–8 comma-separated keywords, no quotes around them.
PROMPT,

                'output_format_instructions' => 'CRITICAL: Return ONLY the JSON object. No text before {. No text after }. No markdown. No code blocks. No explanation. The entire response must be parseable by json_decode().',

                'model_override' => 'gpt-4o-mini',
                'temperature'    => 0.55,
                'max_tokens'     => 4500,
                'is_active'      => true,
                'version_number' => 2,
            ]
        );

        $this->command->info('✓ AI Course Generator JSON template updated to v2 (course_generator_json_v1)');
    }
}
