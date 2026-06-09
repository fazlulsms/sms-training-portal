<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateProject;
use App\Models\CorporateSession;
use App\Models\CorporateCertificate;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = CorporateProject::withCount(['sessions', 'participants', 'certificates']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) =>
                $sq->where('project_name',   'like', "%$q%")
                   ->orWhere('company_name',  'like', "%$q%")
                   ->orWhere('contact_person','like', "%$q%")
                   ->orWhere('email',         'like', "%$q%")
            );
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $projects = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_projects'      => CorporateProject::count(),
            'active_projects'     => CorporateProject::where('status', 'Active')->count(),
            'total_sessions'      => CorporateSession::count(),
            'total_certificates'  => CorporateCertificate::count(),
        ];

        return view('corporate.projects.index', compact('projects', 'stats'));
    }

    public function create()
    {
        return view('corporate.projects.form', ['project' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name'         => 'required|string|max:200',
            'company_name'         => 'required|string|max:200',
            'address'              => 'nullable|string',
            'contact_person'       => 'nullable|string|max:120',
            'contact_designation'  => 'nullable|string|max:120',
            'email'                => 'nullable|email|max:150',
            'phone'                => 'nullable|string|max:30',
            'status'               => 'required|in:Active,Completed,On Hold,Cancelled',
            'remarks'              => 'nullable|string',
        ]);

        $project = CorporateProject::create($validated);

        return redirect()->route('corporate.projects.show', $project)
                         ->with('success', 'Project created successfully.');
    }

    public function show(CorporateProject $project)
    {
        $project->load([
            'sessions' => fn($q) => $q->withCount('participants')
                                      ->with('certificates'),
        ]);

        $stats = [
            'total_sessions'     => $project->sessions->count(),
            'total_participants' => $project->participants()->count(),
            'total_certificates' => $project->certificates()->count(),
        ];

        return view('corporate.projects.show', compact('project', 'stats'));
    }

    public function edit(CorporateProject $project)
    {
        return view('corporate.projects.form', compact('project'));
    }

    public function update(Request $request, CorporateProject $project)
    {
        $validated = $request->validate([
            'project_name'         => 'required|string|max:200',
            'company_name'         => 'required|string|max:200',
            'address'              => 'nullable|string',
            'contact_person'       => 'nullable|string|max:120',
            'contact_designation'  => 'nullable|string|max:120',
            'email'                => 'nullable|email|max:150',
            'phone'                => 'nullable|string|max:30',
            'status'               => 'required|in:Active,Completed,On Hold,Cancelled',
            'remarks'              => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('corporate.projects.show', $project)
                         ->with('success', 'Project updated.');
    }

    public function destroy(CorporateProject $project)
    {
        $project->delete();
        return redirect()->route('corporate.projects.index')
                         ->with('success', 'Project deleted.');
    }
}
