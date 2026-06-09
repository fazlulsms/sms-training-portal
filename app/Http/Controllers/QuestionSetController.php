<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionSetController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = QuestionSet::withCount('questions')->latest();

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $questionSets = $query->paginate(20)->withQueryString();

        return view('question-sets.index', compact('questionSets'));
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function create()
    {
        $courses = Course::orderBy('name')->get();
        return view('question-sets.create', compact('courses'));
    }

    // ── Store ─────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                       => 'required|string|max:255',
            'description'                 => 'nullable|string',
            'course_id'                   => 'nullable|exists:courses,id',
            'status'                      => 'required|in:Active,Inactive',
            'total_marks'                 => 'required|integer|min:1|max:10000',
            'pass_mark'                   => 'nullable|integer|min:1',
            'pass_percentage'             => 'nullable|integer|min:1|max:100',
            'allowed_attempts'            => 'required|integer|min:1|max:10',
            'time_limit_minutes'          => 'nullable|integer|min:1|max:600',
            'show_result_to_participant'  => 'nullable|boolean',
            'allow_certificate_after_pass'=> 'nullable|boolean',
        ]);

        $validated['created_by']                    = Auth::user()->name ?? Auth::user()->email;
        $validated['show_result_to_participant']     = $request->boolean('show_result_to_participant');
        $validated['allow_certificate_after_pass']  = $request->boolean('allow_certificate_after_pass');

        $qs = QuestionSet::create($validated);

        return redirect("/admin/question-sets/{$qs->id}/questions")
            ->with('success', 'Question set created. Now add your questions.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $questionSet = QuestionSet::findOrFail($id);
        $courses     = Course::orderBy('name')->get();
        return view('question-sets.edit', compact('questionSet', 'courses'));
    }

    // ── Update ────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $questionSet = QuestionSet::findOrFail($id);

        $validated = $request->validate([
            'title'                       => 'required|string|max:255',
            'description'                 => 'nullable|string',
            'course_id'                   => 'nullable|exists:courses,id',
            'status'                      => 'required|in:Active,Inactive',
            'total_marks'                 => 'required|integer|min:1',
            'pass_mark'                   => 'nullable|integer|min:1',
            'pass_percentage'             => 'nullable|integer|min:1|max:100',
            'allowed_attempts'            => 'required|integer|min:1|max:10',
            'time_limit_minutes'          => 'nullable|integer|min:1|max:600',
            'show_result_to_participant'  => 'nullable|boolean',
            'allow_certificate_after_pass'=> 'nullable|boolean',
        ]);

        $validated['show_result_to_participant']     = $request->boolean('show_result_to_participant');
        $validated['allow_certificate_after_pass']  = $request->boolean('allow_certificate_after_pass');

        $questionSet->update($validated);

        return redirect("/admin/question-sets")->with('success', 'Question set updated.');
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function delete($id)
    {
        QuestionSet::findOrFail($id)->delete();
        return redirect('/admin/question-sets')->with('success', 'Question set deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // QUESTION MANAGEMENT (sub-resource)
    // ─────────────────────────────────────────────────────────────────────

    public function questions($id)
    {
        $questionSet = QuestionSet::with(['questions.options'])->findOrFail($id);
        $types       = Question::TYPES;
        return view('question-sets.questions', compact('questionSet', 'types'));
    }

    public function storeQuestion(Request $request, $id)
    {
        $questionSet = QuestionSet::findOrFail($id);

        $validated = $request->validate([
            'question_text'         => 'required|string',
            'question_type'         => 'required|in:' . implode(',', array_keys(Question::TYPES)),
            'is_required'           => 'nullable|boolean',
            'marks'                 => 'required|integer|min:0',
            'correct_answer'        => 'nullable|string|max:500',
            'exact_match_required'  => 'nullable|boolean',
            'manual_review_required'=> 'nullable|boolean',
            'sort_order'            => 'nullable|integer|min:0',
            'options'               => 'nullable|array',
            'options.*.text'        => 'required_with:options|string|max:500',
            'options.*.is_correct'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($questionSet, $validated, $request) {
            $question = $questionSet->questions()->create([
                'question_text'          => $validated['question_text'],
                'question_type'          => $validated['question_type'],
                'is_required'            => $request->boolean('is_required'),
                'marks'                  => $validated['marks'],
                'correct_answer'         => $validated['correct_answer'] ?? null,
                'exact_match_required'   => $request->boolean('exact_match_required'),
                'manual_review_required' => $request->boolean('manual_review_required'),
                'sort_order'             => $validated['sort_order'] ?? 0,
            ]);

            // For true/false auto-create options
            if ($validated['question_type'] === 'true_false') {
                $correctAnswer = strtolower($validated['correct_answer'] ?? 'true');
                foreach (['True', 'False'] as $idx => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt,
                        'is_correct'  => strtolower($opt) === $correctAnswer,
                        'sort_order'  => $idx,
                    ]);
                }
            } elseif (in_array($validated['question_type'], ['mcq_single', 'mcq_multiple'])) {
                foreach ($request->input('options', []) as $idx => $opt) {
                    if (!empty($opt['text'])) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $opt['text'],
                            'is_correct'  => !empty($opt['is_correct']),
                            'sort_order'  => $idx,
                        ]);
                    }
                }
            }
        });

        return redirect("/admin/question-sets/{$id}/questions")->with('success', 'Question added.');
    }

    public function editQuestion($setId, $qId)
    {
        $questionSet = QuestionSet::findOrFail($setId);
        $question    = Question::with('options')->where('question_set_id', $setId)->findOrFail($qId);
        $types       = Question::TYPES;
        return view('question-sets.edit-question', compact('questionSet', 'question', 'types'));
    }

    public function updateQuestion(Request $request, $setId, $qId)
    {
        $questionSet = QuestionSet::findOrFail($setId);
        $question    = Question::where('question_set_id', $setId)->findOrFail($qId);

        $validated = $request->validate([
            'question_text'         => 'required|string',
            'question_type'         => 'required|in:' . implode(',', array_keys(Question::TYPES)),
            'is_required'           => 'nullable|boolean',
            'marks'                 => 'required|integer|min:0',
            'correct_answer'        => 'nullable|string|max:500',
            'exact_match_required'  => 'nullable|boolean',
            'manual_review_required'=> 'nullable|boolean',
            'sort_order'            => 'nullable|integer|min:0',
            'options'               => 'nullable|array',
            'options.*.text'        => 'nullable|string|max:500',
            'options.*.is_correct'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($question, $validated, $request) {
            $question->update([
                'question_text'          => $validated['question_text'],
                'question_type'          => $validated['question_type'],
                'is_required'            => $request->boolean('is_required'),
                'marks'                  => $validated['marks'],
                'correct_answer'         => $validated['correct_answer'] ?? null,
                'exact_match_required'   => $request->boolean('exact_match_required'),
                'manual_review_required' => $request->boolean('manual_review_required'),
                'sort_order'             => $validated['sort_order'] ?? 0,
            ]);

            $question->options()->delete();

            if ($validated['question_type'] === 'true_false') {
                $correctAnswer = strtolower($validated['correct_answer'] ?? 'true');
                foreach (['True', 'False'] as $idx => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt,
                        'is_correct'  => strtolower($opt) === $correctAnswer,
                        'sort_order'  => $idx,
                    ]);
                }
            } elseif (in_array($validated['question_type'], ['mcq_single', 'mcq_multiple'])) {
                foreach ($request->input('options', []) as $idx => $opt) {
                    if (!empty($opt['text'])) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $opt['text'],
                            'is_correct'  => !empty($opt['is_correct']),
                            'sort_order'  => $idx,
                        ]);
                    }
                }
            }
        });

        return redirect("/admin/question-sets/{$setId}/questions")->with('success', 'Question updated.');
    }

    public function deleteQuestion($setId, $qId)
    {
        Question::where('question_set_id', $setId)->findOrFail($qId)->delete();
        return redirect("/admin/question-sets/{$setId}/questions")->with('success', 'Question deleted.');
    }
}
