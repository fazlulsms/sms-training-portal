<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationConfirmed;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use App\Models\User;
use App\Services\AutoInvoiceService;
use App\Services\TrainingNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicEnrollmentController extends Controller
{
    // ── Step 1 & 2: Show enrollment form for a specific schedule ──
    public function show(int $scheduleId)
    {
        $schedule = TrainingSchedule::with('course', 'trainer')
            ->where('is_public', true)
            ->findOrFail($scheduleId);

        abort_unless($schedule->is_open, 404, 'Registration for this schedule is closed.');

        // Pre-fill from logged-in participant
        $user = Auth::user();

        return view('public.enroll', compact('schedule', 'user'));
    }

    // ── Step 3-8: Process enrollment ─────────────────────────────
    public function store(Request $request, int $scheduleId)
    {
        $schedule = TrainingSchedule::with('course')
            ->where('is_public', true)
            ->findOrFail($scheduleId);

        abort_unless($schedule->is_open, 422, 'Registration is closed.');

        $validated = $request->validate([
            'full_name'               => 'required|string|max:200',
            'email'                   => 'required|email|max:200',
            'phone'                   => 'nullable|string|max:30',
            'gender'                  => 'nullable|string|max:30',
            'company'                 => 'nullable|string|max:200',
            'designation'             => 'nullable|string|max:100',
            'industry'                => 'nullable|string|max:150',
            'experience_years'        => 'nullable|string|max:30',
            'country'                 => 'nullable|string|max:80',
            'country_code'            => 'nullable|string|max:10',
            'city'                    => 'nullable|string|max:100',
            'mobile_number'           => 'nullable|string|max:30',
            'full_address'            => 'nullable|string|max:400',
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'special_requirements'    => 'nullable|string|max:1000',
            'referral_source'         => 'nullable|string|max:100',
            'pre_questions'           => 'nullable|string|max:1000',
            'selected_mode'           => 'required|in:Physical,Online',
            'payment_method'          => 'required|in:manual,sslcommerz',
            'participants'            => 'nullable|integer|min:1|max:100',
        ]);

        $participants = (int) ($validated['participants'] ?? 1);
        $fee = $validated['selected_mode'] === 'Physical'
            ? ($schedule->physical_fee ?? 0)
            : ($schedule->online_fee ?? 0);

        // Duplicate check
        $existing = Enrollment::where('email', $validated['email'])
            ->where('training_schedule_id', $schedule->id)
            ->first();

        if ($existing) {
            return back()->withInput()
                ->with('error', 'An enrollment for this email and schedule already exists (ID: ' . $existing->id . ').');
        }

        // Create / link participant account
        [$user, $plainPassword, $accountCreated] = $this->resolveOrCreateParticipant(
            $validated['full_name'], $validated['email'],
            $validated['phone'] ?? $validated['mobile_number'] ?? null,
            $validated['company'] ?? null,
            $validated['designation'] ?? null,
        );

        // Create enrollment
        $enrollment = Enrollment::create([
            'training_schedule_id'    => $schedule->id,
            'full_name'               => $validated['full_name'],
            'email'                   => $validated['email'],
            'phone'                   => $validated['phone'] ?? null,
            'gender'                  => $validated['gender'] ?? null,
            'company'                 => $validated['company'] ?? null,
            'designation'             => $validated['designation'] ?? null,
            'industry'                => $validated['industry'] ?? null,
            'experience_years'        => $validated['experience_years'] ?? null,
            'country'                 => $validated['country'] ?? 'Bangladesh',
            'country_code'            => $validated['country_code'] ?? null,
            'city'                    => $validated['city'] ?? null,
            'mobile_number'           => $validated['mobile_number'] ?? $validated['phone'] ?? null,
            'full_address'            => $validated['full_address'] ?? null,
            'emergency_contact_name'  => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'special_requirements'    => $validated['special_requirements'] ?? null,
            'referral_source'         => $validated['referral_source'] ?? null,
            'pre_questions'           => $validated['pre_questions'] ?? null,
            'selected_mode'           => $validated['selected_mode'],
            'applied_fee'             => $fee * $participants,
            'payment_method'          => $validated['payment_method'],
            'payment_status'          => 'Pending',
            'amount_received'         => 0,
            'attendance_status'       => 'Pending',
            'completion_status'       => 'Pending',
            'registration_status'     => 'Pending',
            'remarks'                 => 'Enrolled via public website',
        ]);

        // ── Auto-invoice ─────────────────────────────────────────────
        $invoice = null;
        try {
            $invoice = AutoInvoiceService::forIltEnrollment($enrollment, $schedule);
        } catch (\Throwable $e) {
            Log::error('AutoInvoice failed (ILT public)', [
                'enrollment_id' => $enrollment->id,
                'error'         => $e->getMessage(),
            ]);
        }

        // ── Participant confirmation email (with invoice PDF if available) ─
        try {
            $scheduleInfo = collect([
                $schedule->batch_code,
                $schedule->start_date?->format('d M Y'),
                $schedule->venue,
            ])->filter()->implode(' · ');

            if ($invoice) {
                Mail::to($validated['email'])
                    ->send(new RegistrationConfirmed(
                        invoice:      $invoice,
                        courseName:   $schedule->course?->name ?? 'Training Program',
                        scheduleInfo: $scheduleInfo ?: null,
                        tempPassword: $plainPassword,
                        type:         'ILT',
                    ));
            }
        } catch (\Throwable $e) {
            Log::error('RegistrationConfirmed email failed (ILT public)', [
                'enrollment_id' => $enrollment->id,
                'error'         => $e->getMessage(),
            ]);
        }

        // ── Admin notification ───────────────────────────────────────
        try {
            TrainingNotificationService::adminNewRegistration($enrollment, 'Enrollment');
        } catch (\Throwable $e) {
            Log::error('Admin new-registration notification failed (ILT public)', [
                'enrollment_id' => $enrollment->id,
                'error'         => $e->getMessage(),
            ]);
        }

        // If SSLCommerz selected — initiate payment
        if ($validated['payment_method'] === 'sslcommerz') {
            return redirect()->route('public.enroll.payment', $enrollment->id);
        }

        return redirect()->route('public.enroll.success', $enrollment->id);
    }

    // ── Success page ──────────────────────────────────────────
    public function success(int $enrollmentId)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($enrollmentId);
        return view('public.enroll-success', compact('enrollment'));
    }

    // ── Payment initiation (SSLCommerz scaffold) ──────────────
    public function payment(int $enrollmentId)
    {
        $enrollment = Enrollment::with('trainingSchedule.course')->findOrFail($enrollmentId);

        // SSLCommerz integration lives here in Phase 2
        // For now, redirect to manual payment page
        return redirect()->route('public.enroll.success', $enrollmentId)
            ->with('info', 'Online payment will be available soon. Your enrollment is recorded.');
    }

    // ── Helpers ───────────────────────────────────────────────
    private function resolveOrCreateParticipant(
        string $name, string $email,
        ?string $phone, ?string $company, ?string $designation
    ): array {
        $existing = User::where('email', $email)->first();
        if ($existing) {
            return [$existing, null, false];
        }

        $plain = 'SMS@' . date('Y') . '#' . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'        => $name,
            'email'       => $email,
            'password'    => Hash::make($plain),
            'role'        => 'participant',
            'phone'       => $phone,
            'company'     => $company,
            'designation' => $designation,
            'is_active'   => true,
        ]);

        return [$user, $plain, true];
    }
}
