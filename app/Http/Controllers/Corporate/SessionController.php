<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateProject;
use App\Models\CorporateSession;
use App\Models\CorporateParticipant;
use App\Models\CorporateAttendance;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $query = CorporateSession::with('project')
            ->withCount('participants');

        if ($request->filled('project_id')) $query->where('corporate_project_id', $request->project_id);
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) =>
                $sq->where('course_name', 'like', "%$q%")
                   ->orWhere('trainer_name', 'like', "%$q%")
                   ->orWhere('venue', 'like', "%$q%")
                   ->orWhereHas('project', fn($pq) => $pq->where('company_name', 'like', "%$q%"))
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('training_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('training_date', '<=', $request->date_to);
        }

        $sessions  = $query->latest('training_date')->paginate(20)->withQueryString();
        $projects  = CorporateProject::orderBy('project_name')->get();

        return view('corporate.sessions.index', compact('sessions', 'projects'));
    }

    public function create(Request $request)
    {
        $projects      = CorporateProject::orderBy('project_name')->get();
        $selectedProject = $request->filled('project_id')
                         ? CorporateProject::find($request->project_id)
                         : null;
        return view('corporate.sessions.form', [
            'session'         => null,
            'projects'        => $projects,
            'selectedProject' => $selectedProject,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'corporate_project_id' => 'required|exists:corporate_projects,id',
            'course_name'          => 'required|string|max:200',
            'trainer_name'         => 'nullable|string|max:150',
            'training_date'        => 'required|date',
            'training_date_end'    => 'nullable|date|after_or_equal:training_date',
            'duration'             => 'nullable|string|max:50',
            'venue'                => 'nullable|string|max:200',
            'target_group'         => 'nullable|string|max:200',
            'description'          => 'nullable|string',
            'status'               => 'required|in:Planned,Ongoing,Completed,Cancelled',
        ]);

        $session = CorporateSession::create($validated);

        return redirect()->route('corporate.sessions.show', $session)
                         ->with('success', 'Training session created.');
    }

    public function show(CorporateSession $session)
    {
        $session->load([
            'project',
            'participants.attendance' => fn($q) => $q->where('corporate_session_id', $session->id),
            'participants.certificate',
            'certificates',
            'evidences',
            'evaluations',
        ]);

        $attendanceSummary = [
            'present'  => $session->attendance()->where('status', 'Present')->count(),
            'absent'   => $session->attendance()->where('status', 'Absent')->count(),
            'partial'  => $session->attendance()->where('status', 'Partial')->count(),
            'total'    => $session->participants()->count(),
        ];

        $avgScore = $session->evaluations()->avg('feedback_score');

        return view('corporate.sessions.show', compact('session', 'attendanceSummary', 'avgScore'));
    }

    public function edit(CorporateSession $session)
    {
        $projects = CorporateProject::orderBy('project_name')->get();
        return view('corporate.sessions.form', [
            'session'         => $session,
            'projects'        => $projects,
            'selectedProject' => $session->project,
        ]);
    }

    public function update(Request $request, CorporateSession $session)
    {
        $validated = $request->validate([
            'corporate_project_id' => 'required|exists:corporate_projects,id',
            'course_name'          => 'required|string|max:200',
            'trainer_name'         => 'nullable|string|max:150',
            'training_date'        => 'required|date',
            'training_date_end'    => 'nullable|date|after_or_equal:training_date',
            'duration'             => 'nullable|string|max:50',
            'venue'                => 'nullable|string|max:200',
            'target_group'         => 'nullable|string|max:200',
            'description'          => 'nullable|string',
            'status'               => 'required|in:Planned,Ongoing,Completed,Cancelled',
        ]);

        $session->update($validated);

        return redirect()->route('corporate.sessions.show', $session)
                         ->with('success', 'Session updated.');
    }

    public function destroy(CorporateSession $session)
    {
        $session->delete();
        return redirect()->route('corporate.sessions.index')
                         ->with('success', 'Session deleted.');
    }
}
