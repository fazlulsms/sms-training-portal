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
                'template_name' => 'Course Generator (JSON API) v3',
                'category'      => 'Training AI',
                'description'   => 'Generates a complete training course as structured JSON with duration-based module/lesson limits and per-lesson metadata. Used by the AI Course Generator.',

                'system_prompt' => <<<'PROMPT'
You are an expert curriculum designer and structured JSON generator for SMS Training Academy.
Your sole task is to generate a professional, publication-ready training course and return it as a single valid JSON object.

ABSOLUTE OUTPUT RULES:
- Output ONLY valid JSON — no explanations, no markdown, no code fences, no surrounding text.
- Start your response with { and end with }.
- Every field must be fully populated with professional, specific, workplace-relevant content.
- Learning objectives must use Bloom's Taxonomy action verbs (Identify, Explain, Apply, Analyse, Design, Evaluate).
- All content must be directly relevant to the specified course topic, industry, and audience.
- Do NOT include any pricing information anywhere in the output.

DURATION-BASED COURSE STRUCTURE (CRITICAL — YOU MUST FOLLOW THESE LIMITS):
Derive the exact module and lesson count from the Duration field provided:
- 30 Minutes  → 2–3 Modules, 4–6 Lessons total
- 1 Hour      → 3–4 Modules, 6–10 Lessons total
- 2 Hours     → 4–5 Modules, 8–12 Lessons total
- 4 Hours     → 5–7 Modules, 12–20 Lessons total
- 8 Hours     → 6–9 Modules, 20–30 Lessons total
- 16 Hours    → 8–12 Modules, 30–48 Lessons total
- 40 Hours    → 10–18 Modules, 50–100 Lessons total
For intermediate durations, interpolate proportionally.
Modules must be logically sequenced from foundational to advanced.
The sum of all lesson duration_minutes MUST approximately equal the total course duration in minutes.

LESSON METADATA (REQUIRED FOR EVERY LESSON):
Each lesson object must include:
- lesson_number: integer (sequential within module)
- title: "Lesson X.Y: [Specific descriptive title]"
- lesson_type: ONE of concept | process | skill | compliance | case_study | awareness | technical
  - concept: Explaining what something is, definitions, frameworks, theory
  - process: How something works, step-by-step procedures, workflows
  - skill: Developing practical ability, application, how to perform a task
  - compliance: Requirements, regulations, legal obligations, must-do rules
  - case_study: Learning from real situations, incident analysis, decision-making
  - awareness: Big-picture context, why it matters, importance and consequences
  - technical: Technical specifications, calculations, data interpretation, engineering details
- duration_minutes: integer — estimated time to complete this lesson (realistic, accounts for reading + activities)
- description: 1–2 sentence summary of what this lesson covers
- learning_objectives: array of 2–3 strings using Bloom's Taxonomy verbs
PROMPT,

                'user_prompt_template' => <<<'PROMPT'
Generate a complete, publication-ready professional training course for the following:

{input}

Return ONLY a valid JSON object using exactly this structure:

{
  "course_code": "SMS-ABBREV-LEVEL — where ABBREV is a short uppercase topic code (e.g. ISO14001, QMS, HSE, OSHA, EHS, LEAN, GMP, HACCP, GDPR) and LEVEL is one of: LA (Lead Auditor), IA (Internal Auditor), F (Foundation), P (Practitioner), A (Advanced), M (Management). Example: SMS-ISO14001-IA",
  "category_text": "Training category (e.g. ISO Standards, Health & Safety, Environmental Management, Quality Management).",
  "cpd_hours": 16,
  "course_description": "Comprehensive 3–4 sentence description of what this course covers, who it is for, and what participants will achieve.",
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
    "Specific job role 1",
    "Specific job role 2",
    "Specific job role 3",
    "Specific job role 4",
    "Specific job role 5"
  ],
  "prerequisites": [
    "Prerequisite or prior knowledge 1",
    "Prerequisite or prior knowledge 2"
  ],
  "suggested_delivery_method": "Brief delivery recommendation (e.g. eLearning (Self-Paced), Blended Learning, Instructor-Led).",
  "modules": [
    {
      "module_number": 1,
      "title": "Module 1: [Specific descriptive module title]",
      "duration": "X hours",
      "lessons": [
        {
          "lesson_number": 1,
          "title": "Lesson 1.1: [Specific lesson title — what concept/skill is covered]",
          "lesson_type": "concept",
          "duration_minutes": 20,
          "description": "1–2 sentence summary of what this lesson covers and why it matters.",
          "learning_objectives": [
            "Upon completion, learners will be able to [Bloom's verb] [specific outcome]",
            "Upon completion, learners will be able to [Bloom's verb] [specific outcome]"
          ]
        }
      ]
    }
  ],
  "assessment_plan": "Detailed description of assessments: types (MCQ, practical, written), passing criteria, number of attempts, and any practical components.",
  "certificate_criteria": "Clear criteria for certificate award: minimum score, attendance requirement, and any practical sign-off needed.",
  "certification_information": "Full certification details: certificate title, issuing body (SMS Training Academy), accreditation or recognition, certificate validity period, CPD recognition, how it is issued (digital/physical), and its professional value.",
  "public_summary": "Compelling 150–250 word public website summary written in third person, marketing tone. Cover: what the course is, who it is designed for, key skills gained, why it matters professionally, and the certification awarded. Do not include pricing. End with a call-to-action sentence.",
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

CRITICAL VALIDATION — CHECK BEFORE RETURNING:
1. Count your modules and lessons — they must match the duration-based limits above.
2. Sum all lesson duration_minutes — must approximately equal total course duration in minutes.
3. Each lesson must have lesson_type, duration_minutes (integer), description, and learning_objectives.
4. cpd_hours must match duration (1 hour = 1 CPD, 1 day = 8 CPD).
5. course_code must follow SMS-ABBREV-LEVEL format exactly. No spaces.
6. public_summary must be 150–250 words.
7. faq must include all 8 items with specific answers relevant to this exact course.
8. seo_keywords: 6–8 comma-separated keywords, no quotes.
PROMPT,

                'output_format_instructions' => 'CRITICAL: Return ONLY the JSON object. No text before {. No text after }. No markdown. No code blocks. No explanation. The entire response must be parseable by json_decode().',

                'model_override' => 'gpt-4o-mini',
                'temperature'    => 0.5,
                'max_tokens'     => 6000,
                'is_active'      => true,
                'version_number' => 3,
            ]
        );

        $this->command->info('✓ AI Course Generator template updated to v3 (duration-based limits + lesson metadata)');
    }
}
