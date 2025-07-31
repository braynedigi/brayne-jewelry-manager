<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class ColorSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colorSettings = [
            // Button Colors
            ['key' => 'primary_button_color', 'value' => '#0d6efd', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary Button Color', 'description' => 'Main action buttons (Create, Save, etc.)'],
            ['key' => 'secondary_button_color', 'value' => '#6c757d', 'type' => 'color', 'group' => 'appearance', 'label' => 'Secondary Button Color', 'description' => 'Secondary action buttons (Cancel, Back, etc.)'],
            ['key' => 'success_button_color', 'value' => '#198754', 'type' => 'color', 'group' => 'appearance', 'label' => 'Success Button Color', 'description' => 'Success action buttons (Approve, Complete, etc.)'],
            ['key' => 'warning_button_color', 'value' => '#ffc107', 'type' => 'color', 'group' => 'appearance', 'label' => 'Warning Button Color', 'description' => 'Warning action buttons (Edit, Update, etc.)'],
            ['key' => 'danger_button_color', 'value' => '#dc3545', 'type' => 'color', 'group' => 'appearance', 'label' => 'Danger Button Color', 'description' => 'Danger action buttons (Delete, Cancel, etc.)'],
            ['key' => 'info_button_color', 'value' => '#0dcaf0', 'type' => 'color', 'group' => 'appearance', 'label' => 'Info Button Color', 'description' => 'Info action buttons (View, Details, etc.)'],
            
            // Sidebar & Navigation Colors
            ['key' => 'sidebar_background_color', 'value' => '#343a40', 'type' => 'color', 'group' => 'appearance', 'label' => 'Sidebar Background', 'description' => 'Background color for the left sidebar'],
            ['key' => 'sidebar_text_color', 'value' => '#ffffff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Sidebar Text Color', 'description' => 'Text color for sidebar menu items'],
            ['key' => 'sidebar_active_color', 'value' => '#007bff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Active Menu Color', 'description' => 'Color for active/selected menu items'],
            ['key' => 'top_navbar_color', 'value' => '#ffffff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Top Navbar Color', 'description' => 'Background color for the top navigation bar'],
            ['key' => 'top_navbar_text_color', 'value' => '#212529', 'type' => 'color', 'group' => 'appearance', 'label' => 'Top Navbar Text', 'description' => 'Text color for the top navigation bar'],
            
            // Card & Panel Colors
            ['key' => 'card_background_color', 'value' => '#ffffff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Card Background', 'description' => 'Background color for content cards'],
            ['key' => 'card_header_color', 'value' => '#f8f9fa', 'type' => 'color', 'group' => 'appearance', 'label' => 'Card Header Color', 'description' => 'Background color for card headers'],
            ['key' => 'right_panel_color', 'value' => '#f8f9fa', 'type' => 'color', 'group' => 'appearance', 'label' => 'Right Panel Color', 'description' => 'Background color for right sidebar panels'],
            
            // Status Badge Colors
            ['key' => 'status_pending_color', 'value' => '#ffc107', 'type' => 'color', 'group' => 'appearance', 'label' => 'Pending Status', 'description' => 'Color for pending status badges'],
            ['key' => 'status_approved_color', 'value' => '#0dcaf0', 'type' => 'color', 'group' => 'appearance', 'label' => 'Approved Status', 'description' => 'Color for approved status badges'],
            ['key' => 'status_production_color', 'value' => '#0d6efd', 'type' => 'color', 'group' => 'appearance', 'label' => 'Production Status', 'description' => 'Color for production status badges'],
            ['key' => 'status_completed_color', 'value' => '#198754', 'type' => 'color', 'group' => 'appearance', 'label' => 'Completed Status', 'description' => 'Color for completed status badges'],
            
            // Additional UI Colors
            ['key' => 'link_color', 'value' => '#0d6efd', 'type' => 'color', 'group' => 'appearance', 'label' => 'Link Color', 'description' => 'Color for hyperlinks throughout the application'],
            ['key' => 'border_color', 'value' => '#dee2e6', 'type' => 'color', 'group' => 'appearance', 'label' => 'Border Color', 'description' => 'Color for borders and dividers'],
            ['key' => 'shadow_color', 'value' => '#000000', 'type' => 'color', 'group' => 'appearance', 'label' => 'Shadow Color', 'description' => 'Color for shadows and depth effects'],
        ];

        foreach ($colorSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 