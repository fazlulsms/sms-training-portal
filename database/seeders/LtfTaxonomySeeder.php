<?php

namespace Database\Seeders;

use App\Models\LtfAudienceType;
use App\Models\LtfDeliveryMethod;
use App\Models\LtfIndustry;
use App\Models\LtfLearningFramework;
use App\Models\LtfProgramPurpose;
use App\Models\LtfStandard;
use App\Models\LtfTrainingModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * LtfTaxonomySeeder — Definitive LTF master data seeder.
 *
 * Idempotent: uses updateOrCreate so it is safe to re-run at any time.
 * Does NOT delete existing records — only creates or updates.
 * Program Purposes are seeded last so framework ID lookups are accurate.
 *
 * Run: php artisan db:seed --class=LtfTaxonomySeeder
 */
class LtfTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedFrameworks();
            $this->seedDeliveryMethods();
            $this->seedTrainingModels();
            $this->seedStandards();
            $this->seedIndustries();
            $this->seedAudienceTypes();
            $this->seedProgramPurposes();
        });

        $this->printSummary();
    }

    // ── 1. Learning Frameworks ────────────────────────────────────────────

    private function seedFrameworks(): void
    {
        $frameworks = [
            [
                'name'                  => 'Awareness Framework',
                'ai_block_hint'         => 'awareness',
                'description'           => 'Short, accessible courses that build general awareness of a topic — why it matters, real-world consequences, and basic concepts. Suitable for all staff regardless of prior knowledge.',
                'typical_duration_days' => 1,
                'display_order'         => 1,
            ],
            [
                'name'                  => 'Standard Interpretation Framework',
                'ai_block_hint'         => 'standard_interpretation',
                'description'           => 'Clause-by-clause courses that teach what a standard requires and how to interpret it in practice. For practitioners who need to understand and apply specific requirements.',
                'typical_duration_days' => 2,
                'display_order'         => 2,
            ],
            [
                'name'                  => 'Implementation Framework',
                'ai_block_hint'         => 'implementation',
                'description'           => 'Practical step-by-step courses on how to build and deploy a management system or programme from gap analysis through to certification readiness.',
                'typical_duration_days' => 3,
                'display_order'         => 3,
            ],
            [
                'name'                  => 'Internal Auditor Framework',
                'ai_block_hint'         => 'internal_auditor',
                'description'           => 'Competency-based courses that develop internal audit skills: planning, conducting, finding classification, NCR writing, and follow-up verification.',
                'typical_duration_days' => 2,
                'display_order'         => 4,
            ],
            [
                'name'                  => 'Lead Auditor Framework',
                'ai_block_hint'         => 'lead_auditor',
                'description'           => 'Professional-level courses aligned to IRCA or equivalent certification requirements. Covers audit programme management, complex NCR grading, ethics, and lead auditor responsibilities.',
                'typical_duration_days' => 5,
                'display_order'         => 5,
            ],
            [
                'name'                  => 'Social Compliance Auditor Framework',
                'ai_block_hint'         => 'social_compliance_audit',
                'description'           => 'Specialist framework for APSCA, SMETA, SLCP, and SA8000 auditing. Emphasises worker interviews, triangulation, ethical dilemmas, and social audit integrity.',
                'typical_duration_days' => 3,
                'display_order'         => 6,
            ],
            [
                'name'                  => 'Technical Skills Framework',
                'ai_block_hint'         => 'technical_skills',
                'description'           => 'Practical skill-building courses on specific techniques and tools — Root Cause Analysis, CAP Management, Interview Techniques, and other professional competencies.',
                'typical_duration_days' => 2,
                'display_order'         => 7,
            ],
            [
                'name'                  => 'Executive Learning Framework',
                'ai_block_hint'         => 'executive',
                'description'           => 'Strategic leadership courses for senior managers and board-level executives. Focuses on governance obligations, risk management, ESG, and strategic alignment.',
                'typical_duration_days' => 1,
                'display_order'         => 8,
            ],
            [
                'name'                  => 'Train-the-Trainer Framework',
                'ai_block_hint'         => 'train_the_trainer',
                'description'           => 'Facilitator development courses that build instructional design, delivery, and participant management skills for workplace trainers.',
                'typical_duration_days' => 2,
                'display_order'         => 9,
            ],
            [
                'name'                  => 'Qualification Program Framework',
                'ai_block_hint'         => 'qualification',
                'description'           => 'Assessment-heavy framework for formal qualification pathways. Combines scenario exercises, portfolio evidence, and rigorous assessment against a defined competency standard.',
                'typical_duration_days' => 10,
                'display_order'         => 10,
            ],
        ];

        foreach ($frameworks as $fw) {
            LtfLearningFramework::updateOrCreate(
                ['ai_block_hint' => $fw['ai_block_hint']],
                array_merge($fw, [
                    'slug'   => Str::slug($fw['name']),
                    'status' => 'active',
                ])
            );
        }

        $this->command->info('  ✓ Learning Frameworks: ' . count($frameworks));
    }

    // ── 2. Delivery Methods ───────────────────────────────────────────────

    private function seedDeliveryMethods(): void
    {
        $methods = [
            ['name' => 'Self-Paced eLearning',           'description' => 'Online courses completed at the learner\'s own pace.',            'display_order' => 1],
            ['name' => 'Instructor-Led Training',         'description' => 'Face-to-face classroom-based training with a live instructor.',    'display_order' => 2],
            ['name' => 'Virtual Instructor-Led Training', 'description' => 'Live online training via video conference with an instructor.',     'display_order' => 3],
            ['name' => 'Blended Learning',                'description' => 'Combination of eLearning and face-to-face or virtual sessions.',   'display_order' => 4],
            ['name' => 'Micro Learning',                  'description' => 'Short, focused learning modules (5–15 minutes) on single topics.', 'display_order' => 5],
            ['name' => 'Webinar',                         'description' => 'Live online presentation or seminar, typically shorter format.',    'display_order' => 6],
            ['name' => 'Workshop',                        'description' => 'Interactive hands-on session focused on practical skill-building.', 'display_order' => 7],
            ['name' => 'Coaching / Mentoring',            'description' => 'One-to-one or small group guided learning with a subject expert.',  'display_order' => 8],
        ];

        foreach ($methods as $m) {
            LtfDeliveryMethod::updateOrCreate(
                ['slug' => Str::slug($m['name'])],
                array_merge($m, ['status' => 'active'])
            );
        }

        $this->command->info('  ✓ Delivery Methods: ' . count($methods));
    }

    // ── 3. Training Models ────────────────────────────────────────────────

    private function seedTrainingModels(): void
    {
        $models = [
            ['name' => 'Public Training',          'description' => 'Open-enrolment training available to participants from any organisation.', 'display_order' => 1],
            ['name' => 'Corporate Training',        'description' => 'Training delivered exclusively to staff of a single organisation.',         'display_order' => 2],
            ['name' => 'Customized Training',       'description' => 'Training adapted to the specific needs, context, or standards of a client.','display_order' => 3],
            ['name' => 'Internal Company Training', 'description' => 'Training designed and delivered internally by the organisation\'s own team.', 'display_order' => 4],
            ['name' => 'Open Enrollment',           'description' => 'Scheduled public courses that individuals or teams can register for.',      'display_order' => 5],
            ['name' => 'Partner / Franchise Delivery','description'=> 'Training delivered by an authorised partner or licensee.',                'display_order' => 6],
        ];

        foreach ($models as $m) {
            LtfTrainingModel::updateOrCreate(
                ['slug' => Str::slug($m['name'])],
                array_merge($m, ['status' => 'active'])
            );
        }

        $this->command->info('  ✓ Training Models: ' . count($models));
    }

    // ── 4. Standards & Frameworks ─────────────────────────────────────────

    private function seedStandards(): void
    {
        $standards = [
            // ISO Standards
            ['domain'=>'iso', 'name'=>'ISO 9001',          'full_name'=>'ISO 9001 Quality Management Systems',                       'version'=>'2015', 'display_order'=>1],
            ['domain'=>'iso', 'name'=>'ISO 14001',         'full_name'=>'ISO 14001 Environmental Management Systems',                'version'=>'2015', 'display_order'=>2],
            ['domain'=>'iso', 'name'=>'ISO 14001:2026',    'full_name'=>'ISO 14001 Environmental Management Systems (2026 Revision)','version'=>'2026', 'display_order'=>3],
            ['domain'=>'iso', 'name'=>'ISO 45001',         'full_name'=>'ISO 45001 Occupational Health & Safety',                   'version'=>'2018', 'display_order'=>4],
            ['domain'=>'iso', 'name'=>'ISO 22000',         'full_name'=>'ISO 22000 Food Safety Management Systems',                  'version'=>'2018', 'display_order'=>5],
            ['domain'=>'iso', 'name'=>'ISO 27001',         'full_name'=>'ISO 27001 Information Security Management',                 'version'=>'2022', 'display_order'=>6],
            ['domain'=>'iso', 'name'=>'ISO 50001',         'full_name'=>'ISO 50001 Energy Management Systems',                      'version'=>'2018', 'display_order'=>7],
            ['domain'=>'iso', 'name'=>'ISO 42001',         'full_name'=>'ISO 42001 Artificial Intelligence Management Systems',      'version'=>'2023', 'display_order'=>8],
            ['domain'=>'iso', 'name'=>'ISO 37001',         'full_name'=>'ISO 37001 Anti-Bribery Management Systems',                'version'=>'2016', 'display_order'=>9],
            ['domain'=>'iso', 'name'=>'ISO 13485',         'full_name'=>'ISO 13485 Medical Devices Quality Management Systems',      'version'=>'2016', 'display_order'=>10],

            // Social Compliance
            ['domain'=>'social_compliance', 'name'=>'SA8000',                   'full_name'=>'SA8000 Social Accountability Standard',            'version'=>'2014', 'display_order'=>20],
            ['domain'=>'social_compliance', 'name'=>'SMETA',                    'full_name'=>'Sedex Members Ethical Trade Audit',                 'version'=>null,   'display_order'=>21],
            ['domain'=>'social_compliance', 'name'=>'SLCP',                     'full_name'=>'Social & Labor Convergence Program',                'version'=>null,   'display_order'=>22],
            ['domain'=>'social_compliance', 'name'=>'APSCA Competency Framework','full_name'=>'APSCA Social Audit Competency Framework',          'version'=>null,   'display_order'=>23],
            ['domain'=>'social_compliance', 'name'=>'Amfori BSCI',              'full_name'=>'Amfori Business Social Compliance Initiative',      'version'=>null,   'display_order'=>24],
            ['domain'=>'social_compliance', 'name'=>'WRAP',                     'full_name'=>'Worldwide Responsible Accredited Production',        'version'=>null,   'display_order'=>25],

            // Sustainability & ESG
            ['domain'=>'sustainability', 'name'=>'Higg FEM',      'full_name'=>'Higg Facility Environmental Module',                   'version'=>null, 'display_order'=>30],
            ['domain'=>'sustainability', 'name'=>'GRI Standards',  'full_name'=>'Global Reporting Initiative Standards',                'version'=>null, 'display_order'=>31],
            ['domain'=>'sustainability', 'name'=>'ESG',            'full_name'=>'Environmental, Social & Governance Framework',         'version'=>null, 'display_order'=>32],
            ['domain'=>'sustainability', 'name'=>'CDP',            'full_name'=>'Carbon Disclosure Project',                           'version'=>null, 'display_order'=>33],
            ['domain'=>'sustainability', 'name'=>'SBTi',           'full_name'=>'Science Based Targets Initiative',                    'version'=>null, 'display_order'=>34],

            // Supply Chain Security
            ['domain'=>'supply_chain', 'name'=>'CTPAT', 'full_name'=>'Customs-Trade Partnership Against Terrorism', 'version'=>null, 'display_order'=>40],
            ['domain'=>'supply_chain', 'name'=>'AEO',   'full_name'=>'Authorised Economic Operator',               'version'=>null, 'display_order'=>41],

            // Labor & Human Rights
            ['domain'=>'labor_rights', 'name'=>'UNGP',                   'full_name'=>'UN Guiding Principles on Business & Human Rights', 'version'=>null, 'display_order'=>50],
            ['domain'=>'labor_rights', 'name'=>'ILO Standards',           'full_name'=>'International Labour Organization Standards',     'version'=>null, 'display_order'=>51],
            ['domain'=>'labor_rights', 'name'=>'Responsible Recruitment', 'full_name'=>'Responsible Recruitment Framework',              'version'=>null, 'display_order'=>52],

            // Grievance & Worker Voice
            ['domain'=>'grievance', 'name'=>'Grievance Management Systems', 'full_name'=>'Operational Grievance Mechanism Design',         'version'=>null, 'display_order'=>60],
            ['domain'=>'grievance', 'name'=>'Worker Committees',            'full_name'=>'Worker Representation & Committee Systems',      'version'=>null, 'display_order'=>61],
            ['domain'=>'grievance', 'name'=>'Worker Engagement',            'full_name'=>'Worker Voice & Engagement Frameworks',           'version'=>null, 'display_order'=>62],

            // Health & Safety
            ['domain'=>'hse', 'name'=>'Occupational Safety', 'full_name'=>'Occupational Health & Safety Management', 'version'=>null, 'display_order'=>70],
            ['domain'=>'hse', 'name'=>'Fire Safety',          'full_name'=>'Fire Safety & Emergency Preparedness',   'version'=>null, 'display_order'=>71],
            ['domain'=>'hse', 'name'=>'Chemical Safety',      'full_name'=>'Chemical Hazard & COSHH Management',     'version'=>null, 'display_order'=>72],

            // Quality & Operations
            ['domain'=>'quality_ops', 'name'=>'Lean',                         'full_name'=>'Lean Manufacturing & Process Improvement', 'version'=>null, 'display_order'=>80],
            ['domain'=>'quality_ops', 'name'=>'Six Sigma',                    'full_name'=>'Six Sigma Quality Management',             'version'=>null, 'display_order'=>81],
            ['domain'=>'quality_ops', 'name'=>'Root Cause Analysis',          'full_name'=>'Root Cause Analysis Methods & Tools',      'version'=>null, 'display_order'=>82],
            ['domain'=>'quality_ops', 'name'=>'Corrective Action Management', 'full_name'=>'Corrective & Preventive Action Management','version'=>null, 'display_order'=>83],
        ];

        foreach ($standards as $s) {
            LtfStandard::updateOrCreate(
                ['slug' => Str::slug($s['name'])],
                array_merge($s, ['description' => null, 'status' => 'active'])
            );
        }

        $this->command->info('  ✓ Standards & Frameworks: ' . count($standards));
    }

    // ── 5. Industries ─────────────────────────────────────────────────────

    private function seedIndustries(): void
    {
        $industries = [
            ['name' => 'Cross Industry',     'display_order' =>  1],
            ['name' => 'Garments',           'display_order' =>  2],
            ['name' => 'Textile',            'display_order' =>  3],
            ['name' => 'Footwear',           'display_order' =>  4],
            ['name' => 'Electronics',        'display_order' =>  5],
            ['name' => 'Furniture',          'display_order' =>  6],
            ['name' => 'Plastic',            'display_order' =>  7],
            ['name' => 'Agriculture',        'display_order' =>  8],
            ['name' => 'Food & Beverage',    'display_order' =>  9],
            ['name' => 'Logistics',          'display_order' => 10],
            ['name' => 'Construction',       'display_order' => 11],
            ['name' => 'Energy',             'display_order' => 12],
            ['name' => 'Healthcare',         'display_order' => 13],
            ['name' => 'Education',          'display_order' => 14],
            ['name' => 'Retail',             'display_order' => 15],
            ['name' => 'Service Sector',     'display_order' => 16],
            ['name' => 'Chemical',           'display_order' => 17],
            ['name' => 'Packaging',          'display_order' => 18],
            ['name' => 'Printing',           'display_order' => 19],
            ['name' => 'Metal & Engineering','display_order' => 20],
        ];

        foreach ($industries as $ind) {
            LtfIndustry::updateOrCreate(
                ['slug' => Str::slug($ind['name'])],
                array_merge($ind, ['description' => null, 'status' => 'active'])
            );
        }

        $this->command->info('  ✓ Industries: ' . count($industries));
    }

    // ── 6. Audience Types ─────────────────────────────────────────────────

    private function seedAudienceTypes(): void
    {
        $audiences = [
            ['name' => 'Workers',                     'display_order' =>  1],
            ['name' => 'Supervisors',                 'display_order' =>  2],
            ['name' => 'Managers',                    'display_order' =>  3],
            ['name' => 'Internal Auditors',           'display_order' =>  4],
            ['name' => 'Lead Auditors',               'display_order' =>  5],
            ['name' => 'HR Professionals',            'display_order' =>  6],
            ['name' => 'Compliance Professionals',    'display_order' =>  7],
            ['name' => 'Sustainability Professionals','display_order' =>  8],
            ['name' => 'Factory Representatives',     'display_order' =>  9],
            ['name' => 'Consultants',                 'display_order' => 10],
            ['name' => 'Executives',                  'display_order' => 11],
            ['name' => 'Trainers',                    'display_order' => 12],
            ['name' => 'EMS Coordinators',            'display_order' => 13],
            ['name' => 'QMS Coordinators',            'display_order' => 14],
            ['name' => 'HSE Professionals',           'display_order' => 15],
            ['name' => 'Certification Body Auditors', 'display_order' => 16],
            ['name' => 'Social Compliance Auditors',  'display_order' => 17],
            ['name' => 'APSCA Candidates',            'display_order' => 18],
        ];

        foreach ($audiences as $a) {
            LtfAudienceType::updateOrCreate(
                ['slug' => Str::slug($a['name'])],
                array_merge($a, ['description' => null, 'status' => 'active'])
            );
        }

        $this->command->info('  ✓ Audience Types: ' . count($audiences));
    }

    // ── 7. Program Purposes ───────────────────────────────────────────────

    private function seedProgramPurposes(): void
    {
        // Resolve framework IDs by ai_block_hint — safe across environments
        $fw = LtfLearningFramework::pluck('id', 'ai_block_hint')->toArray();

        $purposes = [
            [
                'name'                  => 'Awareness Training',
                'suggested_framework'   => 'awareness',
                'description'           => 'Build general awareness of a topic for all staff, with no prior knowledge assumed.',
                'display_order'         => 1,
            ],
            [
                'name'                  => 'Foundation Training',
                'suggested_framework'   => 'awareness',
                'description'           => 'Introductory courses establishing foundational knowledge before progressing to specialist training.',
                'display_order'         => 2,
            ],
            [
                'name'                  => 'Standard Interpretation Training',
                'suggested_framework'   => 'standard_interpretation',
                'description'           => 'Clause-by-clause courses teaching how to read, interpret, and apply a specific standard or regulation.',
                'display_order'         => 3,
            ],
            [
                'name'                  => 'Implementation Training',
                'suggested_framework'   => 'implementation',
                'description'           => 'Practical training on how to build and deploy a management system or compliance programme.',
                'display_order'         => 4,
            ],
            [
                'name'                  => 'Internal Auditor Training',
                'suggested_framework'   => 'internal_auditor',
                'description'           => 'Develops competence to plan and conduct internal audits and write Non-Conformance Reports.',
                'display_order'         => 5,
            ],
            [
                'name'                  => 'Lead Auditor Training',
                'suggested_framework'   => 'lead_auditor',
                'description'           => 'Professional auditor development aligned to IRCA or equivalent certification requirements.',
                'display_order'         => 6,
            ],
            [
                'name'                  => 'Auditor Conversion Training',
                'suggested_framework'   => 'standard_interpretation',
                'description'           => 'Short conversion courses for qualified auditors adding a new standard to their scope.',
                'display_order'         => 7,
            ],
            [
                'name'                  => 'Certification Preparation',
                'suggested_framework'   => 'standard_interpretation',
                'description'           => 'Courses that prepare organisations or individuals for an upcoming certification audit.',
                'display_order'         => 8,
            ],
            [
                'name'                  => 'Qualification Program',
                'suggested_framework'   => 'qualification',
                'description'           => 'Formal multi-module programmes leading to an assessed qualification or credential.',
                'display_order'         => 9,
            ],
            [
                'name'                  => 'Competency Development Program',
                'suggested_framework'   => 'technical_skills',
                'description'           => 'Structured learning journeys that develop specific professional competencies over time.',
                'display_order'         => 10,
            ],
            [
                'name'                  => 'Refresher Training',
                'suggested_framework'   => 'standard_interpretation',
                'description'           => 'Short update courses covering recent standard revisions, regulatory changes, or knowledge gaps.',
                'display_order'         => 11,
            ],
            [
                'name'                  => 'Train-the-Trainer',
                'suggested_framework'   => 'train_the_trainer',
                'description'           => 'Builds instructional design, facilitation, and assessment skills for workplace trainers.',
                'display_order'         => 12,
            ],
            [
                'name'                  => 'Executive Development',
                'suggested_framework'   => 'executive',
                'description'           => 'Strategic leadership programmes for senior managers, directors, and board-level executives.',
                'display_order'         => 13,
            ],
            [
                'name'                  => 'Professional Development',
                'suggested_framework'   => 'technical_skills',
                'description'           => 'Continuing professional development courses that extend practitioner skills and knowledge.',
                'display_order'         => 14,
            ],
        ];

        foreach ($purposes as $p) {
            $fwHint = $p['suggested_framework'];
            $fwId   = $fw[$fwHint] ?? null;

            LtfProgramPurpose::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'name'                  => $p['name'],
                    'description'           => $p['description'],
                    'suggested_framework_id'=> $fwId,
                    'display_order'         => $p['display_order'],
                    'status'                => 'active',
                ]
            );
        }

        $this->command->info('  ✓ Program Purposes: ' . count($purposes));
    }

    // ── Summary ───────────────────────────────────────────────────────────

    private function printSummary(): void
    {
        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════');
        $this->command->info('  LTF Taxonomy Seeding Complete');
        $this->command->info('════════════════════════════════════════════');
        $this->command->table(
            ['Table', 'Total Records', 'Active'],
            [
                ['ltf_learning_frameworks', LtfLearningFramework::count(),  LtfLearningFramework::where('status','active')->count()],
                ['ltf_delivery_methods',    LtfDeliveryMethod::count(),     LtfDeliveryMethod::where('status','active')->count()],
                ['ltf_training_models',     LtfTrainingModel::count(),      LtfTrainingModel::where('status','active')->count()],
                ['ltf_standards',           LtfStandard::count(),           LtfStandard::where('status','active')->count()],
                ['ltf_industries',          LtfIndustry::count(),           LtfIndustry::where('status','active')->count()],
                ['ltf_audience_types',      LtfAudienceType::count(),       LtfAudienceType::where('status','active')->count()],
                ['ltf_program_purposes',    LtfProgramPurpose::count(),     LtfProgramPurpose::where('status','active')->count()],
            ]
        );
        $this->command->newLine();
    }
}
