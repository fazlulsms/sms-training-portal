<?php

namespace App\Support;

/**
 * Maps LTF Learning Framework ai_block_hint values to recommended lesson block
 * type sequences for the AI Course Generator.
 *
 * Usage:
 *   $hint     = $course->ltfLearningFramework?->ai_block_hint;
 *   $sequence = LtfBlockStrategy::blockSequence($hint);
 */
class LtfBlockStrategy
{
    // ── Block sequences keyed by ai_block_hint ────────────────────────────────

    private const STRATEGIES = [

        // ── Awareness Training (1 day) ────────────────────────────────────────
        'awareness' => [
            'sequence'         => ['rich_text','myth_fact','workplace_example','fun_fact','reflection','knowledge_check'],
            'optional'         => ['case_study','click_reveal'],
            'avoid'            => ['accordion','matching','slides'],
            'rationale'        => 'Myth-first approach builds curiosity; workplace examples anchor abstract concepts for a mixed-level audience.',
            'lesson_depth'     => 'short',
            'assessment_style' => 'Awareness — 3 questions per module quiz; focus on recall and basic understanding only. Avoid trick questions.',
            'final_exam_style' => 'Awareness — 15 questions total; 60% recall MCQ, 25% true/false, 15% simple scenario; easy-to-medium difficulty only.',
            'structure_hints'  => 'Design 3–4 modules maximum with 2–3 lessons each. Each lesson should have a single clear takeaway. Prioritise breadth over depth; avoid deep technical dives.',
        ],

        // ── Standard Interpretation (2 days) ─────────────────────────────────
        'standard_interpretation' => [
            'sequence'         => ['slides','rich_text','accordion','click_reveal','workplace_example','case_study','knowledge_check'],
            'optional'         => ['matching','download','reflection'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Accordion blocks mirror clause-by-clause navigation; click-reveal pairs each requirement with its practical intent.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Standard Interpretation — 4 questions per module quiz; test clause knowledge, requirement-vs-intent distinctions, and practical application.',
            'final_exam_style' => 'Standard Interpretation — 20 questions; 60% MCQ, 25% true/false, 15% scenario; medium difficulty; cover all clauses proportionally.',
            'structure_hints'  => 'Organise modules around standard sections or clause groups. Each module covers one clause group; each lesson covers one sub-clause or requirement type.',
        ],

        // ── Internal Auditor Training (2 days) ────────────────────────────────
        'internal_auditor' => [
            'sequence'         => ['rich_text','scenario','workplace_example','matching','case_study','knowledge_check'],
            'optional'         => ['slides','click_reveal','download','reflection'],
            'avoid'            => ['fun_fact','myth_fact'],
            'rationale'        => 'Scenario exercises simulate audit interviews; matching reinforces clause-to-evidence linkage.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Internal Auditor — 4 questions per module quiz; test audit procedure knowledge, evidence assessment, and NCR identification.',
            'final_exam_style' => 'Internal Auditor — 20 questions; 40% scenario-based; include NCR identification, audit trail analysis, and interview technique scenarios.',
            'structure_hints'  => 'Structure: Module 1 = Audit Principles; Module 2 = Audit Planning; Module 3 = Conducting the Audit; Module 4 = Reporting & Follow-up. 3–4 lessons per module.',
        ],

        // ── Lead Auditor Training (5 days) ────────────────────────────────────
        'lead_auditor' => [
            'sequence'         => ['slides','rich_text','case_study','scenario','accordion','workplace_example','matching','reflection','knowledge_check','click_reveal'],
            'optional'         => ['fun_fact','case_study'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Full toolkit for a 5-day certification program; download blocks deliver audit checklists and NCR templates.',
            'lesson_depth'     => 'deep',
            'assessment_style' => 'Lead Auditor — 5 questions per module quiz; test audit leadership, programme management, and complex NCR grading.',
            'final_exam_style' => 'Lead Auditor — 25 questions; 50% scenario-based with complex multi-stakeholder situations; minimum 20% hard difficulty questions.',
            'structure_hints'  => '5-day programme: Day 1 = Standards & Systems Overview, Day 2 = Audit Principles, Day 3 = Audit Practice, Day 4 = Complex Case Studies, Day 5 = Synthesis & Certification Prep. 3–5 lessons per day-module.',
        ],

        // ── Management Systems Implementation (3 days) ────────────────────────
        'implementation' => [
            'sequence'         => ['rich_text','slides','accordion','workplace_example','case_study','click_reveal','knowledge_check'],
            'optional'         => ['matching','reflection','scenario'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Accordion blocks map each implementation phase; download blocks provide gap-analysis templates.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Implementation — 4 questions per module quiz; test phase objectives, gap analysis decisions, and documentation requirements.',
            'final_exam_style' => 'Implementation — 20 questions; cover all implementation phases; scenario questions focus on gap analysis decisions, change management, and documentation choices.',
            'structure_hints'  => 'Organise by implementation phase: Context → Leadership → Planning → Support → Operations → Performance Evaluation → Improvement. 2–3 lessons per phase.',
        ],

        // ── Social Compliance Auditing (3 days) ───────────────────────────────
        'social_compliance_audit' => [
            'sequence'         => ['rich_text','scenario','case_study','myth_fact','workplace_example','matching','click_reveal','knowledge_check'],
            'optional'         => ['slides','reflection','accordion'],
            'avoid'            => ['fun_fact','slides'],
            'rationale'        => 'Scenario blocks simulate worker interviews; myth_fact corrects common misconceptions in social auditing.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Social Compliance Audit — 4 questions per module quiz; test auditor neutrality, worker interview techniques, and finding classification.',
            'final_exam_style' => 'Social Compliance Audit — 20 questions; 40% scenario emphasis; include worker rights, forced-labour indicators, audit integrity, and supply chain transparency scenarios.',
            'structure_hints'  => 'Structure: Module 1 = Labour Standards Foundation; Module 2 = Audit Preparation; Module 3 = Conducting the Audit; Module 4 = Findings, Reporting & Corrective Action. 3–4 lessons per module.',
        ],

        // ── Technical Skills Training (2 days) ────────────────────────────────
        'technical_skills' => [
            'sequence'         => ['rich_text','slides','workplace_example','scenario','matching','click_reveal','knowledge_check'],
            'optional'         => ['case_study','download','reflection'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Click-reveal delivers step-by-step procedures; matching enforces tool-to-process relationships.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Technical Skills — 4 questions per module quiz; test procedural recall, tool and technique application, and error identification.',
            'final_exam_style' => 'Technical Skills — 20 questions; practical application scenarios; test troubleshooting, process decision-making, and technique selection.',
            'structure_hints'  => 'Organise by skill area or process stage. Each lesson teaches one discrete skill or technique. Every procedure must include a worked example or step-by-step walkthrough.',
        ],

        // ── Executive / Management Training (1 day) ───────────────────────────
        'executive' => [
            'sequence'         => ['rich_text','case_study','slides','reflection','knowledge_check'],
            'optional'         => ['fun_fact','workplace_example','click_reveal'],
            'avoid'            => ['matching','myth_fact','accordion'],
            'rationale'        => 'Video sets strategic context; case studies replace drill exercises; reflection suits leadership development.',
            'lesson_depth'     => 'concise',
            'assessment_style' => 'Executive Development — 3 questions per module quiz; test strategic understanding and governance decisions only. Avoid operational-level questions.',
            'final_exam_style' => 'Executive Development — 15 questions; strategic governance scenarios only; board-level decision-making and organisational risk management perspective.',
            'structure_hints'  => 'Maximum 3 modules with 2–3 lessons each. Lead with the business case. Focus on governance obligations, risk management, and strategic alignment. Skip implementation details entirely.',
        ],

        // ── Train the Trainer (2 days) ────────────────────────────────────────
        'train_the_trainer' => [
            'sequence'         => ['rich_text','scenario','reflection','workplace_example','slides','click_reveal','case_study','knowledge_check'],
            'optional'         => ['matching','accordion','fun_fact'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Reflection is central to trainer development; scenario blocks simulate facilitation situations.',
            'lesson_depth'     => 'medium',
            'assessment_style' => 'Train the Trainer — 4 questions per module quiz; test facilitation principles, learning design choices, and participant management techniques.',
            'final_exam_style' => 'Train the Trainer — 20 questions; 35% scenario-based; include facilitation challenges, learning-style accommodation, and session design scenarios.',
            'structure_hints'  => 'Structure: Module 1 = Adult Learning Principles; Module 2 = Lesson Planning & Design; Module 3 = Facilitation Skills; Module 4 = Assessment & Evaluation. 3–4 lessons per module.',
        ],

        // ── Qualification Program (multi-module) ──────────────────────────────
        'qualification' => [
            'sequence'         => ['slides','rich_text','accordion','case_study','scenario','matching','reflection','click_reveal','workplace_example','knowledge_check'],
            'optional'         => ['fun_fact','myth_fact'],
            'avoid'            => ['myth_fact','fun_fact'],
            'rationale'        => 'Assessment-heavy sequence for credential programs; structured block progression supports evidence-based learning.',
            'lesson_depth'     => 'deep',
            'assessment_style' => 'Qualification Program — 5 questions per module quiz; test theoretical mastery and application; include portfolio evidence requirement questions.',
            'final_exam_style' => 'Qualification Program — 25 questions; 50% MCQ, 25% scenario, 25% application-level case study style; minimum 20% hard difficulty questions required.',
            'structure_hints'  => 'Minimum 4 modules; each module concludes with a portfolio task or evidence requirement. Structure builds cumulatively. Final module = synthesis, integration, and professional practice.',
        ],
    ];

    // ── Fallback when no framework is selected ────────────────────────────────

    private const DEFAULT_SEQUENCE = [
        'rich_text',
        'workplace_example',
        'knowledge_check',
    ];

    // ── Public API ────────────────────────────────────────────────────────────

    public static function blockSequence(?string $hint): array
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['sequence'];
        }
        return self::DEFAULT_SEQUENCE;
    }

    public static function rationale(?string $hint): string
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['rationale'];
        }
        return 'General-purpose sequence suitable for any learning framework.';
    }

    public static function avoidedBlocks(?string $hint): array
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['avoid'];
        }
        return [];
    }

    public static function optionalBlocks(?string $hint): array
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['optional'];
        }
        return [];
    }

    public static function lessonDepthFragment(?string $hint, ?string $level = null): string
    {
        $depthLabel = ($hint && isset(self::STRATEGIES[$hint]))
            ? self::STRATEGIES[$hint]['lesson_depth']
            : 'medium';

        $wordRange = match ($level) {
            'beginner'                => '400–700',
            'intermediate'            => '700–1200',
            'advanced'                => '1000–1800',
            'expert'                  => '1500–2500',
            // Legacy level labels from Mode B
            'Awareness'               => '400–700',
            'Professional'            => '700–1200',
            'Advanced'                => '1000–1800',
            default                   => match ($depthLabel) {
                'short', 'concise'    => '400–700',
                'deep'                => '1000–1800',
                default               => '700–1200',
            },
        };

        return "Target word count per lesson: {$wordRange} words. " .
               "Lesson depth profile: {$depthLabel}. " .
               "Do not pad or repeat to reach the word count — stop when the concept is clear.";
    }

    public static function structureHints(?string $hint): string
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['structure_hints'];
        }
        return 'Design 3–5 modules with 3–4 lessons each. Balance theory with practical application in every module.';
    }

    public static function assessmentFragment(?string $hint, string $level = 'intermediate'): string
    {
        $frameworkStyle = ($hint && isset(self::STRATEGIES[$hint]))
            ? self::STRATEGIES[$hint]['assessment_style']
            : 'Generate 4 multiple-choice questions per module quiz covering key lesson concepts.';

        $competencyStyle = self::competencyAssessmentFragment($level);

        return $frameworkStyle . ($competencyStyle ? ' ' . $competencyStyle : '');
    }

    public static function finalExamFragment(?string $hint, string $level = 'intermediate'): string
    {
        $frameworkStyle = ($hint && isset(self::STRATEGIES[$hint]))
            ? self::STRATEGIES[$hint]['final_exam_style']
            : 'Generate 20 questions covering all course modules. Mix MCQ, true/false, and scenario questions.';

        $competencyStyle = self::competencyAssessmentFragment($level);

        return $frameworkStyle . ($competencyStyle ? ' ' . $competencyStyle : '');
    }

    /**
     * Returns a combined prompt context string for a given framework hint and competency level.
     * Suitable for injection into any AI prompt that needs full taxonomy context.
     */
    public static function fullContext(?string $hint, ?string $level = null): string
    {
        $parts = [];

        $fragment = self::promptFragment($hint);
        if ($fragment) $parts[] = $fragment;

        $depth = self::lessonDepthFragment($hint, $level);
        if ($depth) $parts[] = $depth;

        if ($hint) {
            $avoided = self::avoidedBlocks($hint);
            if (!empty($avoided)) {
                $labels  = self::blockLabels();
                $readable = implode(', ', array_map(fn($t) => $labels[$t] ?? $t, $avoided));
                $parts[] = "Avoid the following block types for this framework: {$readable}.";
            }
        }

        $comp = self::competencyFragment($level);
        if ($comp) $parts[] = $comp;

        return implode("\n\n", $parts);
    }

    public static function allHints(): array
    {
        return [
            'awareness'               => 'Awareness Training Framework',
            'standard_interpretation' => 'Standard Interpretation Framework',
            'internal_auditor'        => 'Internal Auditor Training Framework',
            'lead_auditor'            => 'Lead Auditor Training Framework',
            'implementation'          => 'Implementation Training Framework',
            'social_compliance_audit' => 'Social Compliance Audit Framework',
            'technical_skills'        => 'Technical Skills Training Framework',
            'executive'               => 'Executive Development Framework',
            'train_the_trainer'       => 'Train the Trainer Framework',
            'qualification'           => 'Qualification Program Framework',
        ];
    }

    public static function all(): array
    {
        return self::STRATEGIES;
    }

    public static function defaultSequence(): array
    {
        return self::DEFAULT_SEQUENCE;
    }

    public static function promptFragment(?string $hint): string
    {
        $sequence = self::blockSequence($hint);
        $labels   = self::blockLabels();

        $readableList = implode(', ', array_map(
            fn(string $t) => $labels[$t] ?? $t,
            $sequence
        ));

        $frameworkName = self::allHints()[$hint] ?? 'General';

        return "This course follows the \"{$frameworkName}\" learning framework. " .
               "For each lesson, prefer the following block types in this order: {$readableList}. " .
               "Not all blocks are required for every lesson — choose the most appropriate subset " .
               "based on lesson content and depth.";
    }

    public static function competencyFragment(?string $level): string
    {
        return match ($level) {
            'beginner', 'Awareness' =>
                'The target learner is a BEGINNER. Use plain language and concrete examples. ' .
                'Avoid jargon without explanation. Keep knowledge checks straightforward — ' .
                'single-concept questions with clear correct answers. Case studies should ' .
                'involve simple, familiar workplace scenarios.',

            'intermediate', 'Professional' =>
                'The target learner has INTERMEDIATE knowledge. Build on foundational concepts ' .
                'without restating basics. Introduce nuance, exceptions, and real-world trade-offs. ' .
                'Knowledge checks may include application-level questions (not just recall). ' .
                'Case studies should present moderate-complexity situations requiring analysis.',

            'advanced', 'Advanced' =>
                'The target learner is ADVANCED. Assume solid prior knowledge — skip introductory ' .
                'explanations. Focus on edge cases, complex scenarios, and critical evaluation. ' .
                'Knowledge checks should require synthesis and judgment, not recall. Case studies ' .
                'should present ambiguous or multi-stakeholder situations.',

            'expert' =>
                'The target learner is an EXPERT or practitioner. Frame content as peer-level ' .
                'reference material. Prioritise depth, precision, and technical accuracy over ' .
                'scaffolding. Knowledge checks should challenge assumptions and test nuanced ' .
                'interpretation. Case studies should be complex, cross-functional, and ' .
                'require expert-level decision-making.',

            default => '',
        };
    }

    public static function blockLabels(): array
    {
        return [
            'rich_text'         => 'Rich Text',
            'accordion'         => 'Accordion / FAQ',
            'slides'            => 'Slide Presentation',
            'knowledge_check'   => 'Knowledge Check',
            'scenario'          => 'Scenario Exercise',
            'matching'          => 'Matching Activity',
            'click_reveal'      => 'Click to Reveal',
            'fun_fact'          => 'Fun Fact',
            'reflection'        => 'Reflection',
            'myth_fact'         => 'Myth vs Fact',
            'workplace_example' => 'Workplace Example',
            'case_study'        => 'Case Study',
            'video'             => 'Video',
            'audio'             => 'Audio',
            'image'             => 'Image',
            'gallery'           => 'Image Gallery',
            'pdf'               => 'PDF Viewer',
            'download'          => 'Download Resources',
        ];
    }

    private static function competencyAssessmentFragment(string $level): string
    {
        return match ($level) {
            'beginner', 'Awareness' =>
                'Questions must test basic recall and recognition only. Avoid application or analysis questions. Use simple, unambiguous language.',
            'intermediate', 'Professional' =>
                'Mix recall (40%) and application (60%) questions. Distractors should reflect common learner misunderstandings. Include at least one scenario-based question.',
            'advanced', 'Advanced' =>
                'Focus on application (40%), analysis (40%), and synthesis (20%). Distractors must be plausible expert-level alternatives. Scenario questions should involve competing priorities or ambiguous situations.',
            'expert' =>
                'All questions should test analysis, synthesis, or evaluation. Include cross-functional scenarios, edge cases, and regulatory nuance. Distractors must challenge even experienced practitioners.',
            default => '',
        };
    }
}
