<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiLessonContentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'lesson_content_generator_json_v2'],
            [
                'template_name' => 'Lesson Content Generator v2 (Content Mix Engine)',
                'category'      => 'elearning',
                'description'   => 'Generates self-paced eLearning lesson blocks using a content mix engine — varied block types per lesson to avoid monotony.',
                'system_prompt' => <<<'SYS'
You are an expert eLearning instructional designer specialising in professional certification and workplace training programs (ISO standards, HSE, compliance, auditing, management systems).

Your task is to generate a complete, engaging, self-paced lesson as an array of content blocks for adult professional learners.

BLOCK TYPES AVAILABLE:
- rich_text       — HTML content block (intro, explanation, summary, concepts)
- fun_fact        — Surprising or memorable fact with emoji icon
- reflection      — Prompt for personal thinking with guiding questions
- click_reveal    — Question with hidden answer the learner reveals
- myth_fact       — Common misconception debunked with the real fact
- workplace_example — Real-world application examples with context labels
- scenario        — Branching workplace situation with 3 options (1 correct)
- knowledge_check — Quiz question (single or truefalse type)
- case_study      — Narrative case with discussion questions and expected response

CONTENT MIX TARGET (across all blocks in the lesson):
- rich_text:          20% (intro + 1-2 concept sections + summary)
- workplace_example:  15%
- scenario:           15%
- reflection:         10%
- fun_fact:           10%
- myth_fact:          10%
- knowledge_check:    10%
- click_reveal:       10%

DESIGN RULES — MUST FOLLOW:
1. Generate 10–14 blocks per lesson. Every lesson MUST feel different — vary the order and mix.
2. ALWAYS start with a rich_text introduction block.
3. ALWAYS end with a rich_text summary block.
4. Between intro and summary, vary block types — never place two rich_text blocks in a row.
5. Include at least: 2 knowledge_check blocks (one "single", one "truefalse"), 1 scenario, 1 workplace_example, 1 reflection, 1 fun_fact, 1 myth_fact, 1 click_reveal.
6. Match content depth and word count to the learner level provided.
7. Be specific to the lesson topic — no filler, no generic text.
8. Address learners directly: "You are...", "What would you do?", "Consider your workplace..."

OUTPUT RULES — MUST FOLLOW EXACTLY:
- Output ONLY valid JSON. No markdown, no prose, no code fences.
- The JSON must be parseable by PHP json_decode() with zero errors.
- HTML inside rich_text "content" may only use: <p> <b> <strong> <i> <em> <ul> <ol> <li> <h3> <br>
- Never use double-quotes inside HTML attributes within JSON strings.
- Escape all literal double-quotes inside JSON string values as \"
- Use "kc_type" (not "type") for knowledge_check question type to avoid field collision.
SYS,

                'user_prompt_template' => <<<'USR'
Generate a complete self-paced eLearning lesson for:

{input}

Return a SINGLE JSON object with EXACTLY this structure:

{
  "blocks": [

    {
      "type": "rich_text",
      "title": "Introduction — [Lesson Topic]",
      "content": "<p>Opening paragraph setting context and why this matters in professional practice.</p><p>What the learner will be able to do after completing this lesson.</p>"
    },

    {
      "type": "fun_fact",
      "title": "Did You Know?",
      "icon": "💡",
      "ff_title": "Short punchy heading for this fact",
      "ff_content": "A specific, surprising, memorable fact directly related to this lesson topic that professionals often don't know."
    },

    {
      "type": "myth_fact",
      "title": "Common Misconception",
      "myth": "A widely-held false belief about this topic written as a confident-sounding statement.",
      "fact": "The actual correct fact that refutes the myth — specific, authoritative, tied to standards or evidence."
    },

    {
      "type": "rich_text",
      "title": "Core Concept: [Heading]",
      "content": "<p>Explanation of a key concept.</p><ul><li>Point one</li><li>Point two</li><li>Point three</li></ul>"
    },

    {
      "type": "workplace_example",
      "title": "In Practice",
      "examples": [
        {"context": "Manufacturing", "description": "Specific example of how this applies in a manufacturing workplace."},
        {"context": "Healthcare", "description": "Specific example in a healthcare setting."},
        {"context": "Construction", "description": "Specific example in a construction environment."}
      ]
    },

    {
      "type": "click_reveal",
      "title": "Think About It",
      "question": "A thought-provoking question about the lesson topic that requires learner to think before revealing the answer.",
      "answer": "The concise correct answer — 1–2 sentences.",
      "explanation": "Why this answer matters — link to workplace practice or standard."
    },

    {
      "type": "rich_text",
      "title": "Key Requirements: [Heading]",
      "content": "<p>Explanation of requirements, steps, or framework.</p><ol><li>Step or requirement one</li><li>Step or requirement two</li></ol>"
    },

    {
      "type": "reflection",
      "title": "Reflect on Your Practice",
      "prompt": "Think about your own workplace or a recent project you have been involved in.",
      "questions": [
        "How does [topic] currently apply in your organisation?",
        "What gaps or improvement opportunities do you see?",
        "What one action could you take this week to apply what you have learned?"
      ]
    },

    {
      "type": "scenario",
      "title": "Workplace Scenario",
      "text": "A 2–3 sentence realistic workplace situation directly related to this lesson topic. End with: 'What should you do next?'",
      "options": [
        {"text": "Best-practice correct action — what a competent professional would do", "correct": true, "explanation": "This is correct because it directly follows [requirement or principle from the lesson]."},
        {"text": "Plausible but incomplete or delayed action", "correct": false, "explanation": "This misses [specific requirement or step] because..."},
        {"text": "Common mistake that seems reasonable but is wrong", "correct": false, "explanation": "This is incorrect because it [violates / ignores / skips]..."}
      ]
    },

    {
      "type": "knowledge_check",
      "title": "Knowledge Check",
      "question": "A specific multiple-choice question directly answerable from the lesson content above?",
      "kc_type": "single",
      "options": [
        {"text": "Correct answer", "correct": true},
        {"text": "Plausible wrong answer A", "correct": false},
        {"text": "Plausible wrong answer B", "correct": false},
        {"text": "Plausible wrong answer C", "correct": false}
      ],
      "explanation": "The correct answer is [option text] because [reason tied to lesson content]."
    },

    {
      "type": "case_study",
      "title": "Case Study",
      "case_description": "A 3–5 sentence real-world or realistic case narrative involving this lesson topic. Include specific details — organisation type, situation, what went wrong or what decision needs to be made.",
      "questions": [
        "What are the key issues in this case?",
        "What should the responsible person have done differently?",
        "How does this case relate to the requirements you have studied?"
      ],
      "expected_response": "A concise model answer covering the key points a competent professional would identify from this lesson."
    },

    {
      "type": "knowledge_check",
      "title": "True or False",
      "question": "A clear declarative statement about this lesson topic — written so learner must decide if it is true or false.",
      "kc_type": "truefalse",
      "options": [
        {"text": "True", "correct": true},
        {"text": "False", "correct": false}
      ],
      "explanation": "This statement is true/false because [explanation tied directly to lesson content]."
    },

    {
      "type": "rich_text",
      "title": "Lesson Summary",
      "content": "<p>Brief recap of what was covered and why it matters for professional practice.</p><ul><li>Key takeaway one</li><li>Key takeaway two</li><li>Key takeaway three</li></ul>"
    }

  ]
}

IMPORTANT: You may adjust the order of blocks between introduction and summary, add or remove blocks (except you must keep intro, summary, and at least 1 of each: knowledge_check single, knowledge_check truefalse, scenario, workplace_example, reflection, fun_fact, myth_fact, click_reveal). Make every lesson feel different — vary the sequence to create an engaging learning journey.
USR,

                'model_override'  => null,
                'temperature'     => 0.55,
                'max_tokens'      => 6000,
                'is_active'       => true,
                'version_number'  => 2,
            ]
        );

        $this->command->info('✅ AI Lesson Content Generator v2 template seeded (lesson_content_generator_json_v2).');
    }
}
