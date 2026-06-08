<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerPortalController extends Controller
{
    // ── Resolve the trainer record for the logged-in user ──

    private function resolveTrainer()
    {
        $trainer = Auth::user()->trainer;

        if (!$trainer) {
            abort(403, 'Your account is not linked to a trainer profile. Please contact admin.');
        }

        return $trainer;
    }

    // ── Dashboard ─────────────────────────────────────────

    public function dashboard()
    {
        $trainer     = $this->resolveTrainer();
        $scheduleIds = $trainer->schedules()->pluck('id');

        $totalSchedules    = $scheduleIds->count();
        $totalParticipants = Enrollment::whereIn('training_schedule_id', $scheduleIds)->count();

        $attendanceSummary = [
            'present' => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('attendance_status', 'Present')->count(),
            'absent'  => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('attendance_status', 'Absent')->count(),
            'partial' => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('attendance_status', 'Partial')->count(),
            'pending' => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('attendance_status', 'Pending')->count(),
        ];

        $completionSummary = [
            'completed'     => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('completion_status', 'Completed')->count(),
            'not_completed' => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('completion_status', 'Not Completed')->count(),
            'pending'       => Enrollment::whereIn('training_schedule_id', $scheduleIds)->where('completion_status', 'Pending')->count(),
        ];

        $upcomingSchedule = TrainingSchedule::with('course')
            ->whereIn('id', $scheduleIds)
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->first();

        $recentSchedules = TrainingSchedule::with(['course', 'enrollments'])
            ->whereIn('id', $scheduleIds)
            ->orderByDesc('start_date')
            ->limit(5)
            ->get();

        return view('trainer.dashboard', compact(
            'trainer',
            'totalSchedules',
            'totalParticipants',
            'attendanceSummary',
            'completionSummary',
            'upcomingSchedule',
            'recentSchedules'
        ));
    }

    // ── My Schedules ──────────────────────────────────────

    public function schedules()
    {
        $trainer = $this->resolveTrainer();

        $schedules = TrainingSchedule::with(['course', 'enrollments'])
            ->where('trainer_id', $trainer->id)
            ->orderByDesc('start_date')
            ->get();

        return view('trainer.schedules', compact('trainer', 'schedules'));
    }

    // ── Participants for one schedule ─────────────────────

    public function participants(TrainingSchedule $schedule)
    {
        $trainer = $this->resolveTrainer();

        abort_unless($schedule->trainer_id === $trainer->id, 403);

        $schedule->load(['course', 'enrollments']);

        return view('trainer.participants', compact('trainer', 'schedule'));
    }

    // ── Update attendance ─────────────────────────────────

    public function updateAttendance(Request $request, Enrollment $enrollment)
    {
        $trainer = $this->resolveTrainer();

        abort_unless(
            $enrollment->trainingSchedule->trainer_id === $trainer->id,
            403
        );

        $request->validate([
            'attendance_status' => 'required|in:Pending,Present,Absent,Partial,Late',
        ]);

        $enrollment->update(['attendance_status' => $request->attendance_status]);

        return back()->with('success', 'Attendance updated.');
    }

    // ── Update completion ─────────────────────────────────

    public function updateCompletion(Request $request, Enrollment $enrollment)
    {
        $trainer = $this->resolveTrainer();

        abort_unless(
            $enrollment->trainingSchedule->trainer_id === $trainer->id,
            403
        );

        $request->validate([
            'completion_status' => 'required|in:Pending,Completed,Not Completed',
        ]);

        $enrollment->update(['completion_status' => $request->completion_status]);

        return back()->with('success', 'Completion status updated.');
    }

    // ── Attendance sheet (trainer-scoped, delegates to AttendanceController) ──

    public function attendanceSheet(TrainingSchedule $schedule)
    {
        $trainer = $this->resolveTrainer();
        abort_unless($schedule->trainer_id === $trainer->id, 403);

        return app(AttendanceController::class)->sheet($schedule);
    }

    public function attendanceSave(\Illuminate\Http\Request $request, TrainingSchedule $schedule)
    {
        $trainer = $this->resolveTrainer();
        abort_unless($schedule->trainer_id === $trainer->id, 403);

        return app(AttendanceController::class)->save($request, $schedule);
    }
}
