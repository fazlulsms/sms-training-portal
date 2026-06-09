<?php

namespace App\Http\Controllers;

use App\Models\ParticipantTestAnswer;
use App\Models\ParticipantTestAttempt;
use App\Services\ExamService;
use Illuminate\Http\Request;

class ParticipantExamController extends Controller
{
    // ── Show exam ─────────────────────────────────────────────────────────

    public function show($token)
    {
        $attempt = ParticipantTestAttempt::with([
            'enrollment.trainingSchedule.course',
            'questionSet.questions.options',
        ])->where('exam_token', $token)->firstOrFail();

        // Submitted already — show result
        if ($attempt->isSubmitted()) {
            return view('exam.submitted', compact('attempt'));
        }

        // Mark as started
        if ($attempt->status === 'not_started') {
            $attempt->update([
                'status'     => 'in_progress',
                'started_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $questionSet = $attempt->questionSet;
        $questions   = $questionSet->questions()->with('options')->get();

        // Load existing answers for this attempt (in case of resume)
        $existingAnswers = $attempt->answers()->with('question')->get()
            ->keyBy('question_id');

        return view('exam.show', compact('attempt', 'questionSet', 'questions', 'existingAnswers'));
    }

    // ── Submit exam ───────────────────────────────────────────────────────

    public function submit(Request $request, $token)
    {
        $attempt = ParticipantTestAttempt::with('questionSet.questions.options', 'enrollment')
            ->where('exam_token', $token)
            ->firstOrFail();

        if ($attempt->isSubmitted()) {
            return redirect('/exam/' . $token)->with('info', 'This exam has already been submitted.');
        }

        $questions = $attempt->questionSet->questions;

        // Save answers
        foreach ($questions as $question) {
            $existing = ParticipantTestAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->first();

            $answerData = [
                'attempt_id'  => $attempt->id,
                'question_id' => $question->id,
            ];

            $fieldName = "q_{$question->id}";

            switch ($question->question_type) {
                case 'mcq_single':
                case 'true_false':
                    $answerData['answer_options'] = $request->input($fieldName)
                        ? [$request->input($fieldName)]
                        : null;
                    break;

                case 'mcq_multiple':
                    $answerData['answer_options'] = $request->input($fieldName, []);
                    break;

                case 'file_upload':
                    if ($request->hasFile($fieldName)) {
                        $path = $request->file($fieldName)->store('exam-uploads', 'public');
                        $answerData['file_path'] = $path;
                    }
                    break;

                case 'declaration':
                    $answerData['answer_text'] = $request->boolean($fieldName) ? 'Yes' : null;
                    break;

                default:
                    $answerData['answer_text'] = $request->input($fieldName);
                    break;
            }

            if ($existing) {
                $existing->update($answerData);
            } else {
                ParticipantTestAnswer::create($answerData);
            }
        }

        // Mark submitted
        $attempt->update(['status' => 'submitted']);

        // Auto-grade
        ExamService::autoGrade($attempt->fresh()->load('answers.question.options', 'questionSet'));

        return redirect('/exam/' . $token . '/result');
    }

    // ── Result page ───────────────────────────────────────────────────────

    public function result($token)
    {
        $attempt = ParticipantTestAttempt::with([
            'enrollment.trainingSchedule.course',
            'questionSet',
            'answers.question.options',
        ])->where('exam_token', $token)->firstOrFail();

        return view('exam.result', compact('attempt'));
    }
}
