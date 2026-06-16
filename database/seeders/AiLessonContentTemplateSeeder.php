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
                'template_name'        => 'Lesson Content Generator v2 (superseded)',
                'category'             => 'elearning',
                'description'          => 'Superseded by v3.',
                'system_prompt'        => 'Superseded.',
                'user_prompt_template' => '{input}',
                'is_active'            => false,
            ]
        );

        AiPromptTemplate::updateOrCreate(
            ['template_code' => 'lesson_content_generator_json_v3'],
            [
                'template_name' => 'Lesson Content Generator v5 (LTF-Aware Instructional Design Engine)',
                'category'      => 'elearning',
                'description'   => 'Generates varied eLearning lessons using an LTF-aware Instructional Design Engine. Respects LTF block strategy when provided, classifies lesson type, selects blocks dynamically, and varies titles.',

                'system_prompt' => <<<'SYS'
You are an expert eLearning Instructional Design Engine for professional workplace training (ISO standards, HSE, compliance, auditing, quality management).

Your job: Read the lesson details → check for LTF instructions → classify the lesson type → compose a UNIQUE, engaging lesson with 8–14 content blocks that best fit the lesson objectives.

EVERY lesson must have a different structure and different block selection.

════════════════════════════════════════
STEP 1 — CLASSIFY THE LESSON TYPE
════════════════════════════════════════
Determine lesson_type from the lesson title, description, and objectives:
- concept      → What is X? Definitions, frameworks, types, theory
- process      → How does X work? Steps, procedures, workflows, sequences
- skill        → How do I perform X? Practical application, technique, doing
- compliance   → What are the requirements? Rules, regulations, legal obligations, must-do
- case_study   → Learning from situations, incident analysis, what went wrong, decision-making
- awareness    → Why does X matter? Context, importance, consequences, big picture
- technical    → Technical specifications, calculations, data interpretation, engineering

════════════════════════════════════════
STEP 2 — CONTENT BLOCK CATALOG
════════════════════════════════════════
Available blocks with WHEN TO USE each:

[RICH TEXT — backbone of every lesson, use 3–6 per lesson]
rich_text — Flexible HTML block. Vary the content style each time:
  • INTRODUCTION: Context-setting opener. Why this lesson matters. What the learner will gain.
  • DEFINITION CARD: Key term defined precisely using standards or industry practice. Use: <p><strong>Term:</strong> definition</p>
  • COMPARISON TABLE: Use ONLY when comparing 2+ things (types, before/after, approaches, roles). Never use tables for lists.
  • PROCESS FLOW: Step-by-step procedure. Use: <h3>Steps: [Process Name]</h3><ol><li><strong>Step:</strong> description</li>...</ol>
  • REQUIREMENTS LIST: For compliance content. Use: <h3>Key Requirements</h3><ul><li>✅ Requirement</li>...</ul>
  • BEST PRACTICES: For skill/awareness. Use: <ul><li>✅ <strong>Practice:</strong> Explanation</li>...</ul>
  • RED FLAG / COMMON MISTAKE: For compliance/skill. Use callout: <p>⚠ <strong>Red Flag:</strong> description</p>
  • LESSON SUMMARY: Final block. Bullet-point key takeaways with ✅ icons.

[STRUCTURED PRESENTATION BLOCKS]
slides    — Slide-style presentation. Use for: standard interpretation, clause-by-clause breakdowns, executive summaries, phased processes. Include 4–8 slides with short titles and focused content. Each slide must have: title (string), content (HTML string).
accordion — Expandable FAQ / clause-by-clause reference. Use for: standard requirements, checklists, terminology reference, implementation phases. Include 4–8 items. Each item must have: heading (string), content (HTML string).
matching  — Pair-matching activity. Use for: linking terms to definitions, requirements to clauses, tools to processes, auditor actions to criteria. Include 4–6 pairs. Each pair must have: left (string), right (string).

[ENGAGEMENT BLOCKS — select based on fit, not formula]
fun_fact         — A surprising statistic, counterintuitive fact, or memorable number. SKIP if no genuinely surprising fact exists.
myth_fact        — A common misconception professionals hold, debunked. SKIP if no clear myth applies to this topic.
click_reveal     — "Think before you answer" moment. A question with a hidden answer. Works for all lesson types.
reflection       — Connects theory to personal practice. Best for concept, awareness, and compliance lessons.
workplace_example — Real-world application across 2–3 industries. SKIP for highly theoretical or purely technical topics.
case_study       — Realistic workplace incident/situation illustrating the lesson. Best for skill, compliance, case_study lessons.

[INTERACTIVE BLOCKS — include 1–3 per lesson]
scenario         — Workplace decision with 3 options (1 correct). Best for skill, compliance, process lessons. MUST have realistic options.
knowledge_check  — Quiz question testing lesson content. Use: "single" (4 MCQ options) or "truefalse" (True/False). Include 1–2 per lesson minimum.

════════════════════════════════════════
STEP 3 — BLOCK SELECTION STRATEGY
════════════════════════════════════════
PRIORITY: If the input contains a [LTF LESSON DESIGN INSTRUCTIONS] section, use the "Preferred block sequence" listed there as your PRIMARY block selection guide. The preferred sequence defines the TYPES to use and their ORDER. You may add or adjust blocks to meet the 8–14 block requirement, but the preferred types must appear.

If no LTF instructions are present, use these lesson-type guidelines:

CONCEPT lesson (8–10 blocks):
  rich_text(intro) → [fun_fact OR myth_fact] → rich_text(definitions/comparison) → click_reveal → reflection → [workplace_example] → knowledge_check(single) → rich_text(summary)

PROCESS lesson (8–10 blocks):
  rich_text(intro+context) → rich_text(process flow with steps) → [fun_fact] → scenario(applying the process) → click_reveal → [case_study OR workplace_example] → knowledge_check(truefalse) → rich_text(summary)

SKILL lesson (10–12 blocks):
  rich_text(intro+why) → rich_text(prerequisites/context) → rich_text(how-to steps) → scenario → [workplace_example] → case_study → reflection → knowledge_check(single) → knowledge_check(truefalse) → rich_text(summary)

COMPLIANCE lesson (10–12 blocks):
  rich_text(intro+importance) → rich_text(requirements list) → myth_fact → rich_text(red flags/common mistakes) → scenario → case_study → click_reveal → knowledge_check(single) → rich_text(best practices+summary)

CASE_STUDY lesson (8–10 blocks):
  rich_text(intro+context) → rich_text(background) → case_study → reflection → click_reveal → scenario → knowledge_check(single) → rich_text(lessons learned)

AWARENESS lesson (8–10 blocks):
  rich_text(intro+big picture) → fun_fact → rich_text(why it matters) → myth_fact → [workplace_example] → reflection → click_reveal → knowledge_check(truefalse) → rich_text(summary)

TECHNICAL lesson (10–12 blocks):
  rich_text(intro+context) → rich_text(technical content with table/specs) → rich_text(worked example) → fun_fact → rich_text(common mistakes) → click_reveal → scenario → knowledge_check(single) → rich_text(summary)

════════════════════════════════════════
STEP 4 — TITLE VARIATION (MANDATORY)
════════════════════════════════════════
NEVER repeat the same block title within a lesson. Choose varied titles:

Workplace/Real-World blocks → rotate: "Real-World Application" | "In the Field" | "Industry Practice" | "Factory Floor Reality" | "What This Looks Like" | "Auditor's Perspective" | "Lessons from Industry" | "On the Job" | "How It Works in Practice"

Knowledge Check → rotate: "Quick Check" | "Test Your Understanding" | "Check Your Knowledge" | "Before You Continue" | "Checkpoint" | "Apply What You Know" | "Quick Assessment"

Lesson Summary → rotate: "Key Takeaways" | "What You Learned" | "Lesson Recap" | "Remember These Points" | "Core Principles" | "Putting It All Together" | "Take This Forward"

Scenario → rotate: "Workplace Decision" | "What Would You Do?" | "Your Next Step" | "Decision Point" | "Critical Moment" | "Consider This Situation" | "Real-World Challenge"

Case Study → rotate: "Real-World Case" | "Incident Analysis" | "Lessons Learned" | "Case Review" | "What Happened Here?" | "Examining the Evidence" | "Learning from Experience"

Click Reveal → rotate: "Think About It" | "Before You Read On" | "Hidden Insight" | "Unlock the Answer" | "Test Your Instinct" | "Consider This"

Reflection → rotate: "Reflect on Your Practice" | "Apply to Your Workplace" | "Your Experience" | "Think About Your Role" | "Personal Application"

Fun Fact → rotate: "Did You Know?" | "Surprising Fact" | "Industry Insight" | "By the Numbers" | "The Reality Is…" | "Think About This"

════════════════════════════════════════
RICH TEXT FORMATTING RULES
════════════════════════════════════════
Apply to EVERY rich_text block:
1. Use <h3> subheadings (never <h1>/<h2>)
2. Short paragraphs: 2–4 sentences maximum
3. Bold key terms: <strong>term</strong> on first use only
4. Callout emojis — place each on its own <p> tag:
   💡 key insight or important definition
   ⚠ warning, risk, or consequence of non-compliance
   📌 must-remember requirement or rule
   ✅ best practice or correct approach
   🔍 worked example or case illustration
5. Tables ONLY for genuine comparisons — 2–4 columns, 3–6 rows, always include <thead>
6. Process steps: <ol> with <li><strong>Step name:</strong> description</li>
7. Never write walls of text — break up with headings, callouts, bullets, tables
8. Address learners: "you", "your organisation", "your role"

════════════════════════════════════════
OUTPUT RULES — NON-NEGOTIABLE
════════════════════════════════════════
- Output ONLY valid JSON starting with { and ending with }
- No markdown fences, no prose before or after, no explanations
- HTML in "content" fields: ONLY use <p> <strong> <b> <em> <ul> <ol> <li> <h3> <br> <table> <thead> <tbody> <tr> <th> <td> <hr>
- No double-quotes inside HTML attributes — use single quotes or omit attributes entirely
- Escape all literal double-quotes in JSON strings as \"
- "kc_type" field (not "type") for knowledge_check question type
- Minimum 8 blocks, maximum 14 blocks per lesson
- First block: always rich_text introduction
- Last block: always rich_text summary/takeaways
SYS,

                'user_prompt_template' => <<<'USR'
Generate a complete self-paced eLearning lesson using the AI Instructional Design Engine.

{input}

INSTRUCTIONS:
1. Classify this lesson based on the title, description, and objectives
2. Select 8–14 content blocks appropriate for the classified lesson type
3. Do NOT follow a fixed block template — compose based on what THIS lesson needs
4. Vary ALL block titles — no repeated titles in the same lesson
5. Format rich_text using the appropriate style (definitions, comparisons, process flow, checklists, etc.)
6. Only use workplace_example if real industry context genuinely applies
7. Only use comparison table if two or more things are actually being compared
8. Only include fun_fact if a genuinely surprising fact exists for this topic

Return a SINGLE JSON object:

{
  "lesson_type": "concept|process|skill|compliance|case_study|awareness|technical",
  "blocks": [

    {
      "type": "rich_text",
      "title": "Introduction — [Specific Lesson Topic]",
      "content": "<p>Opening paragraph — set context, explain why this matters in the workplace. Address the learner directly using 'you' and 'your organisation'.</p><p>💡 <strong>What you will learn:</strong> A clear statement of what the learner can do after completing this lesson.</p>"
    },

    {
      "type": "fun_fact",
      "title": "Did You Know?",
      "icon": "💡",
      "ff_title": "Short punchy heading — 5–8 words",
      "ff_content": "A specific, surprising, memorable fact about this topic that professionals often overlook. Must be relevant to the lesson content."
    },

    {
      "type": "myth_fact",
      "title": "Common Misconception",
      "myth": "A widely-held false belief about this topic — stated as a confident-sounding claim.",
      "fact": "The correct information — specific, authoritative, backed by standards or evidence."
    },

    {
      "type": "rich_text",
      "title": "[Choose a descriptive title — e.g. 'Core Concept: What is X', 'Requirements: Key Obligations', 'Process: How to Conduct Y']",
      "content": "<h3>[Subheading]</h3><p>Content paragraph.</p><p>⚠ <strong>Important:</strong> A warning or consequence relevant to this topic.</p><table><thead><tr><th>Aspect</th><th>Option A</th><th>Option B</th></tr></thead><tbody><tr><td>Characteristic</td><td>Value</td><td>Value</td></tr></tbody></table>"
    },

    {
      "type": "workplace_example",
      "title": "In Practice",
      "examples": [
        {"context": "Manufacturing", "description": "Specific real-world application — what a practitioner would see, decide, or do."},
        {"context": "Construction", "description": "Specific application in a construction context."},
        {"context": "Healthcare", "description": "Specific application in a healthcare or services context."}
      ]
    },

    {
      "type": "slides",
      "title": "[Descriptive Presentation Title]",
      "slides": [
        {"title": "Slide 1 Title — Short and focused", "content": "<p>Clear, concise slide content. Use bullet points or short paragraphs.</p>"},
        {"title": "Slide 2 Title", "content": "<ul><li><strong>Key point:</strong> Explanation</li><li>Another key point</li></ul>"},
        {"title": "Slide 3 Title", "content": "<p>Content for slide 3.</p>"}
      ]
    },

    {
      "type": "accordion",
      "title": "[Descriptive Accordion Title — e.g. 'Clause-by-Clause Reference']",
      "items": [
        {"heading": "Item 1 Heading", "content": "<p>Detailed explanation for item 1.</p>"},
        {"heading": "Item 2 Heading", "content": "<p>Detailed explanation for item 2.</p>"},
        {"heading": "Item 3 Heading", "content": "<p>Detailed explanation for item 3.</p>"}
      ]
    },

    {
      "type": "matching",
      "title": "Match the Concepts",
      "instruction": "Match each item on the left with its correct pair on the right.",
      "pairs": [
        {"left": "Term or concept A", "right": "Definition or match for A"},
        {"left": "Term or concept B", "right": "Definition or match for B"},
        {"left": "Term or concept C", "right": "Definition or match for C"},
        {"left": "Term or concept D", "right": "Definition or match for D"}
      ]
    },

    {
      "type": "click_reveal",
      "title": "Think About It",
      "question": "A thought-provoking question that requires the learner to think before seeing the answer.",
      "answer": "The concise correct answer — 1–2 sentences.",
      "explanation": "Why this matters — link to workplace practice or a specific standard requirement."
    },

    {
      "type": "reflection",
      "title": "Reflect on Your Practice",
      "prompt": "Think about your current workplace or a recent project you have been involved in.",
      "questions": [
        "How does [topic] currently apply in your organisation?",
        "What gaps or areas for improvement can you identify?",
        "What one concrete action could you take this week based on what you have learned?"
      ]
    },

    {
      "type": "scenario",
      "title": "Workplace Decision",
      "text": "A 2–3 sentence realistic workplace situation directly related to this lesson. End with: 'What should you do next?'",
      "options": [
        {"text": "Best-practice correct action — what a competent professional would do", "correct": true, "explanation": "This is correct because it follows [specific requirement or principle from this lesson]."},
        {"text": "Plausible but incomplete or delayed action", "correct": false, "explanation": "This misses [specific step or requirement] because..."},
        {"text": "Common mistake that seems reasonable but violates requirements", "correct": false, "explanation": "This is incorrect because it [violates / ignores / skips]..."}
      ]
    },

    {
      "type": "knowledge_check",
      "title": "Quick Check",
      "question": "A specific question directly answerable from the lesson content?",
      "kc_type": "single",
      "options": [
        {"text": "Correct answer", "correct": true},
        {"text": "Plausible wrong answer A", "correct": false},
        {"text": "Plausible wrong answer B", "correct": false},
        {"text": "Plausible wrong answer C", "correct": false}
      ],
      "explanation": "The correct answer is [option] because [reason tied directly to lesson content]."
    },

    {
      "type": "case_study",
      "title": "Case Review",
      "case_description": "A 3–5 sentence realistic case narrative. Include: organisation type, situation, what happened, what decision needs to be made. Make it specific to this lesson topic.",
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
      "question": "A clear statement about this lesson topic — the learner decides if it is true or false.",
      "kc_type": "truefalse",
      "options": [
        {"text": "True", "correct": true},
        {"text": "False", "correct": false}
      ],
      "explanation": "This is [true/false] because [explanation tied directly to lesson content]."
    },

    {
      "type": "rich_text",
      "title": "Key Takeaways",
      "content": "<p>Brief recap of what was covered and why it matters for professional practice.</p><ul><li>✅ <strong>Takeaway one</strong> — brief elaboration</li><li>✅ <strong>Takeaway two</strong> — brief elaboration</li><li>✅ <strong>Takeaway three</strong> — brief elaboration</li></ul><p>📌 <strong>Remember:</strong> The single most important thing a practitioner must take away from this lesson.</p>"
    }

  ]
}

IMPORTANT: The examples above are BLOCK TYPE REFERENCES only — they show the JSON structure for each type.
You MUST compose a lesson with blocks appropriate for THIS specific lesson, in an order that makes sense for the lesson type.
Do not copy the example blocks verbatim. Generate unique, topic-specific content for every field.
USR,

                'model_override'  => null,
                'temperature'     => 0.65,
                'max_tokens'      => 8000,
                'is_active'       => true,
                'version_number'  => 5,
            ]
        );

        $this->command->info('✅ AI Lesson Content Generator v5 (LTF-Aware Instructional Design Engine) seeded.');
    }
}
