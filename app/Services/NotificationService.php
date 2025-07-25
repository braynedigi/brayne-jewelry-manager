<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use App\Mail\DynamicEmail;

class NotificationService
{
    /**
     * Send notification to specific user
     */
    public static function sendToUser($userId, $type, $title, $message, $data = null)
    {
        $notification = Notification::createNotification($userId, $type, $title, $message, $data);
        
        // Send email if enabled
        if (Setting::getValue('email_notifications', true)) {
            self::sendEmailNotification($notification);
        }
        
        return $notification;
    }

    /**
     * Send notification to all users of specific role
     */
    public static function sendToRole($role, $type, $title, $message, $data = null)
    {
        $users = User::where('role', $role)->get();
        
        foreach ($users as $user) {
            self::sendToUser($user->id, $type, $title, $message, $data);
        }
    }

    /**
     * Send notification to all distributors
     */
    public static function sendToDistributors($type, $title, $message, $data = null)
    {
        self::sendToRole('distributor', $type, $title, $message, $data);
    }

    /**
     * Send notification to all admins
     */
    public static function sendToAdmins($type, $title, $message, $data = null)
    {
        self::sendToRole('admin', $type, $title, $message, $data);
    }

    /**
     * Send email notification
     */
    public static function sendEmailNotification($notification)
    {
        try {
            $user = $notification->user;
            self::updateMailConfig();

            // Try to use a database-driven template
            $template = EmailTemplate::getByType($notification->type);
            $data = $notification->data ?? [];
            $data['title'] = $notification->title;
            $data['message'] = $notification->message;
            $data['user_name'] = $user->name ?? '';
            $data['system_name'] = config('app.name', 'Jewelry Manager');

            // Add order-specific data if available
            if (isset($data['order_id'])) {
                $order = \App\Models\Order::with(['customer', 'distributor.user', 'orderItems.product'])->find($data['order_id']);
                if ($order) {
                    $data['order_number'] = $order->order_number;
                    $data['customer_name'] = $order->customer->name ?? '';
                    $data['customer_email'] = $order->customer->email ?? '';
                    $data['customer_phone'] = $order->customer->phone ?? '';
                    $data['customer_address'] = $order->customer->address ?? '';
                    $data['distributor_name'] = $order->distributor->user->name ?? '';
                    $data['status'] = $order->order_status;
                    $data['priority'] = $order->priority;
                    $data['total_amount'] = number_format($order->total_amount, 2);
                    $data['estimated_delivery'] = $order->estimated_delivery ? $order->estimated_delivery->format('M d, Y') : 'Not set';
                    
                    // Add products data
                    $data['products'] = $order->orderItems->map(function($item) {
                        return [
                            'name' => $item->product->name ?? '',
                            'quantity' => $item->quantity,
                            'price' => number_format($item->price, 2)
                        ];
                    })->toArray();
                }
            }

            // Add customer-specific data if available
            if (isset($data['customer_id'])) {
                $customer = \App\Models\Customer::with('distributor.user')->find($data['customer_id']);
                if ($customer) {
                    $data['customer_name'] = $customer->name ?? '';
                    $data['customer_email'] = $customer->email ?? '';
                    $data['customer_phone'] = $customer->phone ?? '';
                    $data['customer_address'] = $customer->address ?? '';
                    $data['distributor_name'] = $customer->distributor->user->name ?? '';
                }
            }

            // Add product-specific data if available
            if (isset($data['product_id'])) {
                $product = \App\Models\Product::with('category', 'subcategory')->find($data['product_id']);
                if ($product) {
                    $data['product_name'] = $product->name ?? '';
                    $data['product_category'] = $product->category->name ?? '';
                    $data['product_subcategory'] = $product->subcategory->name ?? '';
                    $data['product_description'] = $product->description ?? '';
                    $data['product_price'] = number_format($product->base_price, 2);
                }
            }

            if ($template) {
                $rendered = $template->render($data);
                Mail::to($user->email)->send(new DynamicEmail($rendered['subject'], $rendered['content']));
            } else {
                // Fallback to old mailables for now
                switch ($notification->type) {
                    case 'order_status_updated':
                        $order = \App\Models\Order::find($notification->data['order_id'] ?? null);
                        if ($order) {
                            $oldStatus = $notification->data['old_status'] ?? 'unknown';
                            $newStatus = $notification->data['new_status'] ?? 'unknown';
                            Mail::to($user->email)->send(new \App\Mail\OrderStatusNotification($order, $oldStatus, $newStatus, $notification->message));
                        }
                        break;
                    case 'order_created':
                        $order = \App\Models\Order::find($notification->data['order_id'] ?? null);
                        if ($order) {
                            Mail::to($user->email)->send(new \App\Mail\OrderCreatedNotification($order));
                        }
                        break;
                    default:
                        Mail::to($user->email)->send(new \App\Mail\GeneralNotification(
                            $notification->title,
                            $notification->message,
                            'info',
                            $notification->data ?? []
                        ));
                        break;
                }
            }

            $notification->markEmailAsSent();
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update mail configuration from settings
     */
    private static function updateMailConfig()
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

    /**
     * Order-related notifications
     */
    public static function orderCreated($order)
    {
        if (!Setting::getValue('order_notifications', true)) {
            return;
        }

        $title = 'New Order Created';
        $message = "Order #{$order->order_number} has been created by {$order->customer->name}";
        $data = ['order_id' => $order->id, 'order_number' => $order->order_number];

        // Notify admins
        self::sendToAdmins('order_created', $title, $message, $data);
        
        // Notify the distributor
        if ($order->distributor && $order->distributor->user) {
            self::sendToUser($order->distributor->user->id, 'order_created', $title, $message, $data);
        }
    }

    public static function orderStatusUpdated($order, $oldStatus, $newStatus)
    {
        if (!Setting::getValue('order_notifications', true)) {
            return;
        }

        $title = 'Order Status Updated';
        $message = "Order #{$order->order_number} status changed from {$oldStatus} to {$newStatus}";
        $data = ['order_id' => $order->id, 'order_number' => $order->order_number, 'old_status' => $oldStatus, 'new_status' => $newStatus];

        // Notify admins
        self::sendToAdmins('order_status_updated', $title, $message, $data);
        
        // Notify the distributor
        if ($order->distributor && $order->distributor->user) {
            self::sendToUser($order->distributor->user->id, 'order_status_updated', $title, $message, $data);
        }
    }

    /**
     * Customer-related notifications
     */
    public static function customerCreated($customer)
    {
        if (!Setting::getValue('customer_notifications', true)) {
            return;
        }

        $title = 'New Customer Added';
        $message = "Customer {$customer->name} has been added to the system";
        $data = ['customer_id' => $customer->id, 'customer_name' => $customer->name];

        // Notify admins
        self::sendToAdmins('customer_created', $title, $message, $data);
        
        // Notify the distributor who created the customer
        if ($customer->distributor && $customer->distributor->user) {
            self::sendToUser($customer->distributor->user->id, 'customer_created', $title, $message, $data);
        }
    }

    /**
     * Product-related notifications
     */
    public static function productCreated($product)
    {
        if (!Setting::getValue('product_notifications', true)) {
            return;
        }

        $title = 'New Product Added';
        $message = "Product {$product->name} has been added to the catalog";
        $data = ['product_id' => $product->id, 'product_name' => $product->name];

        // Notify admins
        self::sendToAdmins('product_created', $title, $message, $data);
        
        // Notify all distributors
        self::sendToDistributors('product_created', $title, $message, $data);
    }

    public static function productUpdated($product)
    {
        if (!Setting::getValue('product_notifications', true)) {
            return;
        }

        $title = 'Product Updated';
        $message = "Product {$product->name} has been updated";
        $data = ['product_id' => $product->id, 'product_name' => $product->name];

        // Notify admins
        self::sendToAdmins('product_updated', $title, $message, $data);
        
        // Notify all distributors
        self::sendToDistributors('product_updated', $title, $message, $data);
    }

    /**
     * User-related notifications
     */
    public static function userCreated($user)
    {
        $title = 'New User Account Created';
        $message = "User account for {$user->name} has been created";
        $data = ['user_id' => $user->id, 'user_name' => $user->name];

        // Notify admins
        self::sendToAdmins('user_created', $title, $message, $data);
    }

    /**
     * Get unread notifications count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)->unread()->count();
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)->unread()->update(['read_at' => now()]);
    }
} 