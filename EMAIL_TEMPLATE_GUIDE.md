# Email Template Management System

## Overview

The Jewelry Manager now includes a database-driven email template system that allows administrators to create, edit, and manage email templates without touching code. This system replaces the hardcoded Blade templates with dynamic templates stored in the database.

## Features

- **Dynamic Templates**: Email content and subjects are stored in the database
- **Variable Replacement**: Templates support dynamic variables like `{{order_number}}`, `{{customer_name}}`, etc.
- **Admin Interface**: Full CRUD operations through the admin panel
- **Template Types**: Different templates for different notification types
- **Preview & Test**: Built-in preview and test functionality
- **Fallback System**: Falls back to old system if no template is found

## Accessing Email Templates

1. Log in as an administrator
2. Navigate to **Email Templates** in the sidebar
3. You'll see a list of all available templates

## Template Types

The system supports the following template types:

- **order_status_updated**: When order status changes
- **order_created**: When a new order is created
- **customer_created**: When a new customer is added
- **product_created**: When a new product is added
- **general**: For general notifications

## Available Variables

### Common Variables (All Templates)
- `{{user_name}}`: Name of the user receiving the email
- `{{system_name}}`: Application name (Jewelry Manager)
- `{{title}}`: Notification title
- `{{message}}`: Notification message

### Order-Related Variables
- `{{order_number}}`: Order number (e.g., ORD-001)
- `{{old_status}}`: Previous order status
- `{{new_status}}`: New order status
- `{{customer_name}}`: Customer name
- `{{customer_email}}`: Customer email
- `{{customer_phone}}`: Customer phone
- `{{customer_address}}`: Customer address
- `{{distributor_name}}`: Distributor name
- `{{status}}`: Current order status
- `{{priority}}`: Order priority
- `{{total_amount}}`: Order total amount
- `{{estimated_delivery}}`: Estimated delivery date
- `{{notes}}`: Additional notes
- `{{products}}`: Array of ordered products

### Customer-Related Variables
- `{{customer_name}}`: Customer name
- `{{customer_email}}`: Customer email
- `{{customer_phone}}`: Customer phone
- `{{customer_address}}`: Customer address
- `{{distributor_name}}`: Distributor who added the customer

### Product-Related Variables
- `{{product_name}}`: Product name
- `{{product_category}}`: Product category
- `{{product_subcategory}}`: Product subcategory
- `{{product_description}}`: Product description
- `{{product_price}}`: Product base price

## Creating a New Template

1. Click **Create New Template**
2. Fill in the template details:
   - **Name**: Unique identifier for the template
   - **Type**: Select the notification type
   - **Description**: Brief description of the template
   - **Subject**: Email subject line (can include variables)
   - **Content**: HTML email content (can include variables)
3. Click **Save Template**

## Editing Templates

1. Find the template you want to edit
2. Click the **Edit** button
3. Modify the subject and/or content
4. Use the variable insertion buttons to add dynamic content
5. Click **Save Changes**

## Previewing Templates

1. Click the **Preview** button on any template
2. Enter sample data for the variables
3. View how the email will look with real data

## Testing Templates

1. Click the **Test** button on any template
2. Enter an email address to send a test to
3. Enter sample data for the variables
4. Click **Send Test Email**

## Template Examples

### Order Status Update Template
```
Subject: Order #{{order_number}} Status Updated - {{new_status}}

Content:
Hello {{user_name}},

The status of order #{{order_number}} has been updated.

Previous Status: {{old_status}}
New Status: {{new_status}}
Customer: {{customer_name}}
Total Amount: ${{total_amount}}

Thank you for using {{system_name}}!
```

### New Order Template
```
Subject: New Order #{{order_number}} Created

Content:
Hello {{user_name}},

A new order has been created in the system.

Order Number: #{{order_number}}
Customer: {{customer_name}}
Distributor: {{distributor_name}}
Status: {{status}}
Priority: {{priority}}
Total Amount: ${{total_amount}}

Thank you for using {{system_name}}!
```

## Technical Details

### How It Works

1. When a notification is triggered, the system looks for a template matching the notification type
2. If found, it renders the template with the available data
3. The rendered HTML is sent using the `DynamicEmail` Mailable class
4. If no template is found, it falls back to the old system

### Database Schema

The `email_templates` table contains:
- `name`: Unique template identifier
- `subject`: Email subject template
- `content`: HTML email content template
- `variables`: JSON array of available variables
- `type`: Template type (notification type)
- `is_active`: Whether the template is active
- `description`: Template description

### File Structure

- `app/Models/EmailTemplate.php`: Template model
- `app/Http/Controllers/EmailTemplateController.php`: Admin controller
- `app/Mail/DynamicEmail.php`: Generic mailable for dynamic content
- `resources/views/admin/email-templates/`: Admin interface views
- `database/seeders/EmailTemplateSeeder.php`: Default templates

## Troubleshooting

### Template Not Working
1. Check if the template is active
2. Verify the template type matches the notification type
3. Ensure all required variables are available in the data

### Variables Not Replacing
1. Check variable syntax (use `{{variable_name}}`)
2. Verify the variable is available in the notification data
3. Check for typos in variable names

### Email Not Sending
1. Check email configuration in Settings
2. Verify SMTP settings are correct
3. Check application logs for errors

## Migration from Old System

The old system using hardcoded Blade templates is still available as a fallback. To fully migrate:

1. Create templates for all notification types
2. Test each template thoroughly
3. Once satisfied, you can remove the old Mailable classes

## Best Practices

1. **Test Templates**: Always test templates before using them in production
2. **Use Variables**: Leverage dynamic variables for personalized content
3. **Keep Templates Simple**: Avoid complex HTML that might break in email clients
4. **Backup Templates**: Export templates before making major changes
5. **Version Control**: Consider versioning important templates

## Support

For issues with the email template system:
1. Check the application logs
2. Verify email configuration
3. Test with the built-in test functionality
4. Contact system administrator if problems persist 