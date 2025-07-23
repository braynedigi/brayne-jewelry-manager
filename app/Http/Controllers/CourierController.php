<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::latest()->get();
        return view('couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('couriers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Courier::create($validated);

        return redirect()->route('couriers.index')->with('success', 'Courier created successfully!');
    }

    public function show(Courier $courier)
    {
        return view('couriers.show', compact('courier'));
    }

    public function edit(Courier $courier)
    {
        return view('couriers.edit', compact('courier'));
    }

    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $courier->update($validated);

        return redirect()->route('couriers.index')->with('success', 'Courier updated successfully!');
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();

        return redirect()->route('couriers.index')->with('success', 'Courier deleted successfully!');
    }
}
