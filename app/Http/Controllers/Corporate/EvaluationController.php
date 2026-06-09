<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateSession;
use App\Models\CorporateEvaluation;
use App\Models\CorporateParticipant;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(CorporateSession $session)
    {
        $evaluations = $session->evaluations()->with('participant')->latest()->get();
        $participants = $session->participants()->orderBy('participant_name')->get();
        $avgScore = $evaluations->avg('feedback_score');

        return view('corporate.evaluation.index', compact('session', 'evaluations', 'participants', 'avgScore'));
    }

    public function store(Request $request, CorporateSession $session)
    {
        $request->validate([
            'corporate_participant_id' => 'nullable|exists:corporate_participants,id',
            'evaluator_name'           => 'nullable|string|max:150',
            'feedback_score'           => 'required|integer|min:1|max:5',
            'comments'                 => 'nullable|string',
            'effectiveness_notes'      => 'nullable|string',
        ]);

        CorporateEvaluation::create([
            'corporate_session_id'    => $session->id,
            'corporate_participant_id' => $request->corporate_participant_id ?: null,
            'evaluator_name'           => $request->evaluator_name ?: null,
            'feedback_score'           => $request->feedback_score,
            'comments'                 => $request->comments,
            'effectiveness_notes'      => $request->effectiveness_notes,
        ]);

        return back()->with('success', 'Evaluation recorded.');
    }

    public function destroy(CorporateSession $session, CorporateEvaluation $evaluation)
    {
        $evaluation->delete();
        return back()->with('success', 'Evaluation deleted.');
    }
}
