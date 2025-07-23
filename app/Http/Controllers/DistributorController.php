<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Distributor;
use App\Services\InternationalDetectionService;

class DistributorController extends Controller
{
    public function createProfile()
    {
        $user = Auth::user();
        
        if ($user->distributor) {
            return redirect()->route('distributor.profile.edit');
        }

        return view('distributor.create-profile');
    }

    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $internationalService = new InternationalDetectionService();
        $isInternational = $internationalService->isInternational($validated['country']);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Update user name and logo
        $user = Auth::user();
        $user->update([
            'name' => $validated['name'],
            'logo' => $logoPath,
        ]);

        // Create distributor profile
        Distributor::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'street' => $validated['street'],
            'barangay' => $validated['barangay'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'country' => $validated['country'],
            'is_international' => $isInternational,
        ]);

        return redirect()->route('distributor.dashboard')->with('success', 'Profile created successfully!');
    }

    public function editProfile()
    {
        $user = Auth::user();
        
        if (!$user->distributor) {
            return redirect()->route('distributor.profile.create');
        }

        $distributor = $user->distributor;
        return view('distributor.edit-profile', compact('distributor'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->distributor) {
            return redirect()->route('distributor.profile.create');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $internationalService = new InternationalDetectionService();
        $isInternational = $internationalService->isInternational($validated['country']);

        // Handle logo upload
        $logoPath = $user->logo; // Keep existing logo by default
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo && Storage::disk('public')->exists($user->logo)) {
                Storage::disk('public')->delete($user->logo);
            }
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Update user name and logo
        $user->update([
            'name' => $validated['name'],
            'logo' => $logoPath,
        ]);

        // Update distributor profile
        $user->distributor->update([
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'street' => $validated['street'],
            'barangay' => $validated['barangay'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'country' => $validated['country'],
            'is_international' => $isInternational,
        ]);

        return redirect()->route('distributor.dashboard')->with('success', 'Profile updated successfully!');
    }
} 