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
            // Handle file uploads
            if ($request->hasFile("settings.{$key}")) {
                $file = $request->file("settings.{$key}");
                $path = $file->store('settings', 'public');
                $value = $path;
            }
            
            Setting::setValue($key, $value);
            $updatedSettings[] = $key;
        }

        // Generate dynamic CSS after updating settings
        $this->generateDynamicCSS();

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Generate dynamic CSS file based on current settings
     */
    private function generateDynamicCSS()
    {
        $cssContent = $this->buildCSSContent();
        $cssPath = public_path('css/custom-theme.css');
        
        file_put_contents($cssPath, $cssContent);
    }

    /**
     * Build CSS content with current settings
     */
    private function buildCSSContent()
    {
        $css = "/* Custom Theme CSS - Generated dynamically from admin settings */\n\n";
        $css .= ":root {\n";
        
        // Button Colors
        $css .= "    --primary-button-color: " . Setting::getValue('primary_button_color', '#0d6efd') . ";\n";
        $css .= "    --secondary-button-color: " . Setting::getValue('secondary_button_color', '#6c757d') . ";\n";
        $css .= "    --success-button-color: " . Setting::getValue('success_button_color', '#198754') . ";\n";
        $css .= "    --warning-button-color: " . Setting::getValue('warning_button_color', '#ffc107') . ";\n";
        $css .= "    --danger-button-color: " . Setting::getValue('danger_button_color', '#dc3545') . ";\n";
        $css .= "    --info-button-color: " . Setting::getValue('info_button_color', '#0dcaf0') . ";\n";
        
        // Sidebar & Navigation Colors
        $css .= "    --sidebar-background-color: " . Setting::getValue('sidebar_background_color', '#343a40') . ";\n";
        $css .= "    --sidebar-text-color: " . Setting::getValue('sidebar_text_color', '#ffffff') . ";\n";
        $css .= "    --sidebar-active-color: " . Setting::getValue('sidebar_active_color', '#007bff') . ";\n";
        $css .= "    --top-navbar-color: " . Setting::getValue('top_navbar_color', '#ffffff') . ";\n";
        $css .= "    --top-navbar-text-color: " . Setting::getValue('top_navbar_text_color', '#212529') . ";\n";
        
        // Card & Panel Colors
        $css .= "    --card-background-color: " . Setting::getValue('card_background_color', '#ffffff') . ";\n";
        $css .= "    --card-header-color: " . Setting::getValue('card_header_color', '#f8f9fa') . ";\n";
        $css .= "    --right-panel-color: " . Setting::getValue('right_panel_color', '#f8f9fa') . ";\n";
        
        // Status Badge Colors
        $css .= "    --status-pending-color: " . Setting::getValue('status_pending_color', '#ffc107') . ";\n";
        $css .= "    --status-approved-color: " . Setting::getValue('status_approved_color', '#0dcaf0') . ";\n";
        $css .= "    --status-production-color: " . Setting::getValue('status_production_color', '#0d6efd') . ";\n";
        $css .= "    --status-completed-color: " . Setting::getValue('status_completed_color', '#198754') . ";\n";
        
        // Additional UI Colors
        $css .= "    --link-color: " . Setting::getValue('link_color', '#0d6efd') . ";\n";
        $css .= "    --border-color: " . Setting::getValue('border_color', '#dee2e6') . ";\n";
        $css .= "    --shadow-color: " . Setting::getValue('shadow_color', '#000000') . ";\n";
        
        $css .= "}\n\n";
        
        // Add the rest of the CSS rules from the template file
        $templateCss = file_get_contents(public_path('css/custom-theme-template.css'));
        
        $css .= $templateCss;
        
        return $css;
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

    public function regenerateCSS()
    {
        try {
            $this->generateDynamicCSS();
            return response()->json(['success' => true, 'message' => 'CSS regenerated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to regenerate CSS: ' . $e->getMessage()]);
        }
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
