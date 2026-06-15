<?php

namespace Database\Seeders;

use App\Models\LtfAudienceType;
use App\Models\LtfDeliveryMethod;
use App\Models\LtfIndustry;
use App\Models\LtfLearningFramework;
use App\Models\LtfProgramPurpose;
use App\Models\LtfTrainingModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LtfTaxonomyV2Seeder extends Seeder
{
    public function run(): void
    {
        $this->renameFrameworks();
        $this->addQualificationFramework();
        $this->seedDeliveryMethods();
        $this->seedTrainingModels();
        $this->seedProgramPurposes();
        $this->cleanupIndustries();
        $this->addTrainersAudience();
    }

    // ── 1. Rename Learning Frameworks to include "Framework" ─────

    private function renameFrameworks(): void
    {
        $renames = [
            'awareness'              => 'Awareness Learning Framework',
            'standard_interpretation'=> 'Standard Interpretation Framework',
            'internal_auditor'       => 'Internal Auditor Framework',
            'lead_auditor'           => 'Lead Auditor Framework',
            'implementation'         => 'Implementation Framework',
            'social_compliance_audit'=> 'Social Compliance Auditor Framework',
            'technical_skills'       => 'Technical Skills Framework',
            'executive'              => 'Executive Learning Framework',
            'train_the_trainer'      => 'Train-the-Trainer Framework',
        ];

        foreach ($renames as $hint => $newName) {
            LtfLearningFramework::where('ai_block_hint', $hint)
                ->update(['name' => $newName]);
        }
    }

    // ── 2. Add new Qualification Program Framework ───────────────

    private function addQualificationFramework(): void
    {
        LtfLearningFramework::firstOrCreate(
            ['ai_block_hint' => 'qualification'],
            [
                'name'                  => 'Qualification Program Framework',
                'slug'                  => 'qualification-program-framework',
                'description'           => 'Assessment-heavy framework for formal qualification programs. Combines scenario exercises, evidence-based assessments, and formal documentation requirements.',
                'typical_duration_days' => 3,
                'display_order'         => 10,
                'status'                => 'active',
            ]
        );
    }

    // ── 3. Seed Delivery Methods ──────────────────────────────────

    private function seedDeliveryMethods(): void
    {
        $entries = [
            ['name' => 'Self-Paced eLearning',              'description' => 'Learner-controlled online course with no fixed schedule.',                        'display_order' => 1],
            ['name' => 'Instructor-Led Training (ILT)',      'description' => 'In-person classroom delivery with a live trainer.',                               'display_order' => 2],
            ['name' => 'Virtual Instructor-Led (VILT)',      'description' => 'Live online delivery via video conferencing with a trainer.',                      'display_order' => 3],
            ['name' => 'Blended Learning',                   'description' => 'Combination of self-paced eLearning and live sessions.',                          'display_order' => 4],
            ['name' => 'Micro Learning',                     'description' => 'Short, focused learning modules typically under 15 minutes.',                     'display_order' => 5],
            ['name' => 'Webinar',                            'description' => 'Live online presentation-style session, typically one-way.',                      'display_order' => 6],
            ['name' => 'Workshop',                           'description' => 'Highly interactive hands-on facilitated session.',                                 'display_order' => 7],
            ['name' => 'Coaching / Mentoring',               'description' => 'One-to-one or small group guided development sessions.',                          'display_order' => 8],
        ];

        foreach ($entries as $entry) {
            LtfDeliveryMethod::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, ['status' => 'active'])
            );
        }
    }

    // ── 4. Seed Training Models ───────────────────────────────────

    private function seedTrainingModels(): void
    {
        $entries = [
            ['name' => 'Public Training',            'description' => 'Open-enrollment course available to participants from any organisation.',                 'display_order' => 1],
            ['name' => 'Corporate Training',         'description' => 'Delivered exclusively for a single client organisation\'s team.',                        'display_order' => 2],
            ['name' => 'Customized Training',        'description' => 'Content adapted to a specific client\'s context, systems, or brand.',                    'display_order' => 3],
            ['name' => 'Internal Company Training',  'description' => 'Designed for internal use within SMS Training Services.',                                'display_order' => 4],
            ['name' => 'Open Enrollment',            'description' => 'Self-registration online course open to individual learners.',                           'display_order' => 5],
            ['name' => 'Partner / Franchise Delivery','description' => 'Delivered by authorised partner trainers under SMS Training branding.',                 'display_order' => 6],
        ];

        foreach ($entries as $entry) {
            LtfTrainingModel::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, ['status' => 'active'])
            );
        }
    }

    // ── 5. Seed Program Purposes ──────────────────────────────────

    private function seedProgramPurposes(): void
    {
        // Helper: look up framework ID by ai_block_hint
        $fwId = fn(string $hint): ?int =>
            LtfLearningFramework::where('ai_block_hint', $hint)->value('id');

        $entries = [
            [
                'name'                 => 'Awareness Training',
                'description'          => 'Introductory exposure to a topic, policy, or principle for a general audience.',
                'suggested_hint'       => 'awareness',
                'display_order'        => 1,
            ],
            [
                'name'                 => 'Foundation Training',
                'description'          => 'Foundational knowledge of a standard, framework, or domain before specialised study.',
                'suggested_hint'       => 'standard_interpretation',
                'display_order'        => 2,
            ],
            [
                'name'                 => 'Implementation Training',
                'description'          => 'Equips participants to build, deploy, or operationalise a management system.',
                'suggested_hint'       => 'implementation',
                'display_order'        => 3,
            ],
            [
                'name'                 => 'Internal Auditor Training',
                'description'          => 'Develops skills to plan, conduct, and report internal audits.',
                'suggested_hint'       => 'internal_auditor',
                'display_order'        => 4,
            ],
            [
                'name'                 => 'Lead Auditor Training',
                'description'          => 'Advanced auditor certification program covering audit leadership and reporting.',
                'suggested_hint'       => 'lead_auditor',
                'display_order'        => 5,
            ],
            [
                'name'                 => 'Certification Preparation',
                'description'          => 'Prepares candidates for an external certification examination.',
                'suggested_hint'       => 'lead_auditor',
                'display_order'        => 6,
            ],
            [
                'name'                 => 'Qualification Program',
                'description'          => 'Formal multi-component program leading to a credential or qualification.',
                'suggested_hint'       => 'qualification',
                'display_order'        => 7,
            ],
            [
                'name'                 => 'Competency Development Program',
                'description'          => 'Builds specific professional competencies through structured skill exercises.',
                'suggested_hint'       => 'technical_skills',
                'display_order'        => 8,
            ],
            [
                'name'                 => 'Refresher Training',
                'description'          => 'Updates or reinforces prior knowledge for experienced practitioners.',
                'suggested_hint'       => 'standard_interpretation',
                'display_order'        => 9,
            ],
            [
                'name'                 => 'Train-the-Trainer',
                'description'          => 'Develops facilitation and training delivery skills for those who train others.',
                'suggested_hint'       => 'train_the_trainer',
                'display_order'        => 10,
            ],
            [
                'name'                 => 'Executive Development',
                'description'          => 'Strategic-level learning for executives and senior managers.',
                'suggested_hint'       => 'executive',
                'display_order'        => 11,
            ],
            [
                'name'                 => 'Professional Development',
                'description'          => 'Broad professional skills growth not tied to a specific standard or audit function.',
                'suggested_hint'       => 'technical_skills',
                'display_order'        => 12,
            ],
        ];

        foreach ($entries as $entry) {
            $hint = $entry['suggested_hint'];
            unset($entry['suggested_hint']);

            LtfProgramPurpose::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, [
                    'suggested_framework_id' => $fwId($hint),
                    'status'                 => 'active',
                ])
            );
        }
    }

    // ── 6. Clean up Industries ────────────────────────────────────

    private function cleanupIndustries(): void
    {
        // Remove Manufacturing and Plastic (not in v2 taxonomy)
        LtfIndustry::whereIn('slug', ['manufacturing', 'plastic'])->delete();

        // Normalise hyphen in Cross-Industry slug/name
        LtfIndustry::where('slug', 'cross-industry')
            ->update(['name' => 'Cross Industry', 'slug' => 'cross-industry']);
    }

    // ── 7. Add Trainers to Audience Types ────────────────────────

    private function addTrainersAudience(): void
    {
        $maxOrder = LtfAudienceType::max('display_order') ?? 0;

        LtfAudienceType::firstOrCreate(
            ['slug' => 'trainers'],
            [
                'name'          => 'Trainers',
                'display_order' => $maxOrder + 1,
                'status'        => 'active',
            ]
        );
    }
}
