<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Login page settings
        Setting::setValue(
            'login_logo',
            null,
            'file',
            'appearance',
            'Login Page Logo',
            'Upload a logo to display on the login page'
        );

        Setting::setValue(
            'login_background_color',
            '#f8fafc',
            'color',
            'appearance',
            'Login Background Color',
            'Choose the background color for the login page'
        );

        Setting::setValue(
            'login_background_image',
            null,
            'file',
            'appearance',
            'Login Background Image',
            'Upload a background image for the login page'
        );

        // Notification settings
        Setting::setValue(
            'email_notifications',
            true,
            'boolean',
            'notifications',
            'Email Notifications',
            'Enable email notifications for all users'
        );

        Setting::setValue(
            'in_app_notifications',
            true,
            'boolean',
            'notifications',
            'In-App Notifications',
            'Enable in-app notifications'
        );

        Setting::setValue(
            'order_notifications',
            true,
            'boolean',
            'notifications',
            'Order Notifications',
            'Send notifications for order-related actions'
        );

        Setting::setValue(
            'customer_notifications',
            true,
            'boolean',
            'notifications',
            'Customer Notifications',
            'Send notifications for customer-related actions'
        );

        Setting::setValue(
            'product_notifications',
            true,
            'boolean',
            'notifications',
            'Product Notifications',
            'Send notifications for product-related actions'
        );

        // General settings
        Setting::setValue(
            'company_name',
            'Jewelry Manager',
            'string',
            'general',
            'Company Name',
            'The name of your company'
        );

        Setting::setValue(
            'company_email',
            'admin@jewelrymanager.com',
            'string',
            'general',
            'Company Email',
            'Primary email address for the company'
        );

        Setting::setValue(
            'app_title',
            'Jewelry Manager',
            'string',
            'general',
            'Application Title',
            'The title displayed in browser tabs and throughout the application'
        );

        // Email settings
        Setting::setValue(
            'mail_mailer',
            'smtp',
            'string',
            'email',
            'Mail Driver',
            'The mail driver to use (smtp, sendmail, mailgun, ses, postmark, resend)'
        );

        Setting::setValue(
            'mail_host',
            'smtp.gmail.com',
            'string',
            'email',
            'SMTP Host',
            'The SMTP host address'
        );

        Setting::setValue(
            'mail_port',
            '587',
            'string',
            'email',
            'SMTP Port',
            'The SMTP port number'
        );

        Setting::setValue(
            'mail_username',
            '',
            'string',
            'email',
            'SMTP Username',
            'The SMTP username (usually your email address)'
        );

        Setting::setValue(
            'mail_password',
            '',
            'string',
            'email',
            'SMTP Password',
            'The SMTP password or app password'
        );

        Setting::setValue(
            'mail_encryption',
            'tls',
            'string',
            'email',
            'SMTP Encryption',
            'The encryption type (tls, ssl, or null)'
        );

        Setting::setValue(
            'mail_from_address',
            'noreply@jewelrymanager.com',
            'string',
            'email',
            'From Email Address',
            'The email address that emails will be sent from'
        );

        Setting::setValue(
            'mail_from_name',
            'Jewelry Manager',
            'string',
            'email',
            'From Name',
            'The name that emails will be sent from'
        );
    }
}
