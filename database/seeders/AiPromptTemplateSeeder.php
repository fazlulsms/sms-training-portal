<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiPromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            // ─────────────────────────────────────────────────────
            // A. Course Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'Course Generator',
                'template_code'  => 'course_generator_v1',
                'category'       => 'Training AI',
                'description'    => 'Generate a complete professional training course outline including modules, learning objectives, content overview, and assessment strategy.',
                'system_prompt'  => <<<'PROMPT'
You are an expert curriculum designer specialising in professional and compliance training programmes.
When generating course content, always:
- Structure content using ADDIE or similar instructional design models
- Write clear, measurable learning objectives using Bloom's Taxonomy action verbs
- Include practical workplace activities and real-world application examples
- Align content to relevant international standards (ISO, ILO, GRI, SLCP, Higg)
- Suggest pre-course prerequisites and target audience clearly
- Recommend appropriate assessment methods (MCQ, practical observation, written assessment)
- Indicate recommended delivery method: classroom, online, or blended
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Create a complete professional training course for:

{input}

Please provide:

1. COURSE OVERVIEW
   - Course Title
   - Target Audience
   - Prerequisites
   - Duration (recommended)
   - Delivery Method
   - Certification/Outcome

2. LEARNING OBJECTIVES (5–8 measurable objectives using Bloom's action verbs)

3. COURSE OUTLINE (Modules with sub-topics)
   - Module 1: [Title]
     * Sub-topic 1.1
     * Sub-topic 1.2
     * Key Activity
   (Repeat for each module)

4. ASSESSMENT STRATEGY
   - Assessment type
   - Passing criteria
   - Practical components

5. RECOMMENDED RESOURCES & REFERENCE STANDARDS
PROMPT,
                'output_format_instructions' => 'Use clear headings and numbered lists. Be specific and practical. All content must be suitable for professional paid training delivery.',
                'max_tokens'  => 3000,
                'temperature' => 0.70,
                'is_active'   => true,
            ],

            // ─────────────────────────────────────────────────────
            // B. Lesson Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'Lesson Generator',
                'template_code'  => 'lesson_generator_v1',
                'category'       => 'Training AI',
                'description'    => 'Generate detailed lesson content including key concepts, explanations, examples, and activities for a specific training topic.',
                'system_prompt'  => <<<'PROMPT'
You are an expert instructional content writer for professional training programmes.
When writing lesson content, always:
- Start with a clear lesson overview and learning objectives
- Explain concepts clearly using simple, professional language
- Use real workplace examples relevant to the industry context
- Include check-for-understanding questions throughout
- Suggest trainer tips or facilitation notes where appropriate
- End each section with a practical application or reflection activity
- Keep content engaging and avoid excessive theory without application
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Write detailed training lesson content for the following topic:

{input}

Structure the lesson as:

1. LESSON OVERVIEW
   - Lesson Title
   - Duration estimate
   - Learning Objectives (3–5)

2. INTRODUCTION (Hook/Context — why this matters in the workplace)

3. CORE CONTENT SECTIONS
   Section A: [Key concept or topic]
   - Explanation
   - Real workplace example
   - Key point to remember

   Section B: [Next key concept]
   - Explanation
   - Real workplace example
   - Key point to remember

   (Continue as needed)

4. CASE STUDY / SCENARIO
   A realistic workplace scenario learners can analyse

5. KNOWLEDGE CHECK (3 reflection questions)

6. LESSON SUMMARY & KEY TAKEAWAYS

7. TRAINER NOTES / FACILITATION TIPS
PROMPT,
                'output_format_instructions' => 'Write in a clear, professional tone. Content must be suitable for adult learners in workplace training. Avoid jargon without explanation.',
                'max_tokens'  => 3000,
                'temperature' => 0.70,
                'is_active'   => true,
            ],

            // ─────────────────────────────────────────────────────
            // C. Quiz Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'Quiz Generator',
                'template_code'  => 'quiz_generator_v1',
                'category'       => 'Training AI',
                'description'    => 'Generate MCQs, True/False, and short-answer questions for any training topic with correct answers and explanations.',
                'system_prompt'  => <<<'PROMPT'
You are an expert assessment designer for professional training programmes.
When creating quiz questions, always:
- Write clear, unambiguous question stems
- For MCQs: provide 4 options (A/B/C/D) with one clearly correct answer
- Include plausible but clearly incorrect distractors (no trick questions)
- Provide the correct answer and a brief explanation for each question
- Mix cognitive levels: recall, comprehension, application (use Bloom's)
- Ensure questions directly test stated learning objectives
- Avoid double negatives and "all of the above / none of the above" options
- Make questions practical and relevant to real workplace situations
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Generate a quiz for the following training topic:

{input}

Provide:

MULTIPLE CHOICE QUESTIONS (5 questions)
For each question:
Q[n]: [Question text]
A) [Option]
B) [Option]
C) [Option]
D) [Option]
Correct Answer: [Letter]
Explanation: [Brief explanation why this is correct]

---

TRUE / FALSE QUESTIONS (3 questions)
For each question:
Q[n]: [Statement]
Answer: True / False
Explanation: [Brief explanation]

---

SHORT ANSWER QUESTIONS (2 questions)
For each question:
Q[n]: [Question]
Model Answer: [Expected answer in 2–4 sentences]

---

Total: 10 questions covering key concepts of the topic.
PROMPT,
                'output_format_instructions' => 'Format clearly. Separate each question type with a divider line. All questions must relate directly to the stated topic.',
                'max_tokens'  => 2500,
                'temperature' => 0.60,
                'is_active'   => true,
            ],

            // ─────────────────────────────────────────────────────
            // D. Case Study Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'Case Study Generator',
                'template_code'  => 'case_study_generator_v1',
                'category'       => 'Training AI',
                'description'    => 'Generate realistic compliance, audit, and management system case studies for training exercises.',
                'system_prompt'  => <<<'PROMPT'
