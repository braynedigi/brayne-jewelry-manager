<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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
            
            // This would integrate with your email service
            // For now, we'll just mark it as sent
            $notification->markEmailAsSent();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
            return false;
        }
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