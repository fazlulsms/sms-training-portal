<?php

namespace App\Support;

/**
 * Maps LTF Learning Framework ai_block_hint values to recommended lesson block
 * type sequences for the AI Course Generator.
 *
 * Hook point: pass the course's ltfLearningFramework->ai_block_hint to
 * blockSequence() and inject the result into the AI prompt as the preferred
 * block order for each lesson. The generator itself is unchanged.
 *
 * Usage:
 *   $hint     = $course->ltfLearningFramework?->ai_block_hint;
 *   $sequence = LtfBlockStrategy::blockSequence($hint);
 *   // e.g. ['rich_text', 'scenario', 'knowledge_check']
 */
class LtfBlockStrategy
{
    // ── Block sequences keyed by ai_block_hint ────────────────────────────────

    private const STRATEGIES = [

        // ── Awareness Training (1 day) ────────────────────────────────────────
        // Short, motivating content. Lead with myth-busting and real examples;
        // close with a light check. Avoid heavy text or detailed exercises.
        'awareness' => [
            'sequence' => [
                'rich_text',
                'video',
                'myth_fact',
                'workplace_example',
                'fun_fact',
                'reflection',
                'knowledge_check',
            ],
            'rationale' => 'Myth-first approach builds curiosity; workplace examples anchor abstract concepts for a mixed-level audience.',
        ],

        // ── Standard Interpretation (2 days) ─────────────────────────────────
        // Clause-by-clause structure maps naturally to accordion blocks.
        // Slides give a visual overview before each clause group.
        // Click-reveal pairs requirement text with practical intent.
        'standard_interpretation' => [
            'sequence' => [
                'slides',
                'rich_text',
                'accordion',
                'click_reveal',
                'workplace_example',
                'case_study',
                'knowledge_check',
            ],
            'rationale' => 'Accordion blocks mirror clause-by-clause navigation; click-reveal pairs each requirement with its practical intent.',
        ],

        // ── Internal Auditor Training (2 days) ────────────────────────────────
        // Skill-based: learners must apply knowledge, not just recall it.
        // Scenario exercises simulate audit conversations and evidence review.
        // Matching activities reinforce clause-to-evidence linkage.
        'internal_auditor' => [
            'sequence' => [
                'rich_text',
                'scenario',
                'workplace_example',
                'matching',
                'case_study',
                'knowledge_check',
            ],
            'rationale' => 'Scenario exercises simulate audit interviews; matching reinforces clause-to-evidence linkage.',
        ],

        // ── Lead Auditor Training (5 days) ────────────────────────────────────
        // Multi-day certification program — full block toolkit.
        // Reflection supports audit leadership development.
        // Download provides audit checklists, NCR templates, report templates.
        'lead_auditor' => [
            'sequence' => [
                'slides',
                'rich_text',
                'case_study',
                'scenario',
                'accordion',
                'workplace_example',
                'matching',
                'reflection',
                'knowledge_check',
                'download',
            ],
            'rationale' => 'Full toolkit for a 5-day certification program; download blocks deliver audit checklists and NCR templates.',
        ],

        // ── Management Systems Implementation (3 days) ────────────────────────
        // Sequential process guidance: implementation phases map to accordion.
        // Download provides gap analysis templates and documentation frameworks.
        // Case studies show real-world rollout successes and failures.
        'implementation' => [
            'sequence' => [
                'rich_text',
                'slides',
                'accordion',
                'workplace_example',
                'case_study',
                'download',
                'knowledge_check',
            ],
            'rationale' => 'Accordion blocks map each implementation phase; download blocks provide gap-analysis templates.',
        ],

        // ── Social Compliance Auditing (3 days) ───────────────────────────────
        // Scenario-heavy: worker interviews, supply chain walk-throughs.
        // Myth vs Fact addresses common ethical misconceptions.
        // Download provides SMETA/SLCP/APSCA reference tools.
        'social_compliance_audit' => [
            'sequence' => [
                'rich_text',
                'scenario',
                'case_study',
                'myth_fact',
                'workplace_example',
                'matching',
                'knowledge_check',
                'download',
            ],
            'rationale' => 'Scenario blocks simulate worker interviews; myth_fact corrects common misconceptions in social auditing.',
        ],

        // ── Technical Skills Training (2 days) ────────────────────────────────
        // Step-by-step technique delivery: click-reveal for procedures.
        // Matching reinforces tool-to-process and cause-to-effect pairs.
        'technical_skills' => [
            'sequence' => [
                'rich_text',
                'slides',
                'workplace_example',
                'scenario',
                'matching',
                'click_reveal',
                'knowledge_check',
            ],
            'rationale' => 'Click-reveal delivers step-by-step procedures; matching enforces tool-to-process relationships.',
        ],

        // ── Executive / Management Training (1 day) ───────────────────────────
        // Strategic, time-constrained audience. Video opens with real-world
        // context. Case studies replace exercises. Reflection over recall.
        // Avoid accordion (too granular), fun_fact (too casual for exec tone).
        'executive' => [
            'sequence' => [
                'video',
                'rich_text',
                'case_study',
                'slides',
                'reflection',
                'knowledge_check',
            ],
            'rationale' => 'Video sets strategic context; case studies replace drill exercises; reflection suits leadership development.',
        ],

        // ── Train the Trainer (2 days) ────────────────────────────────────────
        // Meta-learning: learners study how to teach others.
        // Reflection is central — trainers need to examine their own style.
        // Scenario exercises simulate facilitation situations.
        // Download provides session plans and facilitation guides.
        'train_the_trainer' => [
            'sequence' => [
                'rich_text',
                'scenario',
                'reflection',
                'workplace_example',
                'slides',
                'click_reveal',
                'knowledge_check',
                'download',
            ],
            'rationale' => 'Reflection is central to trainer development; scenario blocks simulate facilitation situations.',
        ],

        // ── Qualification Program (multi-module) ──────────────────────────────
        // Extended credential program: combines theoretical mastery with
        // applied portfolio evidence. Assessment-heavy with rich reference
        // materials. Gallery and PDF blocks support evidence portfolios.
        // Download provides templates, rubrics, and submission guides.
        'qualification' => [
            'sequence' => [
                'slides',
                'rich_text',
                'accordion',
                'case_study',
                'scenario',
                'matching',
                'reflection',
                'pdf',
                'gallery',
                'knowledge_check',
                'download',
            ],
            'rationale' => 'Assessment-heavy sequence for credential programs; portfolio blocks (pdf, gallery) support evidence submission.',
        ],
    ];

