<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->input('q');
        $status = $request->input('status');
        $rating = $request->input('rating');

        $testimonials = Testimonial::with('course')
            ->when($q, fn($query) => $query->where(fn($sub) =>
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('company', 'like', "%$q%")
                    ->orWhere('course_name', 'like', "%$q%")
                    ->orWhereHas('course', fn($c) => $c->where('name', 'like', "%$q%"))
            ))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($rating, fn($query) => $query->where('rating', $rating))
            ->latest()
            ->paginate(20)
            ->withQueryString();

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
