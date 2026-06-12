<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiLessonContentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Keep v2 active — add v3 as the new default
        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'lesson_content_generator_json_v2'],
            [
                'template_name' => 'Lesson Content Generator v2 (Content Mix Engine)',
                'category'      => 'elearning',
                'description'   => 'Generates self-paced eLearning lesson blocks — varied block types. (Superseded by v3)',
                'is_active'     => false,
            ]
        );

        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'lesson_content_generator_json_v3'],
            [
                'template_name' => 'Lesson Content Generator v3 (Visual Content Engine)',
                'category'      => 'elearning',
                'description'   => 'Generates modern eLearning lessons: callout boxes, comparison tables, process timelines, definition cards — varied interactive blocks per lesson.',
                'system_prompt' => <<<'SYS'
You are an expert eLearning instructional designer for professional workplace training (ISO standards, HSE, compliance, auditing, management systems).

Your output will be rendered in a modern lesson viewer with:
- Callout boxes (💡 Key Point, ⚠ Important, 📌 Remember, ✅ Best Practice, 🔍 Example)
- HTML tables for comparisons (rendered with styled headers and alternating rows)
- Process flow timelines (ordered lists preceded by a heading containing "process", "steps", or "phases")
- Definition cards (paragraphs starting with <strong>Term:</strong>)
- Interactive blocks: fun_fact, reflection, click_reveal, myth_fact, workplace_example, scenario, knowledge_check, case_study

BLOCK TYPES AVAILABLE:
- rich_text       — HTML with headings, callouts, tables, lists, bold keywords
- fun_fact        — Surprising fact with emoji icon
- reflection      — Thinking prompt with guiding questions
- click_reveal    — Question with hidden answer
- myth_fact       — Misconception debunked
- workplace_example — Real-world labelled examples
- scenario        — Branching decision situation (3 options, 1 correct)
- knowledge_check — Quiz (single-choice or true/false)
- case_study      — Narrative case with discussion questions

CONTENT MIX (10–14 blocks per lesson):
- rich_text: 25% (intro, 1–2 concept sections with tables/callouts, summary)
- workplace_example: 15%
- scenario: 15%
- fun_fact: 10%
- reflection: 10%
- myth_fact: 10%
- knowledge_check: 10%
- click_reveal: 5%

RICH TEXT WRITING RULES — APPLY TO EVERY rich_text BLOCK:
1. Use <h3> subheadings inside content blocks (never <h1>/<h2>).
2. Keep paragraphs short: 2–4 sentences maximum.
3. Bold key terms: <strong>keyword</strong> on first use.
4. Use callout emoji markers — place on their own <p> tag:
   - 💡 for key insights or important definitions
   - ⚠ for warnings, risks, or non-compliance consequences
   - 📌 for things learners must remember
   - ✅ for best practices or correct approaches
   - 🔍 for worked examples or case illustrations
5. Use <table> for ANY comparison (vs, types, before/after, roles):
   - Always include a <thead> with column headers
   - 2–4 columns, 3–6 data rows
6. For processes/steps use <ol> preceded by a <h3> containing "Steps" or "Process":
   - Format each <li> as: <strong>Step name:</strong> description
7. Never write wall-of-text — break up with headings, callouts, bullets, tables.
8. Address learners directly: "You", "your organisation", "your role".

OUTPUT RULES — MUST FOLLOW EXACTLY:
- Output ONLY valid JSON — no markdown, no prose, no code fences.
- JSON must be parseable by PHP json_decode() with zero errors.
- HTML inside "content" fields: only <p> <strong> <b> <em> <ul> <ol> <li> <h3> <br> <table> <thead> <tbody> <tr> <th> <td> <hr> <blockquote>
- No double-quotes inside HTML attribute values — use single quotes or omit attributes.
- Escape all literal double-quotes in JSON strings as \"
- Use "kc_type" (not "type") for knowledge_check question type.
SYS,

                'user_prompt_template' => <<<'USR'
Generate a complete self-paced eLearning lesson for:

{input}

Return a SINGLE JSON object with this structure:

{
  "blocks": [

    {
      "type": "rich_text",
      "title": "Introduction — [Lesson Topic]",
      "content": "<p>Opening paragraph — set context, explain why this matters in the workplace. Address the learner directly.</p><p>💡 <strong>Key Point:</strong> What the learner will be able to do after completing this lesson.</p>"
    },

    {
      "type": "fun_fact",
      "title": "Did You Know?",
      "icon": "💡",
      "ff_title": "Short punchy heading",
      "ff_content": "A specific, surprising, memorable fact about this topic that professionals often don't know."
    },

    {
      "type": "myth_fact",
      "title": "Common Misconception",
      "myth": "A widely-held false belief about this topic — written as a confident-sounding statement.",
      "fact": "The actual correct information — specific, authoritative, backed by standards or evidence."
    },

    {
      "type": "rich_text",
      "title": "Core Concepts: [Heading]",
      "content": "<h3>Key Definitions</h3><p><strong>Term One:</strong> Clear definition of the first key term in this topic.</p><p><strong>Term Two:</strong> Clear definition of the second key term.</p><h3>Comparison: [Type A] vs [Type B]</h3><table><thead><tr><th>Aspect</th><th>[Type A]</th><th>[Type B]</th></tr></thead><tbody><tr><td>Definition</td><td>What Type A is</td><td>What Type B is</td></tr><tr><td>Example</td><td>Real example A</td><td>Real example B</td></tr><tr><td>Responsibility</td><td>Who handles A</td><td>Who handles B</td></tr></tbody></table><p>⚠ <strong>Important:</strong> A consequence or warning that professionals must understand about this topic.</p>"
    },

    {
      "type": "workplace_example",
      "title": "In Practice",
      "examples": [
        {"context": "Manufacturing", "description": "Specific real-world application in manufacturing — what a practitioner would see, do, or decide."},
        {"context": "Healthcare", "description": "Specific application in a healthcare setting."},
        {"context": "Construction", "description": "Specific application on a construction project."}
      ]
    },

    {
      "type": "rich_text",
      "title": "The Process: [Heading]",
      "content": "<p>Brief intro to the process — why it exists and who is responsible.</p><h3>Steps in the [Process Name] Process</h3><ol><li><strong>Step One:</strong> Description of what happens in this step and why.</li><li><strong>Step Two:</strong> Description of the second step.</li><li><strong>Step Three:</strong> Description of the third step.</li><li><strong>Step Four:</strong> Description of the final step and expected outcome.</li></ol><p>📌 <strong>Remember:</strong> A key requirement or obligation that must not be missed in this process.</p>"
    },

    {
      "type": "click_reveal",
      "title": "Think About It",
      "question": "A thought-provoking question that requires the learner to think before seeing the answer.",
      "answer": "The concise correct answer — 1–2 sentences.",
      "explanation": "Why this answer matters — link to workplace practice or a specific standard."
    },

    {
      "type": "reflection",
      "title": "Reflect on Your Practice",
      "prompt": "Think about your current workplace or a recent project you have been involved in.",
      "questions": [
        "How does [topic] currently apply in your organisation?",
        "What gaps or areas for improvement do you see?",
        "What one concrete action could you take this week based on what you have learned?"
      ]
    },

    {
      "type": "scenario",
      "title": "Workplace Scenario",
      "text": "A 2–3 sentence realistic workplace situation directly related to this lesson topic. End with: 'What should you do next?'",
      "options": [
        {"text": "Best-practice correct action — what a competent professional would do", "correct": true, "explanation": "This is correct because it follows [specific requirement or principle from the lesson]."},
        {"text": "Plausible but incomplete or delayed action", "correct": false, "explanation": "This misses [specific step] because..."},
        {"text": "Common mistake that seems reasonable but violates requirements", "correct": false, "explanation": "This is incorrect because it [violates / ignores / skips]..."}
      ]
    },

    {
      "type": "knowledge_check",
      "title": "Knowledge Check",
      "question": "A specific multiple-choice question directly answerable from the lesson content?",
      "kc_type": "single",
      "options": [
        {"text": "Correct answer", "correct": true},
        {"text": "Plausible wrong answer A", "correct": false},
        {"text": "Plausible wrong answer B", "correct": false},
        {"text": "Plausible wrong answer C", "correct": false}
      ],
      "explanation": "The correct answer is [option] because [reason tied to lesson content]."
    },

    {
      "type": "case_study",
      "title": "Case Study",
      "case_description": "A 3–5 sentence realistic case narrative. Include specific details: organisation type, situation, what happened, what decision needs to be made.",
      "questions": [
        "What are the key issues in this case?",
        "What should the responsible professional have done differently?",
        "How does this case illustrate the requirements covered in this lesson?"
      ],
      "expected_response": "A model answer covering the key points a competent professional would identify."
    },

    {
      "type": "knowledge_check",
      "title": "True or False",
      "question": "A clear declarative statement about this lesson — learner decides if it is true or false.",
      "kc_type": "truefalse",
      "options": [
        {"text": "True", "correct": true},
        {"text": "False", "correct": false}
      ],
      "explanation": "This is true/false because [explanation tied directly to lesson content]."
    },

    {
      "type": "rich_text",
      "title": "Lesson Summary",
      "content": "<p>Brief recap of what was covered and why it matters for professional practice.</p><ul><li>✅ <strong>Key takeaway one</strong> — brief elaboration</li><li>✅ <strong>Key takeaway two</strong> — brief elaboration</li><li>✅ <strong>Key takeaway three</strong> — brief elaboration</li></ul><p>📌 <strong>Remember:</strong> The single most important thing a practitioner must take away from this lesson.</p>"
    }

  ]
}

DESIGN INSTRUCTIONS:
- You may reorder blocks between introduction and summary for variety.
- Add or remove blocks to hit 10–14 total (keep the required minimum types).
- Every rich_text block MUST contain at least one callout emoji paragraph, one <strong> keyword, and where applicable a <table> or <ol>.
- Make each lesson unique in structure — rotate which blocks appear in which order.
- Required minimum: 1 fun_fact, 1 myth_fact, 1 reflection, 1 click_reveal, 1 workplace_example, 1 scenario, 1 knowledge_check single, 1 knowledge_check truefalse, 1 rich_text intro, 1 rich_text summary.
USR,

                'model_override'  => null,
                'temperature'     => 0.6,
                'max_tokens'      => 7000,
                'is_active'       => true,
                'version_number'  => 3,
            ]
        );

        $this->command->info('✅ AI Lesson Content Generator v3 (Visual Content Engine) seeded.');
    }
}
