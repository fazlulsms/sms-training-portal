<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingSchedule;
use App\Models\Course;
use App\Models\Trainer;

class TrainingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->input('q');
        $status   = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $schedules = TrainingSchedule::with(['course', 'trainer'])
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->whereHas('course', fn($c) => $c->where('name', 'like', "%$q%"))
                    ->orWhere('batch_code', 'like', "%$q%")
                    ->orWhere('venue', 'like', "%$q%")
                    ->orWhereHas('trainer', fn($t) => $t->where('name', 'like', "%$q%"))
            ))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($dateFrom, fn($query) => $query->where('start_date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->where('start_date', '<=', $dateTo))
            ->orderByRaw("FIELD(status, 'Open', 'Closed', 'Postponed', 'Completed', 'Cancelled')")
            ->orderBy('start_date', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('training_schedules.index', compact('schedules'));
    }

    public function exportCsv(Request $request)
    {
        $q        = $request->input('q');
        $status   = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $schedules = TrainingSchedule::with(['course', 'trainer'])
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->whereHas('course', fn($c) => $c->where('name', 'like', "%$q%"))
                    ->orWhere('batch_code', 'like', "%$q%")
                    ->orWhere('venue', 'like', "%$q%")
                    ->orWhereHas('trainer', fn($t) => $t->where('name', 'like', "%$q%"))
            ))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($dateFrom, fn($query) => $query->where('start_date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->where('start_date', '<=', $dateTo))
            ->orderBy('start_date', 'asc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="schedules_' . now()->format('Ymd') . '.csv"',
        ];

        return response()->stream(function () use ($schedules) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Course', 'Batch Code', 'Trainer', 'Start Date', 'End Date', 'Mode', 'Venue', 'Status']);
            foreach ($schedules as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->course->name ?? '',
                    $s->batch_code,
                    $s->trainer->name ?? '',
                    $s->start_date,
                    $s->end_date,
                    $s->training_mode,
                    $s->venue,
                    $s->status,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function create()
    {
        $courses = Course::where('status', 1)->orderBy('name')->get();
        $trainers = Trainer::orderBy('name')->get();

        return view('training_schedules.create', compact('courses', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'trainer_id' => 'required|exists:trainers,id',
            'batch_code' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'nullable|string|max:255',
            'training_mode' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'physical_fee' => 'nullable|numeric|min:0',
            'online_fee' => 'nullable|numeric|min:0',
            'venue' => 'nullable|string|max:255',
            'zoom_link' => 'nullable|string|max:500',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:Open,Closed,Completed,Postponed,Cancelled',
        ]);

        $data = $request->only([
            'course_id', 'trainer_id', 'batch_code', 'training_title',
            'start_date', 'end_date', 'duration', 'training_mode', 'currency',
            'physical_fee', 'online_fee', 'discount_fee',
            'venue', 'city', 'country', 'zoom_link', 'max_participants',
            'available_seats', 'status', 'schedule_status',
            'registration_deadline', 'time_start', 'time_end',
        ]);
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');

        if (empty($data['training_title'])) {
            $data['training_title'] = Course::find($data['course_id'])?->name ?? '';
        }

        TrainingSchedule::create($data);

        return redirect('/training-schedules')->with('success', 'Training Schedule Added Successfully');
    }

    public function edit($id)
    {
        $schedule = TrainingSchedule::findOrFail($id);
        $courses = Course::where('status', 1)->orderBy('name')->get();
        $trainers = Trainer::orderBy('name')->get();

        return view('training_schedules.edit', compact('schedule', 'courses', 'trainers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'trainer_id' => 'required|exists:trainers,id',
            'batch_code' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'nullable|string|max:255',
            'training_mode' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'physical_fee' => 'nullable|numeric|min:0',
            'online_fee' => 'nullable|numeric|min:0',
            'venue' => 'nullable|string|max:255',
            'zoom_link' => 'nullable|string|max:500',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:Open,Closed,Completed,Postponed,Cancelled',
        ]);

        $schedule = TrainingSchedule::findOrFail($id);

        $data = $request->only([
            'course_id', 'trainer_id', 'batch_code', 'training_title',
            'start_date', 'end_date', 'duration', 'training_mode', 'currency',
            'physical_fee', 'online_fee', 'discount_fee',
            'venue', 'city', 'country', 'zoom_link', 'max_participants',
            'available_seats', 'status', 'schedule_status',
            'registration_deadline', 'time_start', 'time_end',
        ]);
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');

        if (empty($data['training_title'])) {
            $data['training_title'] = Course::find($data['course_id'])?->name ?? $schedule->training_title ?? '';
        }

        $schedule->update($data);

        return redirect('/training-schedules')->with('success', 'Training Schedule Updated Successfully');
    }

    public function delete($id)
    {
        TrainingSchedule::findOrFail($id)->delete();

        return redirect('/training-schedules')->with('success', 'Training Schedule Deleted Successfully');
    }
}