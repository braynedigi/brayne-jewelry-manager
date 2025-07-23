<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Distributor;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $customers = Customer::with('distributor.user')->get();
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor) {
                return redirect()->route('distributor.profile.create');
            }
            $customers = $distributor->customers;
        } else {
            abort(403, 'Access denied.');
        }
        
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $distributors = Distributor::with('user')->get();
            return view('customers.create', compact('distributors'));
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor) {
                return redirect()->route('distributor.profile.create');
            }
            return view('customers.create');
        } else {
            abort(403, 'Access denied.');
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'street' => 'nullable|string|max:255',
                'barangay' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'distributor_id' => 'required|exists:distributors,id',
            ]);
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor) {
                return redirect()->route('distributor.profile.create');
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'street' => 'nullable|string|max:255',
                'barangay' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
            ]);
            
            $validated['distributor_id'] = $distributor->id;
        } else {
            abort(403, 'Access denied.');
        }
        
        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all customers
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor || $customer->distributor_id !== $distributor->id) {
                abort(403, 'Access denied.');
            }
        } else {
            abort(403, 'Access denied.');
        }
        
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $distributors = Distributor::with('user')->get();
            return view('customers.edit', compact('customer', 'distributors'));
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor || $customer->distributor_id !== $distributor->id) {
                abort(403, 'Access denied.');
            }
            return view('customers.edit', compact('customer'));
        } else {
            abort(403, 'Access denied.');
        }
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'street' => 'nullable|string|max:255',
                'barangay' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'distributor_id' => 'required|exists:distributors,id',
            ]);
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor || $customer->distributor_id !== $distributor->id) {
                abort(403, 'Access denied.');
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'street' => 'nullable|string|max:255',
                'barangay' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
            ]);
        } else {
            abort(403, 'Access denied.');
        }
        
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can delete any customer
        } elseif ($user->isDistributor()) {
            $distributor = $user->distributor;
            if (!$distributor || $customer->distributor_id !== $distributor->id) {
                abort(403, 'Access denied.');
            }
        } else {
            abort(403, 'Access denied.');
        }
        
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
