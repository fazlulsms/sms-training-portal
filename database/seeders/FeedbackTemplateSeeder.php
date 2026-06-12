<?php

namespace Database\Seeders;

use App\Models\FeedbackTemplate;
use App\Models\FeedbackQuestion;
use Illuminate\Database\Seeder;

class FeedbackTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedIltTemplate();
        $this->seedElearningTemplate();
    }

    private function seedIltTemplate(): void
    {
        $template = FeedbackTemplate::updateOrCreate(
            ['name' => 'ILT Standard Evaluation', 'type' => 'ilt'],
            [
                'description'              => 'Standard evaluation form for Instructor-Led Training sessions',
                'is_default'               => true,
                'is_active'                => true,
                'allow_multiple'           => false,
                'require_for_certificate'  => false,
                'created_by'               => null,
            ]
        );

        if ($template->questions()->count() === 0) {
            $questions = [
                ['text' => 'Overall satisfaction with this training',           'type' => 'rating_5', 'cat' => 'overall',  'req' => true],
                ['text' => 'Quality of training content and materials',          'type' => 'rating_5', 'cat' => 'content',  'req' => true],
                ['text' => 'Trainer knowledge and expertise',                    'type' => 'rating_5', 'cat' => 'trainer',  'req' => true],
                ['text' => 'Trainer communication and delivery style',           'type' => 'rating_5', 'cat' => 'trainer',  'req' => true],
                ['text' => 'Relevance of training to your job role',             'type' => 'rating_5', 'cat' => 'content',  'req' => true],
                ['text' => 'Practical exercises and activities',                 'type' => 'rating_5', 'cat' => 'content',  'req' => false],
                ['text' => 'Training venue and facilities',                      'type' => 'rating_5', 'cat' => 'overall',  'req' => false],
                ['text' => 'Knowledge gained from this training',                'type' => 'rating_5', 'cat' => 'overall',  'req' => true],
                ['text' => 'Would you recommend this training to colleagues?',   'type' => 'yes_no',   'cat' => 'overall',  'req' => true],
                ['text' => 'What did you find most valuable about this training?','type' => 'text',    'cat' => 'open',     'req' => false],
                ['text' => 'What improvements would you suggest?',               'type' => 'text',     'cat' => 'open',     'req' => false],
                ['text' => 'What topics would you like covered in future sessions?','type' => 'text',  'cat' => 'open',     'req' => false],
            ];

            foreach ($questions as $i => $q) {
                FeedbackQuestion::create([
                    'template_id'   => $template->id,
                    'question_text' => $q['text'],
                    'question_type' => $q['type'],
                    'category'      => $q['cat'],
                    'is_required'   => $q['req'],
                    'sort_order'    => $i + 1,
                ]);
            }
        }
    }

    private function seedElearningTemplate(): void
    {
        $template = FeedbackTemplate::updateOrCreate(
            ['name' => 'eLearning Course Evaluation', 'type' => 'elearning'],
            [
                'description'              => 'Standard evaluation form for online/eLearning courses',
                'is_default'               => true,
                'is_active'                => true,
                'allow_multiple'           => false,
                'require_for_certificate'  => false,
                'created_by'               => null,
            ]
        );

        if ($template->questions()->count() === 0) {
            $questions = [
                ['text' => 'Overall satisfaction with this course',              'type' => 'rating_5', 'cat' => 'overall',   'req' => true],
                ['text' => 'Quality of course content',                          'type' => 'rating_5', 'cat' => 'content',   'req' => true],
                ['text' => 'Clarity and organisation of lessons',                'type' => 'rating_5', 'cat' => 'content',   'req' => true],
                ['text' => 'eLearning platform ease of use',                     'type' => 'rating_5', 'cat' => 'platform',  'req' => true],
                ['text' => 'Video and multimedia quality',                       'type' => 'rating_5', 'cat' => 'elearning', 'req' => false],
                ['text' => 'Quiz and assessment effectiveness',                  'type' => 'rating_5', 'cat' => 'elearning', 'req' => false],
                ['text' => 'Course pace (speed of delivery)',                    'type' => 'rating_5', 'cat' => 'elearning', 'req' => false],
                ['text' => 'Knowledge gained from this course',                  'type' => 'rating_5', 'cat' => 'overall',   'req' => true],
                ['text' => 'Would you recommend this course to colleagues?',     'type' => 'yes_no',   'cat' => 'overall',   'req' => true],
                ['text' => 'What did you find most valuable?',                   'type' => 'text',     'cat' => 'open',      'req' => false],
                ['text' => 'What improvements would you suggest?',               'type' => 'text',     'cat' => 'open',      'req' => false],
                ['text' => 'Were there any technical issues during the course?', 'type' => 'text',     'cat' => 'platform',  'req' => false],
            ];

            foreach ($questions as $i => $q) {
                FeedbackQuestion::create([
                    'template_id'   => $template->id,
                    'question_text' => $q['text'],
                    'question_type' => $q['type'],
                    'category'      => $q['cat'],
                    'is_required'   => $q['req'],
                    'sort_order'    => $i + 1,
                ]);
            }
        }
    }
}
