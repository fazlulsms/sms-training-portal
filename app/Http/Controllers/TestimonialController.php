<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::with('course');
        if ($request->filled('status')) $query->where('status', $request->status);
        $testimonials = $query->latest()->paginate(20);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function approve(Testimonial $testimonial)
    {
        $testimonial->update(['status' => 'approved']);
        return back()->with('success', 'Testimonial approved and is now visible publicly.');
    }

    public function reject(Testimonial $testimonial)
    {
        $testimonial->update(['status' => 'rejected']);
        return back()->with('success', 'Testimonial rejected.');
    }

    public function feature(Testimonial $testimonial)
    {
        $testimonial->update(['status' => 'featured']);
        return back()->with('success', 'Testimonial marked as featured.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo) {
            \Storage::disk('public')->delete($testimonial->photo);
        }
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted.');
    }
}
