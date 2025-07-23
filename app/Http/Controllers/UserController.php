<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Distributor;
use App\Services\InternationalDetectionService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('distributor')->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,distributor,factory',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        // Additional validation for distributor role
        if ($validated['role'] === 'distributor') {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'country' => 'required|string|max:255',
            ]);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo' => $validated['logo'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if ($validated['role'] === 'distributor' && !empty($validated['company_name'])) {
            $internationalService = new InternationalDetectionService();
            $isInternational = $validated['country'] ? $internationalService->isInternational($validated['country']) : false;
            
            Distributor::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'phone' => $validated['phone'] ?? '',
                'street' => $validated['street'] ?? '',
                'barangay' => $validated['barangay'] ?? '',
                'city' => $validated['city'] ?? '',
                'province' => $validated['province'] ?? '',
                'country' => $validated['country'] ?? '',
                'is_international' => $isInternational,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,distributor,factory',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        // Additional validation for distributor role
        if ($validated['role'] === 'distributor') {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'country' => 'required|string|max:255',
            ]);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo) {
                Storage::disk('public')->delete($user->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo' => $validated['logo'] ?? $user->logo,
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if ($validated['role'] === 'distributor') {
            $internationalService = new InternationalDetectionService();
            $isInternational = $validated['country'] ? $internationalService->isInternational($validated['country']) : false;
            
            if ($user->distributor) {
                $user->distributor->update([
                    'company_name' => $validated['company_name'] ?? '',
                    'phone' => $validated['phone'] ?? '',
                    'street' => $validated['street'] ?? '',
                    'barangay' => $validated['barangay'] ?? '',
                    'city' => $validated['city'] ?? '',
                    'province' => $validated['province'] ?? '',
                    'country' => $validated['country'] ?? '',
                    'is_international' => $isInternational,
                ]);
            } elseif (!empty($validated['company_name'])) {
                Distributor::create([
                    'user_id' => $user->id,
                    'company_name' => $validated['company_name'],
                    'phone' => $validated['phone'] ?? '',
                    'street' => $validated['street'] ?? '',
                    'barangay' => $validated['barangay'] ?? '',
                    'city' => $validated['city'] ?? '',
                    'province' => $validated['province'] ?? '',
                    'country' => $validated['country'] ?? '',
                    'is_international' => $isInternational,
                ]);
            }
        } else {
            if ($user->distributor) {
                $user->distributor->delete();
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account!');
        }

        // Delete logo if exists
        if ($user->logo) {
            Storage::disk('public')->delete($user->logo);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
