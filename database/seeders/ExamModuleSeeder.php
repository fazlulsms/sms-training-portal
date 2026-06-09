<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSet;
use Illuminate\Database\Seeder;

class ExamModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo question set
        $qs = QuestionSet::create([
            'title'                       => 'Knowledge Test – Social Compliance Awareness Training',
            'description'                 => 'This knowledge test assesses your understanding of social compliance, labour rights, and workplace standards covered in the training. Please answer all questions carefully. You must score at least 60% to pass.',
            'status'                      => 'Active',
            'total_marks'                 => 50,
            'pass_mark'                   => null,
            'pass_percentage'             => 60,
            'allowed_attempts'            => 2,
            'time_limit_minutes'          => 30,
            'show_result_to_participant'  => true,
            'allow_certificate_after_pass'=> true,
            'created_by'                  => 'System',
        ]);

        // Q1 – MCQ Single
        $q1 = Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'Under international labour standards, what is the maximum regular working hours per week?',
            'question_type'          => 'mcq_single',
            'is_required'            => true,
            'marks'                  => 10,
            'manual_review_required' => false,
            'sort_order'             => 1,
        ]);
        foreach ([
            ['48 hours', true],
            ['40 hours', false],
            ['60 hours', false],
            ['72 hours', false],
        ] as $idx => [$text, $correct]) {
            QuestionOption::create(['question_id' => $q1->id, 'option_text' => $text, 'is_correct' => $correct, 'sort_order' => $idx]);
        }

        // Q2 – True/False
        $q2 = Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'Forced or compulsory labour, including bonded labour, is prohibited under ILO Core Conventions.',
            'question_type'          => 'true_false',
            'is_required'            => true,
            'marks'                  => 10,
            'correct_answer'         => 'true',
            'manual_review_required' => false,
            'sort_order'             => 2,
        ]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'True',  'is_correct' => true,  'sort_order' => 0]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'False', 'is_correct' => false, 'sort_order' => 1]);

        // Q3 – MCQ Multiple
        $q3 = Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'Which of the following are considered forms of workplace harassment? (Select all that apply)',
            'question_type'          => 'mcq_multiple',
            'is_required'            => true,
            'marks'                  => 10,
            'manual_review_required' => false,
            'sort_order'             => 3,
        ]);
        foreach ([
            ['Verbal abuse or threatening language', true],
            ['Constructive performance feedback', false],
            ['Unwanted physical contact', true],
            ['Discrimination based on religion or gender', true],
        ] as $idx => [$text, $correct]) {
            QuestionOption::create(['question_id' => $q3->id, 'option_text' => $text, 'is_correct' => $correct, 'sort_order' => $idx]);
        }

        // Q4 – Short answer
        Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'Name one international organisation that sets global labour standards for workers.',
            'question_type'          => 'short_answer',
            'is_required'            => true,
            'marks'                  => 10,
            'manual_review_required' => true,
            'sort_order'             => 4,
        ]);

        // Q5 – Paragraph
        Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'In your own words, explain why freedom of association is important for workers in a factory environment. (Minimum 3 sentences)',
            'question_type'          => 'paragraph',
            'is_required'            => true,
            'marks'                  => 5,
            'manual_review_required' => true,
            'sort_order'             => 5,
        ]);

        // Q6 – Declaration
        Question::create([
            'question_set_id'        => $qs->id,
            'question_text'          => 'I confirm that I have read and understood the social compliance training materials provided, and I commit to uphold these standards in my workplace.',
            'question_type'          => 'declaration',
            'is_required'            => true,
            'marks'                  => 5,
            'manual_review_required' => false,
            'sort_order'             => 6,
        ]);

        $this->command->info('ExamModuleSeeder: Demo question set created — "Knowledge Test – Social Compliance Awareness Training"');
        $this->command->info('  Question set ID: ' . $qs->id . ' | Total marks: 50 | Pass: 60% (30 marks) | Attempts: 2');
    }
}
