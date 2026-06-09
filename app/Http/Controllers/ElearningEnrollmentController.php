<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentWelcome;
use App\Mail\RegistrationConfirmed;
use App\Models\Course;
use App\Services\AutoInvoiceService;
use App\Services\PaymentConfirmationService;
use App\Services\TrainingNotificationService;
use App\Models\ElearningEnrollment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ElearningEnrollmentController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = ElearningEnrollment::with('course');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('participant_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $courses = Course::orderBy('name')->get();

        $enrollments = $query->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('elearning.enrollments.index', compact('enrollments', 'courses'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        $courses = Course::orderBy('name')->get();

        return view('elearning.enrollments.create', compact('courses'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'course_id'        => 'required|exists:courses,id',
            'participant_name' => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'company'          => 'nullable|string|max:255',
            'designation'      => 'nullable|string|max:255',
            // Payment fields NOT accepted at registration — handled via Invoice Pay
        ]);

        $course = Course::findOrFail($request->course_id);

        // ── Account creation / linking ──────────────────────────────────────
        [$user, $plainPassword, $accountCreated] = $this->resolveOrCreateLearner(
            $request->participant_name,
            $request->email,
            $request->phone,
            $request->company,
            $request->designation,
        );

        // ── Create enrollment (always pending / locked until payment confirmed) ──
        $enrollment = ElearningEnrollment::create([
            'user_id'            => $user->id,
            'course_id'          => $request->course_id,
            'participant_name'   => $request->participant_name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'company'            => $request->company,
            'designation'        => $request->designation,
            'amount'             => $course->course_fee ?? 0,
            'currency'           => 'BDT',
            'payment_method'     => 'pending',
            'payment_status'     => 'pending',
            'access_status'      => 'locked',   // unlocked only after payment
            'started_at'         => null,
            'expires_at'         => null,
            'completion_status'  => 'not_started',
            'certificate_status' => 'not_issued',
        ]);

        // ── SEQUENCE STEP 1: Registration email (invoice attached, NO credentials) ──
        try {
            $invoice = AutoInvoiceService::forElearningEnrollment($enrollment);
            // Pass null for tempPassword — credentials sent only after payment
            TrainingNotificationService::elearningRegistrationCompleted($enrollment, $invoice, null);
        } catch (\Throwable $e) {
            Log::error('AutoInvoice/RegistrationConfirmed failed (eLearning admin)', [
                'enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
            ]);
        }
        TrainingNotificationService::adminNewRegistration($enrollment, 'ElearningEnrollment');

        // NOTE: Welcome email with credentials is sent AFTER payment confirmation
        // (via approvePayment() or Invoice Pay → syncLinkedEnrollment)
        // Store plainPassword temporarily in session so approvePayment can use it if needed
        if ($plainPassword) {
            session(['elearning_temp_pw_' . $enrollment->id => $plainPassword]);
        }

        $msg = $accountCreated
            ? '✅ Enrollment created. Account created. Registration confirmation sent to ' . $request->email . '. Welcome email with login credentials will be sent after payment is confirmed.'
            : '✅ Enrollment created. Registration confirmation sent to ' . $request->email . '. Course access will be activated after payment.';

        return redirect()
            ->route('elearning.enrollments.show', $enrollment)
            ->with('success', $msg);
    }

    // ── Approve Payment ───────────────────────────────────────────────────────

    public function approvePayment(ElearningEnrollment $enrollment): RedirectResponse
    {
        $course = $enrollment->course;

        $enrollment->update([
            'payment_status' => 'manual_approved',
            'access_status'  => 'unlocked',
            'started_at'     => $enrollment->started_at ?? now(),
            'expires_at'     => $course->access_days ? now()->addDays($course->access_days) : null,
        ]);

        $fresh = $enrollment->fresh()->load('course', 'user');

        // ── SEQUENCE STEP 2a: Payment confirmation email (PDF receipt) ───────
        try {
            PaymentConfirmationService::handleElearningEnrollment($fresh);
        } catch (\Throwable $e) {
            Log::error('PaymentConfirmation failed (eLearning approvePayment)', [
                'elearning_enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
            ]);
        }

        // ── SEQUENCE STEP 2b: Welcome email with login credentials ───────────
        // Generate a fresh temp password so the learner can log in now
        $plainPassword = $this->generateTempPassword();
        if ($fresh->user) {
            $fresh->user->update(['password' => Hash::make($plainPassword)]);
        }
        // Course access activated email includes credentials
        TrainingNotificationService::courseAccessActivated($fresh, $plainPassword);

        return back()->with('success', 'Payment approved, course access unlocked, and welcome email sent.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(ElearningEnrollment $enrollment)
    {
        $enrollment->load(['course', 'user']);

        return view('elearning.enrollments.show', compact('enrollment'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(ElearningEnrollment $enrollment)
    {
        $courses = Course::where('course_type', 'elearning')
            ->orderBy('name')
            ->get();

        return view('elearning.enrollments.edit', compact('enrollment', 'courses'));
    }

    public function update(Request $request, ElearningEnrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'course_id'          => 'required|exists:courses,id',
            'participant_name'   => 'required|string|max:255',
            'email'              => 'required|email|max:255',
            'phone'              => 'nullable|string|max:50',
            'company'            => 'nullable|string|max:255',
            'designation'        => 'nullable|string|max:255',
            'amount'             => 'nullable|numeric',
            'currency'           => 'required|string|max:10',
            'payment_method'     => 'required|string|max:50',
            'payment_status'     => 'required|string|max:50',
            'access_status'      => 'required|string|max:50',
            'completion_status'  => 'required|string|max:50',
            'certificate_status' => 'required|string|max:50',
        ]);

        // Capture old statuses before update
        $wasAlreadyPaid    = PaymentConfirmationService::isPaidStatus($enrollment->payment_status);
        $oldAccessStatus   = $enrollment->access_status;
        $oldCompletion     = $enrollment->completion_status;
        $oldCertStatus     = $enrollment->certificate_status;

        $enrollment->update($request->only([
            'course_id', 'participant_name', 'email', 'phone',
            'company', 'designation', 'amount', 'currency',
            'payment_method', 'payment_status', 'access_status',
            'completion_status', 'certificate_status',
        ]));

        $fresh   = $enrollment->fresh();
        $nowPaid = PaymentConfirmationService::isPaidStatus($request->payment_status);

        // Payment confirmed
        if ($nowPaid && !$wasAlreadyPaid) {
            try {
                PaymentConfirmationService::handleElearningEnrollment($fresh);
            } catch (\Throwable $e) {
                Log::error('PaymentConfirmation failed (eLearning update)', [
                    'elearning_enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
                ]);
            }
        }

        // Course access just unlocked
        if ($request->access_status === 'unlocked' && $oldAccessStatus !== 'unlocked') {
            TrainingNotificationService::courseAccessActivated($fresh);
        }

        // Course completed
        if ($request->completion_status === 'completed' && $oldCompletion !== 'completed') {
            TrainingNotificationService::courseCompleted($fresh, 'ElearningEnrollment');
        }

        // Certificate issued
        if (in_array($request->certificate_status, ['issued', 'ready']) && !in_array($oldCertStatus, ['issued', 'ready'])) {
            TrainingNotificationService::certificateIssued($fresh, 'ElearningEnrollment');
        }

        return redirect()
            ->route('elearning.enrollments.show', $enrollment)
            ->with('success', 'Enrollment updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(ElearningEnrollment $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return redirect()
            ->route('elearning.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }

    // ── Resend Welcome Email ──────────────────────────────────────────────────

    public function sendWelcomeEmail(ElearningEnrollment $enrollment): RedirectResponse
    {
        $user = $enrollment->user;

        if (!$user) {
            return back()->with('error', 'No learner account is linked to this enrollment.');
        }

        // Generate a fresh temp password and update the account
        $plainPassword = $this->generateTempPassword();
        $user->update(['password' => Hash::make($plainPassword)]);

        $this->dispatchWelcomeEmail($enrollment, $user, $plainPassword);

        return back()->with('success', '✅ Welcome email resent to ' . $enrollment->email . '. Password has been reset.');
    }

    // ── Reset Learner Password ────────────────────────────────────────────────

    public function resetLearnerPassword(ElearningEnrollment $enrollment): RedirectResponse
    {
        $user = $enrollment->user;

        if (!$user) {
            return back()->with('error', 'No learner account is linked to this enrollment.');
        }

        $plainPassword = $this->generateTempPassword();
        $user->update(['password' => Hash::make($plainPassword)]);

        // Send the reset email with the new credentials
        $this->dispatchWelcomeEmail($enrollment, $user, $plainPassword, isReset: true);

        return back()->with('success', '✅ New password generated and sent to ' . $enrollment->email);
    }

    // ── Login As Learner (Admin Impersonation) ────────────────────────────────

    public function loginAsLearner(ElearningEnrollment $enrollment): RedirectResponse
    {
        $user = $enrollment->user;

        if (!$user) {
            return back()->with('error', 'No learner account is linked to this enrollment.');
        }

        // Store admin's ID so we can restore later
        session(['impersonating_admin_id' => Auth::id()]);

        Auth::loginUsingId($user->id);

        return redirect()
            ->route('participant.my-courses')
            ->with('info', 'You are now viewing the portal as ' . $user->name . '. Use "Return to Admin" to go back.');
    }

    // ── Stop Impersonating ────────────────────────────────────────────────────

    public function stopImpersonating(Request $request): RedirectResponse
    {
        $adminId = session('impersonating_admin_id');

        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $request->session()->forget('impersonating_admin_id');

        Auth::loginUsingId($adminId);

        return redirect()
            ->route('elearning.enrollments.index')
            ->with('success', 'Returned to admin panel.');
    }

    // ── Issue Certificate ─────────────────────────────────────────────────────

    public function issueCertificate(ElearningEnrollment $enrollment): RedirectResponse
    {
        if ($enrollment->certificate_status !== 'eligible') {
            return back()->with('error', 'Enrollment is not eligible for a certificate yet.');
        }

        $enrollment->update([
            'certificate_status'     => 'issued',
            'certificate_number'     => $enrollment->certificate_number
                ?? 'EL-' . date('Y') . '-' . str_pad($enrollment->id, 5, '0', STR_PAD_LEFT),
            'certificate_issue_date' => now()->toDateString(),
        ]);

        TrainingNotificationService::certificateIssued($enrollment->fresh(), 'ElearningEnrollment');

        return back()->with('success', 'Certificate issued successfully.');
    }

    // ── Public Registration ───────────────────────────────────────────────────

    public function publicRegister(Course $course)
    {
        abort_unless($course->course_type === 'elearning' && $course->status == 1, 404);

        if (Setting::get('elearning.allow_self_registration', '1') === '0') {
            return view('elearning.registration-closed', compact('course'));
        }

        return view('elearning.public-register', compact('course'));
    }

    public function publicRegisterStore(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->course_type === 'elearning' && $course->status == 1, 404);

        if (Setting::get('elearning.allow_self_registration', '1') === '0') {
            return redirect()
                ->route('elearning.public.register', $course->id)
                ->with('error', 'Online self-registration is currently closed. Please contact SMS Training Team for enrollment support.');
        }

        $request->validate([
            'participant_name' => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'company'          => 'nullable|string|max:255',
            'designation'      => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:100',
        ]);

        // Prevent duplicate enrollment
        $exists = ElearningEnrollment::where('course_id', $course->id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'You are already registered for this course. Please contact us if you need assistance.');
        }

        [$user, $plainPassword, $accountCreated] = $this->resolveOrCreateLearner(
            $request->participant_name,
            $request->email,
            $request->phone,
            $request->company,
            $request->designation,
        );

        $enrollment = ElearningEnrollment::create([
            'user_id'            => $user?->id,
            'course_id'          => $course->id,
            'participant_name'   => $request->participant_name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'company'            => $request->company,
            'designation'        => $request->designation,
            'country'            => $request->country,
            'amount'             => $course->course_fee ?? 0,
            'currency'           => 'BDT',
            'payment_method'     => 'pending',
            'payment_status'     => 'pending',
            'access_status'      => 'locked',
            'completion_status'  => 'not_started',
            'certificate_status' => 'not_issued',
        ]);

        // ── SEQUENCE STEP 1: Registration email (no credentials) ────────────
        try {
            $invoice = AutoInvoiceService::forElearningEnrollment($enrollment);
            TrainingNotificationService::elearningRegistrationCompleted($enrollment, $invoice, null);
        } catch (\Throwable $e) {
            Log::error('AutoInvoice/RegistrationConfirmed failed (eLearning public)', [
                'enrollment_id' => $enrollment->id, 'error' => $e->getMessage(),
            ]);
        }
        TrainingNotificationService::adminNewRegistration($enrollment, 'ElearningEnrollment');
        // Welcome email with credentials sent AFTER payment confirmation (Step 2)

        $message = $accountCreated
            ? 'Your registration has been received. A learner account has been created and login credentials have been sent to your email address. You can log in once payment is confirmed.'
            : 'Your registration has been received. Login credentials have been sent to your email. You can log in once payment is confirmed.';

        return redirect()
            ->route('elearning.public.register', $course->id)
            ->with('success', $message);
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Find existing user or create a new participant account.
     * Returns [$user, $plainPassword, $accountCreated].
     */
    private function resolveOrCreateLearner(
        string $name,
        string $email,
        ?string $phone = null,
        ?string $company = null,
        ?string $designation = null,
    ): array {
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Link existing account — do not change their password
            return [$existingUser, null, false];
        }

        // Create new participant account with temporary password
        $plainPassword = $this->generateTempPassword();

        $user = User::create([
            'name'        => $name,
            'email'       => $email,
            'password'    => Hash::make($plainPassword),
            'role'        => 'participant',
            'is_active'   => true,
            'phone'       => $phone,
            'company'     => $company,
            'designation' => $designation,
        ]);

        return [$user, $plainPassword, true];
    }

    /**
     * Generate a memorable temporary password: SMS@YYYY#NNNN
     */
    private function generateTempPassword(): string
    {
        return 'SMS@' . date('Y') . '#' . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Send enrollment welcome email. Silently logs on failure.
     */
    private function dispatchWelcomeEmail(
        ElearningEnrollment $enrollment,
        ?User $user,
        ?string $plainPassword,
        bool $isReset = false,
    ): void {
        if (!$user || !$plainPassword) {
            return; // existing user with unchanged password — don't send
        }

        try {
            $enrollment->loadMissing('course');
            Mail::to($enrollment->email)
                ->send(new EnrollmentWelcome($enrollment, $plainPassword));
        } catch (\Throwable $e) {
            Log::error('EnrollmentWelcome email failed', [
                'enrollment_id' => $enrollment->id,
                'email'         => $enrollment->email,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
