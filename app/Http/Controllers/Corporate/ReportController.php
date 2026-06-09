<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateProject;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function projectReport(CorporateProject $project)
    {
        $project->load([
            'sessions' => function ($q) {
                $q->with([
                    'participants' => function ($pq) {
                        $pq->with([
                            'attendance',   // scoped to session via parent eager load
                            'certificate',
                        ]);
                    },
                    'certificates',
                    'evidences',
                    'evaluations',
                ])->withCount('participants')->orderBy('training_date');
            },
        ]);

        $totals = [
            'sessions'      => $project->sessions->count(),
            'participants'  => $project->sessions->sum('participants_count'),
            'certificates'  => $project->certificates()->count(),
            'present'       => 0,
            'absent'        => 0,
        ];

        foreach ($project->sessions as $session) {
            foreach ($session->participants as $p) {
                $att = $p->attendance;
                if ($att?->status === 'Present') $totals['present']++;
                elseif ($att?->status === 'Absent') $totals['absent']++;
            }
        }

        return view('corporate.reports.project', compact('project', 'totals'));
    }
}
