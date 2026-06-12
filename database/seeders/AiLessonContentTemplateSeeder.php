<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiLessonContentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'lesson_content_generator_json_v1'],
            [
                'template_name' => 'Lesson Content Generator v1',
                'category'      => 'elearning',
                'description'   => 'Generates complete self-paced eLearning lesson content blocks (intro, sections, example, scenario, knowledge checks, summary) from lesson context.',
                'system_prompt' => <<<'SYS'
You are an expert eLearning instructional designer specialising in professional certification and workplace training programs (ISO standards, HSE, compliance, auditing, management systems).

Your task is to generate complete, engaging, self-paced lesson content for adult professional learners.

OUTPUT RULES — MUST FOLLOW EXACTLY:
- Output ONLY valid JSON. No markdown, no prose, no code fences before or after.
- The JSON must be parseable by PHP json_decode() with zero errors.
- All HTML inside JSON string values may only use: <p> <b> <strong> <i> <em> <ul> <ol> <li> <h3> <br>
- Never use double-quotes inside HTML attribute values within JSON strings.
- Escape all literal double-quotes inside JSON string values as \"

CONTENT RULES:
- Be specific to the lesson topic — no filler, no generic text.
- Write short paragraphs (3–5 sentences each).
- Address learners directly in exercises ("You are a ...", "What would you do?").
- Include real workplace examples tied to the course topic.
- Scenario: one clearly correct option, two plausible-but-wrong options.
- Knowledge checks: one multiple-choice (type "single", 4 options) and one true/false (type "truefalse").
- All quiz questions must be directly answerable from the lesson content.
- Match word count target to the course level provided.
- Main sections: generate 2–4 sections sized to hit the word count target.
SYS,

                'user_prompt_template' => <<<'USR'
Generate complete self-paced eLearning lesson content for:

{input}

Return a SINGLE JSON object with EXACTLY this structure (no extra keys):

{
  "introduction": {
    "title": "Introduction",
    "html": "<p>Opening paragraph — set context and explain why this topic matters in the workplace.</p><p>Second paragraph — what the learner will be able to do after this lesson.</p>"
  },
  "main_sections": [
    {
      "heading": "First Section Title",
      "html": "<p>First paragraph of content.</p><ul><li>Key point one</li><li>Key point two</li><li>Key point three</li></ul>"
    },
    {
      "heading": "Second Section Title",
      "html": "<p>Content here...</p>"
    }
  ],
  "practical_example": {
    "title": "Practical Example",
    "html": "<p><b>Scenario:</b> Describe a real workplace situation relevant to the lesson topic.</p><p>Explain what a practitioner would observe, do, or decide — and why it matters.</p>"
  },
  "scenario": {
    "title": "Scenario Exercise",
    "text": "Describe a realistic 2–3 sentence workplace situation. End with a clear decision question: 'What should you do next?'",
    "options": [
      {"text": "Best-practice correct action — what a competent professional would do", "correct": true, "explanation": "This is correct because it directly addresses [reason tied to lesson content]."},
      {"text": "Plausible but incomplete or delayed action", "correct": false, "explanation": "This misses [specific requirement or step] because..."},
      {"text": "Common mistake or shortcut that seems reasonable but is wrong", "correct": false, "explanation": "This is incorrect because it [violates / ignores / skips]..."}
    ]
  },
  "knowledge_checks": [
    {
      "title": "Knowledge Check",
      "question": "A clear, specific multiple-choice question about lesson content?",
      "type": "single",
      "options": [
        {"text": "Correct answer — clearly right based on lesson", "correct": true},
        {"text": "Plausible wrong answer A", "correct": false},
        {"text": "Plausible wrong answer B", "correct": false},
        {"text": "Plausible wrong answer C", "correct": false}
      ],
      "explanation": "The correct answer is [option text] because [explanation tied directly to lesson content]."
    },
    {
      "title": "True or False",
      "question": "A statement about the lesson content — written as a clear declarative sentence.",
      "type": "truefalse",
      "options": [
        {"text": "True", "correct": true},
        {"text": "False", "correct": false}
      ],
      "explanation": "This statement is true/false because [explanation tied directly to lesson content]."
    }
  ],
  "summary": {
    "title": "Lesson Summary",
    "html": "<p>Brief recap: what was covered in this lesson and why it matters.</p><ul><li>Key takeaway one</li><li>Key takeaway two</li><li>Key takeaway three</li></ul>"
  }
}
USR,

                'model_override'  => null,
                'temperature'     => 0.45,
                'max_tokens'      => 4000,
                'is_active'       => true,
                'version_number'  => 1,
            ]
        );

        $this->command->info('✅ AI Lesson Content Generator template seeded (lesson_content_generator_json_v1).');
    }
}
