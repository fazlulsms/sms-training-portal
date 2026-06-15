<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'ISO Management Systems',
                'description'   => 'Internationally recognised standards for quality, environment, health & safety, energy, food safety, and information security management.',
                'icon'          => '📋',
                'display_order' => 1,
            ],
            [
                'name'          => 'Social Compliance',
                'description'   => 'Social auditing, ethical trade, and worker welfare frameworks including SA8000, SMETA, SLCP, and APSCA competency programs.',
                'icon'          => '🤝',
                'display_order' => 2,
            ],
            [
                'name'          => 'Sustainability & ESG',
                'description'   => 'Environmental, social and governance reporting, GRI Standards, science-based targets, and carbon disclosure for sustainable business.',
                'icon'          => '🌱',
                'display_order' => 3,
            ],
            [
                'name'          => 'Health & Safety',
                'description'   => 'Occupational health, fire safety, chemical hazard management, and workplace wellbeing programs aligned with ISO 45001.',
                'icon'          => '🦺',
                'display_order' => 4,
            ],
            [
                'name'          => 'Supply Chain & Trade',
                'description'   => 'Supply chain security, customs compliance, C-TPAT, AEO, and responsible sourcing frameworks for global trade professionals.',
                'icon'          => '🔗',
                'display_order' => 5,
            ],
            [
                'name'          => 'Labor & Human Rights',
                'description'   => 'ILO standards, UN Guiding Principles, responsible recruitment, grievance mechanisms, and worker voice programs.',
                'icon'          => '⚖️',
                'display_order' => 6,
            ],
            [
                'name'          => 'Quality & Operations',
                'description'   => 'Lean manufacturing, Six Sigma, operational excellence, root cause analysis, and process improvement methodologies.',
                'icon'          => '⚙️',
                'display_order' => 7,
            ],
            [
                'name'          => 'Professional Development',
                'description'   => 'Leadership, communication, HR management, train the trainer, and executive development programs for career growth.',
                'icon'          => '🎓',
                'display_order' => 8,
            ],
            [
                'name'          => 'eLearning Programs',
                'description'   => 'Self-paced online courses accessible anytime, anywhere — certificates upon completion with full LMS tracking.',
                'icon'          => '💻',
                'display_order' => 9,
            ],
            [
                'name'          => 'Audit & Assurance',
                'description'   => 'Internal auditor, lead auditor, and specialist audit training covering methodology, evidence collection, and NCR writing.',
                'icon'          => '🔍',
                'display_order' => 10,
            ],
        ];

        foreach ($categories as $cat) {
            CourseCategory::firstOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['is_public' => true, 'status' => 'active'])
            );
        }
    }
}
