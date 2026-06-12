<?php

namespace App\Http\Controllers;

use App\Models\FeedbackAssignment;
use App\Models\FeedbackResponse;
use App\Models\FeedbackTemplate;
use App\Models\Course;
use App\Models\TrainingSchedule;
use Illuminate\Http\Request;

class FeedbackResponseController extends Controller
{
    public function index(Request $request)
    {
        $query = FeedbackResponse::with(['assignment.template', 'user'])
            ->where('is_complete', true)
            ->orderByDesc('submitted_at');

        if ($request->filled('type')) {
            $query->whereHas('assignment.template', fn($q) => $q->where('type', $request->type));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('respondent_name', 'like', "%$s%")
                ->orWhere('respondent_email', 'like', "%$s%"));
        }

        $responses   = $query->paginate(25)->withQueryString();
        $totalCount  = FeedbackResponse::where('is_complete', true)->count();
        $pendingCount = FeedbackResponse::where('is_complete', false)->count();
        $avgRating   = FeedbackResponse::where('is_complete', true)->whereNotNull('overall_rating')->avg('overall_rating');

        return view('feedback.responses.index', compact('responses', 'totalCount', 'pendingCount', 'avgRating'));
    }

    public function show(FeedbackResponse $response)
    {
        $response->load(['assignment.template.questions', 'answers.question', 'user']);
        return view('feedback.responses.show', compact('response'));
    }

    public function destroy(FeedbackResponse $response)
    {
        $response->delete();
        return back()->with('success', 'Response deleted.');
    }

    public function approveTestimonial(FeedbackResponse $response)
    {
        $response->update(['testimonial_approved' => true]);
        return back()->with('success', 'Testimonial approved for public display.');
    }

    // Assign a template to a course or schedule
    public function assign(Request $request)
    {
        $data = $request->validate([
            'template_id'             => 'required|exists:feedback_templates,id',
            'assignable_type'         => 'required|in:elearning_course,training_schedule',
            'assignable_id'           => 'required|integer',
            'is_required'             => 'boolean',
            'require_for_certificate' => 'boolean',
            'due_days_after_completion' => 'integer|min:0|max:90',
        ]);

        $assignment = FeedbackAssignment::updateOrCreate(
            [
                'template_id'     => $data['template_id'],
                'assignable_type' => $data['assignable_type'],
                'assignable_id'   => $data['assignable_id'],
            ],
            [
                'is_required'               => $request->boolean('is_required'),
                'require_for_certificate'   => $request->boolean('require_for_certificate'),
                'due_days_after_completion' => $data['due_days_after_completion'] ?? 7,
                'is_active'                 => true,
            ]
        );

        return back()->with('success', 'Feedback template assigned.');
    }
}
