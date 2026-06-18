<?php

namespace App\Http\Controllers;

use App\Models\FeedbackTemplate;
use App\Models\FeedbackQuestion;
use Illuminate\Http\Request;

class FeedbackTemplateController extends Controller
{
    public function index()
    {
        $templates = FeedbackTemplate::withCount(['questions', 'assignments', 'assignments as responses_count' => fn($q) => $q->join('feedback_responses', 'feedback_assignments.id', '=', 'feedback_responses.assignment_id')->where('feedback_responses.is_complete', true)])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('feedback.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('feedback.templates.create', [
            'types'      => FeedbackTemplate::$TYPES,
            'qTypes'     => FeedbackQuestion::$TYPES,
            'categories' => FeedbackQuestion::$CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:150',
            'type'                    => 'required|in:ilt,elearning,webinar,workshop,trainer',
            'description'             => 'nullable|string|max:500',
            'is_active'               => 'boolean',
            'allow_multiple'          => 'boolean',
            'require_for_certificate' => 'boolean',
            'questions'               => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.question_type' => 'required|in:rating_5,yes_no,text,select',
            'questions.*.category'      => 'required|in:overall,content,trainer,platform,elearning,open',
            'questions.*.is_required'   => 'nullable|boolean',
        ]);

        $template = FeedbackTemplate::create([
            'name'                    => $data['name'],
            'type'                    => $data['type'],
            'description'             => $data['description'] ?? null,
            'is_active'               => $request->boolean('is_active', true),
            'allow_multiple'          => $request->boolean('allow_multiple'),
            'require_for_certificate' => $request->boolean('require_for_certificate'),
            'created_by'              => auth()->id(),
        ]);

        foreach ($data['questions'] as $i => $q) {
            FeedbackQuestion::create([
                'template_id'   => $template->id,
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'category'      => $q['category'],
                'is_required'   => isset($q['is_required']) ? (bool) $q['is_required'] : true,
                'sort_order'    => $i,
            ]);
        }

        return redirect()->route('feedback.templates.index')
            ->with('success', "Template \"{$template->name}\" created with " . count($data['questions']) . ' questions.');
    }

    public function show(FeedbackTemplate $template)
    {
        $template->load(['questions', 'assignments.responses']);
        $totalResponses = $template->assignments->sum(fn($a) => $a->responses->where('is_complete', true)->count());

        return view('feedback.templates.show', compact('template', 'totalResponses'));
    }

    public function preview(FeedbackTemplate $template)
    {
        $template->load('questions');
        return view('feedback.preview', compact('template'));
    }

    public function edit(FeedbackTemplate $template)
    {
        $template->load('questions');
        return view('feedback.templates.edit', [
            'template'   => $template,
            'types'      => FeedbackTemplate::$TYPES,
            'qTypes'     => FeedbackQuestion::$TYPES,
            'categories' => FeedbackQuestion::$CATEGORIES,
        ]);
    }

    public function update(Request $request, FeedbackTemplate $template)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:150',
            'type'                    => 'required|in:ilt,elearning,webinar,workshop,trainer',
            'description'             => 'nullable|string|max:500',
            'allow_multiple'          => 'boolean',
            'require_for_certificate' => 'boolean',
            'questions'               => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.question_type' => 'required|in:rating_5,yes_no,text,select',
            'questions.*.category'      => 'required|in:overall,content,trainer,platform,elearning,open',
            'questions.*.is_required'   => 'nullable|boolean',
        ]);

        $template->update([
            'name'                    => $data['name'],
            'type'                    => $data['type'],
            'description'             => $data['description'] ?? null,
            'is_active'               => $request->boolean('is_active', true),
            'allow_multiple'          => $request->boolean('allow_multiple'),
            'require_for_certificate' => $request->boolean('require_for_certificate'),
        ]);

        // Replace questions
        $template->questions()->delete();
        foreach ($data['questions'] as $i => $q) {
            FeedbackQuestion::create([
                'template_id'   => $template->id,
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'category'      => $q['category'],
                'is_required'   => isset($q['is_required']) ? (bool) $q['is_required'] : true,
                'sort_order'    => $i,
            ]);
        }

        return redirect()->route('feedback.templates.show', $template)
            ->with('success', 'Template updated successfully.');
    }

    public function clone(FeedbackTemplate $template)
    {
        $template->load('questions');

        $clone = $template->replicate(['is_default']);
        $clone->name       = 'Copy of ' . $template->name;
        $clone->is_default = false;
        $clone->created_by = auth()->id();
        $clone->save();

        foreach ($template->questions as $q) {
            $clone->questions()->create($q->only([
                'question_text', 'question_type', 'category', 'options', 'is_required', 'sort_order',
            ]));
        }

        return redirect()->route('feedback.templates.edit', $clone)
            ->with('success', "Template cloned. Edit \"{$clone->name}\" below.");
    }

    public function destroy(FeedbackTemplate $template)
    {
        if ($template->is_default) {
            return back()->with('error', 'Default templates cannot be deleted.');
        }

        $template->delete();
        return redirect()->route('feedback.templates.index')
            ->with('success', 'Template deleted.');
    }
}
