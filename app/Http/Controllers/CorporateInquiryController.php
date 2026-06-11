<?php

namespace App\Http\Controllers;

use App\Models\CorporateInquiry;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CorporateInquiryController extends Controller
{
    // Admin: list all inquiries
    public function index(Request $request)
    {
        $status = $request->input('status');
        $inquiries = CorporateInquiry::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('corporate-inquiries.index', compact('inquiries'));
    }

    // Admin: view single inquiry
    public function show($id)
    {
        $inquiry = CorporateInquiry::findOrFail($id);
        return view('corporate-inquiries.show', compact('inquiry'));
    }

    // Admin: update status / notes
    public function update(Request $request, $id)
    {
        $inquiry = CorporateInquiry::findOrFail($id);
        $inquiry->update($request->only(['status', 'admin_notes']));
        return back()->with('success', 'Inquiry updated.');
    }

    // Public: show corporate training page
    public function publicPage()
    {
        $courses = Course::where('is_public', true)->orderBy('name')->get(['id', 'name', 'category']);
        return view('public.corporate', compact('courses'));
    }

    // Public: submit inquiry form
    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'company_name'         => 'required|string|max:255',
            'contact_person'       => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'phone'                => 'nullable|string|max:50',
            'country'              => 'nullable|string|max:100',
            'training_requirement' => 'required|string|min:10',
            'participants_count'   => 'nullable|integer|min:1|max:10000',
            'preferred_date'       => 'nullable|date|after:today',
            'preferred_mode'       => 'nullable|string|in:Physical,Online,Hybrid',
            'message'              => 'nullable|string|max:2000',
        ]);

        CorporateInquiry::create($validated);

        // Notify admin via email if configured
        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "New corporate training inquiry from {$validated['company_name']} ({$validated['email']})\n\n" .
                    "Contact: {$validated['contact_person']}\n" .
                    "Phone: " . ($validated['phone'] ?? '—') . "\n" .
                    "Participants: " . ($validated['participants_count'] ?? '—') . "\n" .
                    "Preferred Date: " . ($validated['preferred_date'] ?? '—') . "\n" .
                    "Mode: " . ($validated['preferred_mode'] ?? 'Physical') . "\n\n" .
                    "Requirement:\n{$validated['training_requirement']}\n\n" .
                    "Message:\n" . ($validated['message'] ?? '—'),
                    fn($m) => $m->to($adminEmail)->subject('New Corporate Training Inquiry – ' . $validated['company_name'])
                );
            } catch (\Throwable $e) {
                // Silently fail — inquiry already saved
            }
        }

        return redirect('/corporate-training')
            ->with('success', 'Thank you! Your inquiry has been submitted. Our team will contact you within 24 hours.');
    }
}
