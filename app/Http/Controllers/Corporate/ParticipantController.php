<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateSession;
use App\Models\CorporateParticipant;
use App\Models\CorporateAttendance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParticipantController extends Controller
{
    public function index(Request $request, CorporateSession $session)
    {
        $q          = $request->input('q');
        $department = $request->input('department');

        $departments = $session->participants()
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $participants = $session->participants()
            ->with(['attendance' => fn($q) => $q->where('corporate_session_id', $session->id), 'certificate'])
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('participant_name', 'like', "%$q%")
                    ->orWhere('employee_id', 'like', "%$q%")
                    ->orWhere('position', 'like', "%$q%")
                    ->orWhere('department', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
            ))
            ->when($department, fn($query) => $query->where('department', $department))
            ->orderBy('participant_name')
            ->paginate(50)
            ->withQueryString();

        return view('corporate.participants.index', compact('session', 'participants', 'departments'));
    }

    public function create(CorporateSession $session)
    {
        return view('corporate.participants.form', ['session' => $session, 'participant' => null]);
    }

    public function store(Request $request, CorporateSession $session)
    {
        $validated = $request->validate([
            'participant_name' => 'required|string|max:150',
            'employee_id'      => 'nullable|string|max:50',
            'position'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:150',
            'contact_number'   => 'nullable|string|max:30',
        ]);

        $validated['corporate_project_id'] = $session->corporate_project_id;
        $validated['corporate_session_id']  = $session->id;

        CorporateParticipant::create($validated);

        return redirect()->route('corporate.sessions.participants.index', $session)
                         ->with('success', 'Participant added.');
    }

    public function edit(CorporateSession $session, CorporateParticipant $participant)
    {
        return view('corporate.participants.form', compact('session', 'participant'));
    }

    public function update(Request $request, CorporateSession $session, CorporateParticipant $participant)
    {
        $validated = $request->validate([
            'participant_name' => 'required|string|max:150',
            'employee_id'      => 'nullable|string|max:50',
            'position'         => 'nullable|string|max:100',
            'department'       => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:150',
            'contact_number'   => 'nullable|string|max:30',
        ]);

        $participant->update($validated);

        return redirect()->route('corporate.sessions.participants.index', $session)
                         ->with('success', 'Participant updated.');
    }

    public function destroy(CorporateSession $session, CorporateParticipant $participant)
    {
        $participant->delete();
        return redirect()->route('corporate.sessions.participants.index', $session)
                         ->with('success', 'Participant removed.');
    }

    // ── Bulk delete ─────────────────────────────────────────────
    public function bulkDestroy(Request $request, CorporateSession $session)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            CorporateParticipant::whereIn('id', $ids)
                ->where('corporate_session_id', $session->id)
                ->delete();
        }
        return redirect()->route('corporate.sessions.participants.index', $session)
                         ->with('success', count($ids) . ' participant(s) deleted.');
    }

    // ── CSV Import ───────────────────────────────────────────────
    public function importCsv(Request $request, CorporateSession $session)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header row
        $count  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;
            CorporateParticipant::create([
                'corporate_project_id' => $session->corporate_project_id,
                'corporate_session_id'  => $session->id,
                'participant_name'      => trim($row[0] ?? ''),
                'employee_id'           => trim($row[1] ?? '') ?: null,
                'position'              => trim($row[2] ?? '') ?: null,
                'department'            => trim($row[3] ?? '') ?: null,
                'email'                 => trim($row[4] ?? '') ?: null,
                'contact_number'        => trim($row[5] ?? '') ?: null,
            ]);
            $count++;
        }
        fclose($handle);

        return redirect()->route('corporate.sessions.participants.index', $session)
                         ->with('success', "$count participant(s) imported.");
    }

    // ── CSV Export ───────────────────────────────────────────────
    public function exportCsv(CorporateSession $session)
    {
        $participants = $session->participants()
            ->with(['attendance' => fn($q) => $q->where('corporate_session_id', $session->id), 'certificate'])
            ->orderBy('participant_name')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="participants_' . $session->id . '.csv"',
        ];

        $callback = function () use ($participants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Participant Name', 'Employee ID', 'Position', 'Department', 'Email', 'Contact Number', 'Attendance', 'Certificate No.']);
            foreach ($participants as $p) {
                fputcsv($handle, [
                    $p->participant_name,
                    $p->employee_id,
                    $p->position,
                    $p->department,
                    $p->email,
                    $p->contact_number,
                    $p->attendance->status ?? 'Not Marked',
                    $p->certificate->certificate_number ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── CSV Template ─────────────────────────────────────────────
    public function csvTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="participants_template.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Participant Name *', 'Employee ID', 'Position', 'Department', 'Email', 'Contact Number']);
            fputcsv($handle, ['Md. Fazlul Haque', 'EMP-001', 'Safety Officer', 'HSE', 'fazlul@example.com', '+8801700000000']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
