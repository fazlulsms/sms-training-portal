<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingSchedule;
use App\Models\Course;
use App\Models\Trainer;

class TrainingScheduleController extends Controller
{
    public function index()
    {
        $schedules = TrainingSchedule::with(['course', 'trainer'])
            ->orderByRaw("FIELD(status, 'Open', 'Closed', 'Postponed', 'Completed', 'Cancelled')")
            ->orderBy('start_date', 'asc')
            ->get();

        return view('training_schedules.index', compact('schedules'));
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

        TrainingSchedule::create($request->only([
            'course_id',
            'trainer_id',
            'batch_code',
            'start_date',
            'end_date',
            'duration',
            'training_mode',
            'currency',
            'physical_fee',
            'online_fee',
            'venue',
            'zoom_link',
            'max_participants',
            'status',
        ]));

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

        $schedule->update($request->only([
            'course_id',
            'trainer_id',
            'batch_code',
            'start_date',
            'end_date',
            'duration',
            'training_mode',
            'currency',
            'physical_fee',
            'online_fee',
            'venue',
            'zoom_link',
            'max_participants',
            'status',
        ]));

        return redirect('/training-schedules')->with('success', 'Training Schedule Updated Successfully');
    }

    public function delete($id)
    {
        TrainingSchedule::findOrFail($id)->delete();

        return redirect('/training-schedules')->with('success', 'Training Schedule Deleted Successfully');
    }
}