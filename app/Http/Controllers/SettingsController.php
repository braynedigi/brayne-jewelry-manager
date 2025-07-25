<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $appearanceSettings = Setting::getByGroup('appearance');
        $notificationSettings = Setting::getByGroup('notifications');
        $generalSettings = Setting::getByGroup('general');
        $emailSettings = Setting::getByGroup('email');

        return view('admin.settings.index', compact('appearanceSettings', 'notificationSettings', 'generalSettings', 'emailSettings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable'
        ]);

        $updatedSettings = [];

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Handle file uploads
                if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    $path = $file->store('settings', 'public');
                    
                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    $value = $path;
                }

                // Handle boolean values
                if ($setting->type === 'boolean') {
                    $value = $value ? true : false;
                }

                // Update the setting
                $setting->update(['value' => $value]);
                $updatedSettings[] = $key;
                
                // Log the update for debugging
                \Log::info("Setting updated: {$key} = {$value}");
            } else {
                // If setting doesn't exist, create it
                Setting::setValue($key, $value, 'string', 'general');
                $updatedSettings[] = $key;
                \Log::info("Setting created: {$key} = {$value}");
            }
        }

        // Clear all settings cache
        Setting::clearCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Send test email
        try {
            // Update mail configuration from settings
            $this->updateMailConfig();
            
            // Send test email
            \Mail::to($request->email)->send(new \App\Mail\GeneralNotification(
                'Test Email - Jewelry Manager',
                'This is a test email to verify your email configuration is working correctly.',
                'info'
            ));
            
            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    /**
     * Update mail configuration from settings
     */
    private function updateMailConfig()
    {
        $mailConfig = [
            'driver' => Setting::getValue('mail_mailer', 'smtp'),
            'host' => Setting::getValue('mail_host', 'smtp.gmail.com'),
            'port' => Setting::getValue('mail_port', '587'),
            'username' => Setting::getValue('mail_username', ''),
            'password' => Setting::getValue('mail_password', ''),
            'encryption' => Setting::getValue('mail_encryption', 'tls'),
            'from' => [
                'address' => Setting::getValue('mail_from_address', 'noreply@jewelrymanager.com'),
                'name' => Setting::getValue('mail_from_name', 'Jewelry Manager'),
            ],
        ];

        config(['mail.mailers.smtp' => $mailConfig]);
        config(['mail.from' => $mailConfig['from']]);
    }

    public function refresh()
    {
        // Clear all settings cache
        Setting::clearCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings cache cleared and refreshed!');
    }

    // Admin courier management methods
    public function storeCourier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        \App\Models\Courier::create($validated);

        return response()->json(['success' => true, 'message' => 'Courier created successfully!']);
    }

    public function updateCourier(Request $request, \App\Models\Courier $courier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $courier->update($validated);

        return response()->json(['success' => true, 'message' => 'Courier updated successfully!']);
    }

    public function destroyCourier(\App\Models\Courier $courier)
    {
        // Check if courier has associated orders
        if ($courier->orders()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete courier with associated orders. Please reassign orders first.'
            ]);
        }

        $courier->delete();

        return response()->json(['success' => true, 'message' => 'Courier deleted successfully!']);
    }
}
