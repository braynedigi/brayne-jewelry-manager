# 📧 Email Notifications Setup Guide

This guide will help you configure email notifications for your Brayne Jewelry Manager system.

## 🎯 Overview

Your jewelry management system now supports email notifications for:
- **Order Status Changes** - When orders move through the production workflow
- **New Order Creation** - When new orders are created
- **General Notifications** - For other system events

## ⚙️ Configuration Steps

### Step 1: Access Email Settings

1. **Login as Admin** to your system
2. **Navigate to Settings** → Admin Settings
3. **Click the "Email" tab** in the settings page

### Step 2: Configure Email Provider

#### Option A: Gmail (Recommended for Testing)

**Settings:**
- **Mail Driver:** SMTP
- **SMTP Host:** `smtp.gmail.com`
- **SMTP Port:** `587`
- **SMTP Username:** Your Gmail address
- **SMTP Password:** Your Gmail password or App Password
- **SMTP Encryption:** TLS
- **From Email Address:** Your Gmail address
- **From Name:** Jewelry Manager

**Gmail Setup:**
1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password:**
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a password for "Mail"
   - Use this password in the SMTP Password field

#### Option B: Other SMTP Providers

**Outlook/Hotmail:**
- **SMTP Host:** `smtp-mail.outlook.com`
- **SMTP Port:** `587`
- **Encryption:** TLS

**Yahoo:**
- **SMTP Host:** `smtp.mail.yahoo.com`
- **SMTP Port:** `587`
- **Encryption:** TLS

**Custom SMTP Server:**
- Use your hosting provider's SMTP settings
- Contact your hosting provider for details

### Step 3: Test Email Configuration

1. **Click "Send Test Email"** button
2. **Enter your email address** when prompted
3. **Check your inbox** for the test email
4. **Verify the email** was received successfully

### Step 4: Enable Notifications

1. **Go to the "Notifications" tab** in settings
2. **Enable "Email Notifications"** toggle
3. **Enable specific notification types:**
   - Order Notifications
   - Customer Notifications
   - Product Notifications

## 📋 Email Templates

The system includes professional email templates for:

### Order Status Notifications
- **Template:** `resources/views/emails/order-status.blade.php`
- **Triggers:** When order status changes (In Production → Finishing → Ready)
- **Content:** Order details, status change, production notes, delivery estimates

### New Order Notifications
- **Template:** `resources/views/emails/order-created.blade.php`
- **Triggers:** When new orders are created
- **Content:** Order information, customer details, products, priority

### General Notifications
- **Template:** `resources/views/emails/general.blade.php`
- **Triggers:** For other system events
- **Content:** Customizable title, message, and additional data

## 🔧 Technical Details

### Email Classes
- `app/Mail/OrderStatusNotification.php`
- `app/Mail/OrderCreatedNotification.php`
- `app/Mail/GeneralNotification.php`

### Notification Service
- `app/Services/NotificationService.php` - Handles email sending logic
- `app/Models/Notification.php` - Database model for notifications

### Settings Storage
- Email configuration is stored in the `settings` table
- Settings are cached for performance
- Changes take effect immediately

## 🚨 Troubleshooting

### Common Issues

**1. "Failed to send test email"**
- Check SMTP credentials
- Verify port and encryption settings
- Ensure firewall allows SMTP traffic

**2. Gmail "Less secure app access" error**
- Enable 2-Factor Authentication
- Use App Password instead of regular password

**3. Emails going to spam**
- Configure SPF/DKIM records for your domain
- Use a reputable SMTP provider
- Avoid spam trigger words in subject lines

**4. No emails being sent**
- Check if email notifications are enabled in settings
- Verify user email addresses are correct
- Check application logs for errors

### Debug Mode

To debug email issues:
1. **Set Mail Driver to "log"** in email settings
2. **Check Laravel logs** in `storage/logs/laravel.log`
3. **Look for email-related error messages**

### Log Location
- **Email logs:** `storage/logs/laravel.log`
- **Mail driver logs:** `storage/logs/laravel-{date}.log`

## 📱 Email Notifications vs In-App Notifications

### Email Notifications
- **Sent to user's email address**
- **Include detailed information**
- **Professional templates**
- **Can be accessed offline**

### In-App Notifications
- **Displayed within the application**
- **Real-time updates**
- **Quick access to related actions**
- **Require user to be logged in**

## 🔄 Notification Flow

1. **Event occurs** (order status change, new order, etc.)
2. **NotificationService** creates notification record
3. **Email notification** is sent if enabled
4. **In-app notification** is displayed
5. **User receives** both email and in-app notification

## 📊 Notification Types

| Type | Description | Recipients |
|------|-------------|------------|
| `order_status_updated` | Order status changes | Distributor, Admin |
| `order_created` | New order created | Admin, Distributor |
| `customer_created` | New customer added | Admin, Distributor |
| `product_created` | New product added | Admin, All Distributors |
| `order_approval` | Order approved/rejected | Distributor |

## 🎨 Customization

### Email Templates
- **Location:** `resources/views/emails/`
- **Base layout:** `layout.blade.php`
- **Customize:** Colors, branding, content

### Notification Content
- **Modify:** `NotificationService.php`
- **Add new types:** Create new mail classes
- **Customize triggers:** Update controllers

## 📞 Support

If you need help with email configuration:
1. **Check this guide** for common solutions
2. **Review Laravel logs** for error details
3. **Test with different SMTP providers**
4. **Contact your hosting provider** for SMTP support

---

**Note:** Email notifications enhance user experience by keeping everyone informed about important system events. Proper configuration ensures reliable delivery and professional communication. 