    // ── Fallback when no framework is selected ────────────────────────────────

    private const DEFAULT_SEQUENCE = [
        'rich_text',
        'workplace_example',
        'knowledge_check',
    ];

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Returns the recommended ordered array of block_types for a given hint.
     * Falls back to a minimal default if the hint is unknown or null.
     *
     * @param  string|null $hint  The ai_block_hint from LtfLearningFramework.
     * @return string[]
     */
    public static function blockSequence(?string $hint): array
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['sequence'];
        }

        return self::DEFAULT_SEQUENCE;
    }

    /**
     * Returns a one-line rationale string for a given hint.
     *
     * @param  string|null $hint
     * @return string
     */
    public static function rationale(?string $hint): string
    {
        if ($hint && isset(self::STRATEGIES[$hint])) {
            return self::STRATEGIES[$hint]['rationale'];
        }

        return 'General-purpose sequence suitable for any learning framework.';
    }

    /**
     * Returns all known hint => framework-name pairs.
     * Useful for displaying strategy coverage in admin UI.
     *
     * @return array<string, string>
     */
    public static function allHints(): array
    {
        return [
            'awareness'              => 'Awareness Training Framework',
            'standard_interpretation'=> 'Standard Interpretation Framework',
            'internal_auditor'       => 'Internal Auditor Training Framework',
            'lead_auditor'           => 'Lead Auditor Training Framework',
            'implementation'         => 'Implementation Training Framework',
            'social_compliance_audit'=> 'Social Compliance Audit Framework',
            'technical_skills'       => 'Technical Skills Training Framework',
            'executive'              => 'Executive Development Framework',
            'train_the_trainer'      => 'Train the Trainer Framework',
            'qualification'          => 'Qualification Program Framework',
        ];
    }

    /**
     * Returns the full strategy map — useful for admin overview pages.
     *
     * @return array<string, array{sequence: string[], rationale: string}>
     */
    public static function all(): array
    {
        return self::STRATEGIES;
    }

    /**
     * Returns the fallback block sequence used when no framework is set.
     *
     * @return string[]
     */
    public static function defaultSequence(): array
    {
        return self::DEFAULT_SEQUENCE;
    }

    /**
     * Builds a plain-English prompt fragment describing the preferred block
     * sequence. Drop this into the AI system prompt so the generator uses the
     * right blocks in the right order for the selected framework.
     *
     * @param  string|null $hint
     * @return string
     */
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

    /**
     * Builds a plain-English prompt fragment describing how competency level
     * should shape lesson depth, complexity, and assessment difficulty.
     * Returns an empty string when level is null or unset.
     *
     * @param  string|null $level  One of: beginner, intermediate, advanced, expert
     * @return string
     */
    public static function competencyFragment(?string $level): string
    {
        return match ($level) {
            'beginner' =>
                'The target learner is a BEGINNER. Use plain language and concrete examples. ' .
                'Avoid jargon without explanation. Keep knowledge checks straightforward — ' .
                'single-concept questions with clear correct answers. Case studies should ' .
                'involve simple, familiar workplace scenarios.',

            'intermediate' =>
                'The target learner has INTERMEDIATE knowledge. Build on foundational concepts ' .
                'without restating basics. Introduce nuance, exceptions, and real-world trade-offs. ' .
                'Knowledge checks may include application-level questions (not just recall). ' .
                'Case studies should present moderate-complexity situations requiring analysis.',

            'advanced' =>
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

    /**
     * Returns human-readable labels for every block type defined in
     * LessonBlock::TYPES, without importing that model.
     *
     * @return array<string, string>
     */
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
}