You are an expert in compliance, management systems, and workplace audit scenarios.
When creating case studies, always:
- Set the scene with a realistic company/factory context
- Include specific details that make the scenario believable (numbers, processes, people)
- Present a genuine compliance challenge or non-conformance situation
- Include relevant evidence and observations an auditor might encounter
- Frame questions to promote critical thinking and problem-solving
- Align scenarios to real standards clauses or compliance requirements
- Avoid unrealistic or overly simplified situations
- Make scenarios suitable for group discussion or individual analysis
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Generate a realistic training case study for:

{input}

Structure the case study as:

CASE STUDY TITLE: [Descriptive title]

BACKGROUND
- Company/Organisation: [Fictional name and brief description]
- Industry/Sector:
- Location context:
- Relevant certification or compliance framework:

THE SITUATION
[3–5 paragraphs describing the scenario, including specific observations, data, evidence, and people involved. Make it realistic and detailed.]

KEY FACTS & EVIDENCE
- Observation 1:
- Observation 2:
- Observation 3:
- Document reviewed:
- Person interviewed:

DISCUSSION QUESTIONS
1. [Question prompting identification of the issue]
2. [Question about root cause analysis]
3. [Question about corrective actions required]
4. [Question about standard clause or requirement reference]
5. [Question about preventive actions or systemic improvements]

TRAINER NOTES
- Key learning points this case study demonstrates:
- Relevant standard clause(s):
- Expected participant outcomes:
PROMPT,
                'output_format_instructions' => 'Write in a professional, realistic tone. The scenario must be believable and grounded in real workplace situations. Avoid obviously fictional or trivial examples.',
                'max_tokens'  => 2500,
                'temperature' => 0.75,
                'is_active'   => true,
            ],

            // ─────────────────────────────────────────────────────
            // E. LinkedIn Post Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'LinkedIn Post Generator',
                'template_code'  => 'linkedin_post_v1',
                'category'       => 'Marketing AI',
                'description'    => 'Generate professional LinkedIn posts to promote SMS Training Academy courses, achievements, and thought leadership content.',
                'system_prompt'  => <<<'PROMPT'
You are a professional LinkedIn content creator specialising in the training and professional development industry.
When writing LinkedIn posts, always:
- Open with a compelling first line that stops the scroll (question, bold statement, or insight)
- Use a professional but conversational tone — avoid corporate-speak
- Write for LinkedIn's algorithm: short paragraphs, line breaks, easy to skim
- Include relevant value: insight, tip, or thought leadership point
- End with a clear call to action (register, comment, share, learn more)
- Use 3–5 relevant hashtags at the end
- Keep total length 150–300 words
- Never use excessive emojis — maximum 2–3 tasteful ones
- Target audience: HR managers, factory managers, compliance officers, quality professionals, sustainability managers
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Write a professional LinkedIn post to promote:

{input}

The post should:
- Grab attention in the first line
- Explain the value and relevance of this training to the target professional audience
- Include 1–2 compelling facts, outcomes, or benefits
- End with a clear call-to-action
- Include 3–5 relevant professional hashtags

Write the complete LinkedIn post ready to publish.
PROMPT,
                'output_format_instructions' => 'Format for LinkedIn with line breaks between paragraphs. Use a professional yet human tone. The post must be ready to copy-paste directly to LinkedIn.',
                'max_tokens'  => 600,
                'temperature' => 0.80,
                'is_active'   => true,
            ],

            // ─────────────────────────────────────────────────────
            // F. Facebook Post Generator
            // ─────────────────────────────────────────────────────
            [
                'template_name'  => 'Facebook Post Generator',
                'template_code'  => 'facebook_post_v1',
                'category'       => 'Marketing AI',
                'description'    => 'Generate engaging Facebook posts to promote SMS Training Academy courses and training programmes to a wider audience.',
                'system_prompt'  => <<<'PROMPT'
You are a social media content creator specialising in training and professional development marketing.
When writing Facebook posts for SMS Training Academy, always:
- Use a friendly, engaging, and approachable tone
- Keep it accessible to a broad audience — not overly technical
- Lead with a relatable problem, question, or opportunity
- Highlight clear benefits: career growth, compliance, job readiness
- Include a sense of urgency or opportunity where appropriate
- End with a clear, friendly call to action
- Use 2–4 relevant emojis to make the post visually appealing
- Keep total length 100–200 words
- Hashtags: 3–5 relevant tags
- Suitable for reaching factory workers, supervisors, HR staff, and working professionals
PROMPT,
                'user_prompt_template' => <<<'PROMPT'
Write an engaging Facebook post to promote:

{input}

The post should:
- Open with a relatable hook (question or common challenge)
- Clearly explain what this training offers in simple, accessible language
- Highlight 2–3 key benefits or outcomes for participants
- Include a friendly, encouraging call to action
- Use 2–4 appropriate emojis
- End with 3–5 relevant hashtags

Write the complete Facebook post ready to publish.
PROMPT,
                'output_format_instructions' => 'Format for Facebook with good visual spacing. Keep language simple and accessible. The post must be ready to copy-paste directly to Facebook.',
                'max_tokens'  => 500,
                'temperature' => 0.85,
                'is_active'   => true,
            ],

        ];

        foreach ($templates as $data) {
            AiPromptTemplate::updateOrCreate(
                ['template_code' => $data['template_code']],
                array_merge($data, ['version_number' => 1])
            );
        }

        $this->command->info('✓ AI Prompt Templates seeded (' . count($templates) . ' templates)');
    }
}
