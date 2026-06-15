<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\LtfDeliveryMethod;
use Illuminate\Http\Request;

class LtfDeliveryMethodController extends Controller
{
    public function index()
    {
        $records = LtfDeliveryMethod::withCount('courses')
            ->orderBy('display_order')->orderBy('name')->get();
        return view('admin.setup.delivery-methods.index', compact('records'));
    }

    public function create()
    {
        return view('admin.setup.delivery-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LtfDeliveryMethod::create($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.delivery-methods.index')->with('success', 'Delivery method created.');
    }

    public function edit(LtfDeliveryMethod $deliveryMethod)
    {
        return view('admin.setup.delivery-methods.edit', ['record' => $deliveryMethod]);
    }

    public function update(Request $request, LtfDeliveryMethod $deliveryMethod)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $deliveryMethod->update($request->only(['name', 'description', 'display_order', 'status']));
        return redirect()->route('setup.delivery-methods.index')->with('success', 'Delivery method updated.');
    }

    public function toggle(LtfDeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->status = $deliveryMethod->status === 'active' ? 'archived' : 'active';
        $deliveryMethod->save();
        return back()->with('success', 'Status updated.');
    }

    public function destroy(LtfDeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->delete();
        return redirect()->route('setup.delivery-methods.index')->with('success', 'Delivery method deleted.');
    }
}
