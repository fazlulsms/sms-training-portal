<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateSession;
use App\Models\CorporateParticipant;
use App\Models\CorporateAttendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function sheet(CorporateSession $session)
    {
        $participants = $session->participants()
            ->with(['attendance' => fn($q) => $q->where('corporate_session_id', $session->id)])
            ->orderBy('participant_name')
            ->get();

        // Ensure an attendance record exists for each participant
        foreach ($participants as $p) {
            if (!$p->attendance) {
                $att = CorporateAttendance::firstOrCreate([
                    'corporate_session_id'    => $session->id,
                    'corporate_participant_id' => $p->id,
                ], ['status' => 'Absent']);
                $p->setRelation('attendance', $att);
            }
        }

        $summary = [
            'present' => $participants->filter(fn($p) => $p->attendance?->status === 'Present')->count(),
            'absent'  => $participants->filter(fn($p) => $p->attendance?->status === 'Absent')->count(),
            'partial' => $participants->filter(fn($p) => $p->attendance?->status === 'Partial')->count(),
        ];

        return view('corporate.attendance.sheet', compact('session', 'participants', 'summary'));
    }

    public function save(Request $request, CorporateSession $session)
    {
        $rows = $request->input('attendance', []);   // [participant_id => ['status' => ..., 'remarks' => ...]]

        foreach ($rows as $participantId => $data) {
            CorporateAttendance::updateOrCreate(
                ['corporate_session_id' => $session->id, 'corporate_participant_id' => $participantId],
                [
                    'status'  => $data['status']  ?? 'Absent',
                    'remarks' => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('corporate.sessions.show', $session)
                         ->with('success', 'Attendance saved successfully.');
    }

    // ── CSV Export ───────────────────────────────────────────────
    public function exportCsv(CorporateSession $session)
    {
        $participants = $session->participants()
            ->with(['attendance' => fn($q) => $q->where('corporate_session_id', $session->id)])
            ->orderBy('participant_name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_session_' . $session->id . '_' . now()->format('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($session, $participants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Session', 'Date', 'Course', 'Company']);
            fputcsv($handle, [
                $session->id,
                $session->training_date?->format('d M Y'),
                $session->course_name,
                $session->project->company_name ?? '',
            ]);
            fputcsv($handle, []);
            fputcsv($handle, ['#', 'Participant Name', 'Employee ID', 'Department', 'Position', 'Status', 'Remarks']);
            foreach ($participants as $i => $p) {
                $att = $p->attendance;
                fputcsv($handle, [
                    $i + 1,
                    $p->participant_name,
                    $p->employee_id ?? '',
                    $p->department ?? '',
                    $p->position ?? '',
                    $att?->status ?? 'Not Marked',
                    $att?->remarks ?? '',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function bulkMark(Request $request, CorporateSession $session)
    {
        $request->validate(['status' => 'required|in:Present,Absent,Partial']);
        $status = $request->status;

        $participantIds = $session->participants()->pluck('id');

        foreach ($participantIds as $id) {
            CorporateAttendance::updateOrCreate(
                ['corporate_session_id' => $session->id, 'corporate_participant_id' => $id],
                ['status' => $status]
            );
        }

        return redirect()->route('corporate.sessions.attendance', $session)
                         ->with('success', "All participants marked as {$status}.");
    }
}
