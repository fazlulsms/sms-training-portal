<?php

namespace Database\Seeders;

use App\Models\LtfAudienceType;
use App\Models\LtfCourseType;
use App\Models\LtfIndustry;
use App\Models\LtfLearningFramework;
use App\Models\LtfStandard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LtfTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCourseTypes();
        $this->seedLearningFrameworks();
        $this->seedStandards();
        $this->seedIndustries();
        $this->seedAudienceTypes();
    }

    // ── Layer 1 — Course Types ────────────────────────────────────

    private function seedCourseTypes(): void
    {
        $entries = [
            // eLearning
            ['group' => 'elearning',  'name' => 'Self-Paced eLearning',             'display_order' => 1],
            ['group' => 'elearning',  'name' => 'Blended Learning',                 'display_order' => 2],
            ['group' => 'elearning',  'name' => 'Micro Learning',                   'display_order' => 3],
            // Instructor-Led Training
            ['group' => 'ilt',        'name' => 'Public Training',                  'display_order' => 4],
            ['group' => 'ilt',        'name' => 'Corporate Training',               'display_order' => 5],
            ['group' => 'ilt',        'name' => 'Customized Training',              'display_order' => 6],
            ['group' => 'ilt',        'name' => 'Virtual Instructor-Led Training',  'display_order' => 7],
            // Assessment-Based
            ['group' => 'assessment', 'name' => 'Qualification Program',            'display_order' => 8],
            ['group' => 'assessment', 'name' => 'Competency Assessment Program',    'display_order' => 9],
            ['group' => 'assessment', 'name' => 'Certification Preparation Program','display_order' => 10],
        ];

        foreach ($entries as $entry) {
            LtfCourseType::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, ['status' => 'active'])
            );
        }
    }

    // ── Layer 2 — Learning Design Frameworks ─────────────────────

    private function seedLearningFrameworks(): void
    {
        $entries = [
            [
                'name'                  => 'Awareness Training',
                'ai_block_hint'         => 'awareness',
                'typical_duration_days' => 1,
                'description'           => 'Introductory level. Builds basic awareness of topics such as stress, workplace etiquette, anti-harassment, and grievance. Suitable for all workers.',
                'display_order'         => 1,
            ],
            [
                'name'                  => 'Standard Interpretation',
                'ai_block_hint'         => 'standard_interpretation',
                'typical_duration_days' => 2,
                'description'           => 'Clause-by-clause interpretation of management system standards. Focus on requirements, intent, and real-world application.',
                'display_order'         => 2,
            ],
            [
                'name'                  => 'Internal Auditor Training',
                'ai_block_hint'         => 'internal_auditor',
                'typical_duration_days' => 2,
                'description'           => 'Equips participants to plan and conduct internal audits, write findings, and support corrective action management.',
                'display_order'         => 3,
            ],
            [
                'name'                  => 'Lead Auditor Training',
                'ai_block_hint'         => 'lead_auditor',
                'typical_duration_days' => 5,
                'description'           => 'Advanced auditor certification program covering audit leadership, evidence collection, NCR writing, and audit reporting.',
                'display_order'         => 4,
            ],
            [
                'name'                  => 'Management Systems Implementation',
                'ai_block_hint'         => 'implementation',
                'typical_duration_days' => 3,
                'description'           => 'Guides organizations through implementing management systems including documentation, gap analysis, and risk management.',
                'display_order'         => 5,
            ],
            [
                'name'                  => 'Social Compliance Auditing',
                'ai_block_hint'         => 'social_compliance_audit',
                'typical_duration_days' => 3,
                'description'           => 'Specialist training in social auditing methodologies including SMETA, SLCP, SA8000, and APSCA competency areas.',
                'display_order'         => 6,
            ],
            [
                'name'                  => 'Technical Skills Training',
                'ai_block_hint'         => 'technical_skills',
                'typical_duration_days' => 2,
                'description'           => 'Builds practical technical skills such as root cause analysis, interview techniques, and corrective action plan management.',
                'display_order'         => 7,
            ],
            [
                'name'                  => 'Executive / Management Training',
                'ai_block_hint'         => 'executive',
                'typical_duration_days' => 1,
                'description'           => 'Strategic level training for executives and senior managers covering leadership, governance, and sustainability strategy.',
                'display_order'         => 8,
            ],
            [
                'name'                  => 'Train the Trainer',
                'ai_block_hint'         => 'train_the_trainer',
                'typical_duration_days' => 2,
                'description'           => 'Develops facilitation and training delivery skills for professionals who train others internally.',
                'display_order'         => 9,
            ],
        ];

        foreach ($entries as $entry) {
            LtfLearningFramework::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, ['status' => 'active'])
            );
        }
    }

    // ── Layer 3 — Standards & Frameworks ─────────────────────────

    private function seedStandards(): void
    {
        $entries = [
            // ISO Standards
            ['domain' => 'iso', 'name' => 'ISO 9001',  'full_name' => 'ISO 9001 Quality Management Systems',                  'version' => '2015', 'display_order' => 1],
            ['domain' => 'iso', 'name' => 'ISO 14001', 'full_name' => 'ISO 14001 Environmental Management Systems',            'version' => '2015', 'display_order' => 2],
            ['domain' => 'iso', 'name' => 'ISO 45001', 'full_name' => 'ISO 45001 Occupational Health & Safety',                'version' => '2018', 'display_order' => 3],
            ['domain' => 'iso', 'name' => 'ISO 50001', 'full_name' => 'ISO 50001 Energy Management Systems',                   'version' => '2018', 'display_order' => 4],
            ['domain' => 'iso', 'name' => 'ISO 22000', 'full_name' => 'ISO 22000 Food Safety Management Systems',              'version' => '2018', 'display_order' => 5],
            ['domain' => 'iso', 'name' => 'ISO 27001', 'full_name' => 'ISO 27001 Information Security Management',             'version' => '2022', 'display_order' => 6],
            ['domain' => 'iso', 'name' => 'ISO 42001', 'full_name' => 'ISO 42001 Artificial Intelligence Management Systems',  'version' => '2023', 'display_order' => 7],
            ['domain' => 'iso', 'name' => 'ISO 37001', 'full_name' => 'ISO 37001 Anti-Bribery Management Systems',             'version' => '2016', 'display_order' => 8],
            ['domain' => 'iso', 'name' => 'ISO 13485', 'full_name' => 'ISO 13485 Medical Devices Quality Management Systems',  'version' => '2016', 'display_order' => 9],

            // Social Compliance
            ['domain' => 'social_compliance', 'name' => 'SA8000',                  'full_name' => 'SA8000 Social Accountability Standard',         'version' => '2014', 'display_order' => 10],
            ['domain' => 'social_compliance', 'name' => 'SMETA',                   'full_name' => 'Sedex Members Ethical Trade Audit',             'version' => null,   'display_order' => 11],
            ['domain' => 'social_compliance', 'name' => 'SLCP',                    'full_name' => 'Social & Labor Convergence Program',            'version' => null,   'display_order' => 12],
            ['domain' => 'social_compliance', 'name' => 'APSCA Competency Areas',  'full_name' => 'APSCA Social Audit Competency Framework',       'version' => null,   'display_order' => 13],
            ['domain' => 'social_compliance', 'name' => 'Amfori BSCI',             'full_name' => 'Amfori Business Social Compliance Initiative',  'version' => null,   'display_order' => 14],
            ['domain' => 'social_compliance', 'name' => 'WRAP',                    'full_name' => 'Worldwide Responsible Accredited Production',   'version' => null,   'display_order' => 15],

            // Sustainability
            ['domain' => 'sustainability', 'name' => 'Higg FEM',      'full_name' => 'Higg Facility Environmental Module',               'version' => null, 'display_order' => 16],
            ['domain' => 'sustainability', 'name' => 'GRI Standards',  'full_name' => 'Global Reporting Initiative Standards',           'version' => null, 'display_order' => 17],
            ['domain' => 'sustainability', 'name' => 'ESG Framework',  'full_name' => 'Environmental, Social & Governance Framework',   'version' => null, 'display_order' => 18],
            ['domain' => 'sustainability', 'name' => 'SBTi',           'full_name' => 'Science Based Targets Initiative',               'version' => null, 'display_order' => 19],
            ['domain' => 'sustainability', 'name' => 'CDP',            'full_name' => 'Carbon Disclosure Project',                      'version' => null, 'display_order' => 20],

            // Supply Chain Security
            ['domain' => 'supply_chain', 'name' => 'CTPAT', 'full_name' => 'Customs-Trade Partnership Against Terrorism', 'version' => null, 'display_order' => 21],
            ['domain' => 'supply_chain', 'name' => 'AEO',   'full_name' => 'Authorised Economic Operator',                'version' => null, 'display_order' => 22],

            // Labor & Human Rights
            ['domain' => 'labor_rights', 'name' => 'UNGP',                    'full_name' => 'UN Guiding Principles on Business & Human Rights', 'version' => null, 'display_order' => 23],
            ['domain' => 'labor_rights', 'name' => 'ILO Standards',           'full_name' => 'International Labour Organization Standards',       'version' => null, 'display_order' => 24],
            ['domain' => 'labor_rights', 'name' => 'Responsible Recruitment', 'full_name' => 'Responsible Recruitment Framework',                'version' => null, 'display_order' => 25],

            // Grievance & Worker Voice
            ['domain' => 'grievance', 'name' => 'Grievance Management Systems', 'full_name' => 'Operational Grievance Mechanism Design',    'version' => null, 'display_order' => 26],
            ['domain' => 'grievance', 'name' => 'Worker Committees',            'full_name' => 'Worker Representation & Committee Systems', 'version' => null, 'display_order' => 27],
            ['domain' => 'grievance', 'name' => 'Worker Engagement',            'full_name' => 'Worker Voice & Engagement Frameworks',     'version' => null, 'display_order' => 28],

            // Health & Safety
            ['domain' => 'hse', 'name' => 'Occupational Safety', 'full_name' => 'Occupational Health & Safety Management', 'version' => null, 'display_order' => 29],
            ['domain' => 'hse', 'name' => 'Fire Safety',         'full_name' => 'Fire Safety & Emergency Preparedness',   'version' => null, 'display_order' => 30],
            ['domain' => 'hse', 'name' => 'Chemical Safety',     'full_name' => 'Chemical Hazard & COSHH Management',     'version' => null, 'display_order' => 31],

            // Quality & Operations
            ['domain' => 'quality_ops', 'name' => 'Lean',                   'full_name' => 'Lean Manufacturing & Process Improvement',  'version' => null, 'display_order' => 32],
            ['domain' => 'quality_ops', 'name' => 'Six Sigma',               'full_name' => 'Six Sigma Quality Management',             'version' => null, 'display_order' => 33],
            ['domain' => 'quality_ops', 'name' => 'Operational Excellence', 'full_name' => 'Operational Excellence Framework',          'version' => null, 'display_order' => 34],

            // Professional Development
            ['domain' => 'professional_dev', 'name' => 'Communication Skills', 'full_name' => 'Professional Communication & Presentation', 'version' => null, 'display_order' => 35],
            ['domain' => 'professional_dev', 'name' => 'Leadership',           'full_name' => 'Leadership & People Management',           'version' => null, 'display_order' => 36],
            ['domain' => 'professional_dev', 'name' => 'HR Management',        'full_name' => 'Human Resource Management Fundamentals',   'version' => null, 'display_order' => 37],
        ];

        foreach ($entries as $entry) {
            LtfStandard::firstOrCreate(
                ['slug' => Str::slug($entry['name'])],
                array_merge($entry, ['status' => 'active'])
            );
        }
    }

    // ── Layer 4 — Industries ──────────────────────────────────────

    private function seedIndustries(): void
    {
        $names = [
            'Manufacturing',
            'Garments',
            'Textile',
            'Footwear',
            'Electronics',
            'Plastic',
            'Furniture',
            'Agriculture',
            'Food & Beverage',
            'Logistics',
            'Construction',
            'Energy',
            'Healthcare',
            'Education',
            'Retail',
            'Service Sector',
            'Cross-Industry',
        ];

        foreach ($names as $i => $name) {
            LtfIndustry::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'display_order' => $i + 1, 'status' => 'active']
            );
        }
    }

    // ── Layer 5 — Audience Types ──────────────────────────────────

    private function seedAudienceTypes(): void
    {
        $names = [
            'Workers',
            'Supervisors',
            'Managers',
            'Internal Auditors',
            'Lead Auditors',
            'HR Professionals',
            'Compliance Professionals',
            'Sustainability Professionals',
            'Factory Representatives',
            'Consultants',
            'Executives',
        ];

        foreach ($names as $i => $name) {
            LtfAudienceType::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'display_order' => $i + 1, 'status' => 'active']
            );
        }
    }
}
