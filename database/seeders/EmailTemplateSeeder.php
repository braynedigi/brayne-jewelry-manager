<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Order Status Update Template
        EmailTemplate::create([
            'name' => 'order_status_update',
            'subject' => 'Order #{{order_number}} Status Updated - {{new_status}}',
            'content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #007bff; margin-top: 0;">Order Status Update</h2>
        <p>Hello {{user_name}},</p>
        <p>The status of order <strong>#{{order_number}}</strong> has been updated.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p><strong>Previous Status:</strong> {{old_status}}</p>
            <p><strong>New Status:</strong> <span style="color: #28a745; font-weight: bold;">{{new_status}}</span></p>
            <p><strong>Customer:</strong> {{customer_name}}</p>
            <p><strong>Total Amount:</strong> ${{total_amount}}</p>
            <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
        </div>
        
        @if(notes)
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107;">
            <h4 style="margin-top: 0; color: #856404;">Additional Notes:</h4>
            <p>{{notes}}</p>
        </div>
        @endif
        
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>',
            'variables' => [
                'order_number', 'old_status', 'new_status', 'customer_name', 
                'total_amount', 'estimated_delivery', 'notes', 'user_name', 'system_name'
            ],
            'type' => 'order_status_updated',
            'is_active' => true,
            'description' => 'Email template for order status updates'
        ]);

        // Order Created Template
        EmailTemplate::create([
            'name' => 'order_created',
            'subject' => 'New Order #{{order_number}} Created',
            'content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order Created</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #28a745; margin-top: 0;">New Order Created</h2>
        <p>Hello {{user_name}},</p>
        <p>A new order has been created in the system.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #007bff;">Order Details</h3>
            <p><strong>Order Number:</strong> #{{order_number}}</p>
            <p><strong>Customer:</strong> {{customer_name}}</p>
            <p><strong>Distributor:</strong> {{distributor_name}}</p>
            <p><strong>Status:</strong> <span style="color: #007bff; font-weight: bold;">{{status}}</span></p>
            <p><strong>Priority:</strong> <span style="color: #dc3545; font-weight: bold;">{{priority}}</span></p>
            <p><strong>Total Amount:</strong> ${{total_amount}}</p>
            <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
        </div>
        
        <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h4 style="margin-top: 0;">Products Ordered:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach(products as product)
                <li><strong>{{product.name}}</strong> - Qty: {{product.quantity}} - ${{product.price}}</li>
                @endforeach
            </ul>
        </div>
        
        <p>Please review and process this order accordingly.</p>
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>',
            'variables' => [
                'order_number', 'customer_name', 'distributor_name', 'status', 
                'priority', 'total_amount', 'estimated_delivery', 'products', 'user_name', 'system_name'
            ],
            'type' => 'order_created',
            'is_active' => true,
            'description' => 'Email template for new order notifications'
        ]);

        // General Notification Template
        EmailTemplate::create([
            'name' => 'general_notification',
            'subject' => '{{title}}',
            'content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{title}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #007bff; margin-top: 0;">{{title}}</h2>
        <p>Hello {{user_name}},</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p>{{message}}</p>
        </div>
        
        @if(data)
        <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h4 style="margin-top: 0;">Additional Information:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach(data as key => value)
                <li><strong>{{key}}:</strong> {{value}}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>',
            'variables' => [
                'title', 'message', 'data', 'user_name', 'system_name'
            ],
            'type' => 'general',
            'is_active' => true,
            'description' => 'General notification email template'
        ]);

        // Customer Created Template
        EmailTemplate::create([
            'name' => 'customer_created',
            'subject' => 'New Customer Added - {{customer_name}}',
            'content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Customer Added</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #28a745; margin-top: 0;">New Customer Added</h2>
        <p>Hello {{user_name}},</p>
        <p>A new customer has been added to the system.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #007bff;">Customer Details</h3>
            <p><strong>Name:</strong> {{customer_name}}</p>
            <p><strong>Email:</strong> {{customer_email}}</p>
            <p><strong>Phone:</strong> {{customer_phone}}</p>
            <p><strong>Address:</strong> {{customer_address}}</p>
            <p><strong>Added By:</strong> {{distributor_name}}</p>
        </div>
        
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>',
            'variables' => [
                'customer_name', 'customer_email', 'customer_phone', 'customer_address', 
                'distributor_name', 'user_name', 'system_name'
            ],
            'type' => 'customer_created',
            'is_active' => true,
            'description' => 'Email template for new customer notifications'
        ]);

        // Product Created Template
        EmailTemplate::create([
            'name' => 'product_created',
            'subject' => 'New Product Added - {{product_name}}',
            'content' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Product Added</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #28a745; margin-top: 0;">New Product Added</h2>
        <p>Hello {{user_name}},</p>
        <p>A new product has been added to the catalog.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #007bff;">Product Details</h3>
            <p><strong>Name:</strong> {{product_name}}</p>
            <p><strong>Category:</strong> {{product_category}}</p>
            <p><strong>Subcategory:</strong> {{product_subcategory}}</p>
            <p><strong>Description:</strong> {{product_description}}</p>
            <p><strong>Base Price:</strong> ${{product_price}}</p>
        </div>
        
        <p>This product is now available for ordering.</p>
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>',
            'variables' => [
                'product_name', 'product_category', 'product_subcategory', 
                'product_description', 'product_price', 'user_name', 'system_name'
            ],
            'type' => 'product_created',
            'is_active' => true,
            'description' => 'Email template for new product notifications'
        ]);
    }
} 