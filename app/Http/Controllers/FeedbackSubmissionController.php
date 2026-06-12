<?php

namespace App\Http\Controllers;

use App\Models\FeedbackAnswer;
use App\Models\FeedbackQuestion;
use App\Models\FeedbackResponse;
use Illuminate\Http\Request;

class FeedbackSubmissionController extends Controller
{
    public function show(string $token)
    {
        $response = FeedbackResponse::where('token', $token)
            ->with(['assignment.template.questions', 'answers'])
            ->firstOrFail();

        // Already submitted — show thank you unless allow_multiple
        if ($response->is_complete && !$response->assignment->template->allow_multiple) {
            return view('feedback.thankyou', compact('response'));
        }

        $template  = $response->assignment->template;
        $questions = $template->questions;
        $isDemo    = app()->environment('local', 'staging') || $template->allow_multiple;

        return view('feedback.submit', compact('response', 'template', 'questions', 'isDemo'));
    }

    public function submit(Request $request, string $token)
    {
        $response = FeedbackResponse::where('token', $token)
            ->with(['assignment.template.questions'])
            ->firstOrFail();

        $template = $response->assignment->template;

        if ($response->is_complete && !$template->allow_multiple) {
            return redirect()->route('feedback.show', $token);
        }

        $questions = $template->questions;

        // Validate required questions
        $rules = [];
        foreach ($questions as $q) {
            $key = 'answers.' . $q->id;
            if ($q->is_required) {
                $rules[$key] = match ($q->question_type) {
                    'rating_5' => 'required|integer|min:1|max:5',
                    'yes_no'   => 'required|boolean',
                    'text'     => 'required|string|max:2000',
                    default    => 'required',
                };
            } else {
                $rules[$key] = 'nullable';
            }
        }
        $request->validate($rules);

        $answers     = $request->input('answers', []);
        $ratingTotal = 0;
        $ratingCount = 0;

        // Clear old answers if allow_multiple
        if ($response->is_complete && $template->allow_multiple) {
            $response->answers()->delete();
        }

        foreach ($questions as $q) {
            $raw = $answers[$q->id] ?? null;
            if ($raw === null) continue;

            $answerData = match ($q->question_type) {
                'rating_5' => ['answer_rating' => (int) $raw, 'answer_bool' => null, 'answer_text' => null],
                'yes_no'   => ['answer_rating' => null, 'answer_bool' => (bool) $raw, 'answer_text' => null],
                default    => ['answer_rating' => null, 'answer_bool' => null, 'answer_text' => (string) $raw],
            };

            FeedbackAnswer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $q->id],
                $answerData
            );

            if ($q->question_type === 'rating_5' && $answerData['answer_rating']) {
                $ratingTotal += $answerData['answer_rating'];
                $ratingCount++;
            }
        }

        $overallRating = $ratingCount > 0 ? round($ratingTotal / $ratingCount, 2) : null;

        $updateData = [
            'is_complete'         => true,
            'submitted_at'        => now(),
            'is_demo'             => app()->environment('local', 'staging') || $template->allow_multiple,
            'testimonial_consent' => $request->boolean('testimonial_consent'),
            'testimonial_text'    => $request->input('testimonial_text'),
            'overall_rating'      => $overallRating,
        ];

        if (!$response->user_id) {
            if ($request->filled('respondent_name'))  $updateData['respondent_name']  = $request->respondent_name;
            if ($request->filled('respondent_email')) $updateData['respondent_email'] = $request->respondent_email;
        }

        $response->update($updateData);

        return redirect()->route('feedback.thankyou', $token);
    }

    public function thankyou(string $token)
    {
        $response = FeedbackResponse::where('token', $token)
            ->with('assignment.template')
            ->firstOrFail();

        return view('feedback.thankyou', compact('response'));
    }
}
