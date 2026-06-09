<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\TrainingAttendance;
use App\Models\TrainingQuestionAssignment;
use App\Models\TrainingSchedule;
use App\Services\ExamService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Show the attendance sheet for a schedule.
     * Auto-initialises missing attendance records for all enrollments × all session dates.
     */
    public function sheet(TrainingSchedule $schedule)
    {
        $schedule->load(['course', 'enrollments', 'trainer']);

        $sessionDates = $this->getSessionDates($schedule);

        if ($sessionDates->isEmpty()) {
            return back()->with('error', 'This schedule has no valid session dates.');
        }

        // Auto-create missing attendance records
        foreach ($schedule->enrollments as $enrollment) {
            foreach ($sessionDates as $index => $date) {
                TrainingAttendance::firstOrCreate(
                    [
                        'schedule_id'   => $schedule->id,
                        'enrollment_id' => $enrollment->id,
                        'session_date'  => $date->toDateString(),
                    ],
                    [
                        'session_label' => 'Day ' . ($index + 1),
                        'status'        => 'Pending',
                        'marked_by'     => Auth::id(),
                    ]
                );
            }
        }

        // Load all attendance records keyed by [enrollment_id][date]
        $records = TrainingAttendance::where('schedule_id', $schedule->id)
            ->get()
            ->groupBy('enrollment_id')
            ->map(fn ($group) => $group->keyBy(fn ($r) => $r->session_date->toDateString()));

        return view('attendance.sheet', compact('schedule', 'sessionDates', 'records'));
    }

    /**
     * Batch-save all attendance statuses from the sheet form.
     */
    public function save(Request $request, TrainingSchedule $schedule)
    {
        $data = $request->input('attendance', []);

        foreach ($data as $enrollmentId => $dates) {
            foreach ($dates as $date => $fields) {
                TrainingAttendance::where('schedule_id', $schedule->id)
                    ->where('enrollment_id', $enrollmentId)
                    ->where('session_date', $date)
                    ->update([
                        'status'         => $fields['status']        ?? 'Pending',
                        'check_in_time'  => $fields['check_in']  ?: null,
                        'check_out_time' => $fields['check_out'] ?: null,
                        'remarks'        => $fields['remarks']       ?? null,
                        'marked_by'      => Auth::id(),
                    ]);
            }
        }

        // Recalculate summary attendance_status for each enrollment
        $sessionDates = $this->getSessionDates($schedule);
        $totalSessions = $sessionDates->count();

        foreach ($schedule->enrollments as $enrollment) {
            $this->syncEnrollmentAttendance($enrollment, $schedule->id, $totalSessions);
        }

        return back()->with('success', 'Attendance saved successfully.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function getSessionDates(TrainingSchedule $schedule): \Illuminate\Support\Collection
    {
        if (!$schedule->start_date || !$schedule->end_date) {
            return collect();
        }

        $period = CarbonPeriod::create(
            Carbon::parse($schedule->start_date),
            Carbon::parse($schedule->end_date)
        );

        return collect($period)->map(fn ($date) => $date);
    }

    private function syncEnrollmentAttendance(Enrollment $enrollment, int $scheduleId, int $totalSessions): void
    {
        if ($totalSessions === 0) return;

        $presentCount = TrainingAttendance::where('schedule_id', $scheduleId)
            ->where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        $percentage = round(($presentCount / $totalSessions) * 100);

        $summary = match (true) {
            $percentage === 0             => 'Absent',
            $percentage >= 80             => 'Present',
            default                       => 'Partial',
        };

        $oldStatus = $enrollment->attendance_status;
        $enrollment->update(['attendance_status' => $summary]);

        // Send exam email if newly marked as attended
        $attended = ['Present', 'Partial', 'Late'];
        if (in_array($summary, $attended) && !in_array($oldStatus, $attended)) {
            $assignment = TrainingQuestionAssignment::where('training_schedule_id', $scheduleId)->first();
            if ($assignment && $assignment->exam_active_after_attendance && !$enrollment->exam_email_sent) {
                ExamService::sendExamEmail($enrollment->fresh());
            }
        }
    }
}
