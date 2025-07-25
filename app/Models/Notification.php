<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'email_sent',
        'email_sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_sent' => 'boolean'
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Mark email as sent
     */
    public function markEmailAsSent()
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => now()
        ]);
    }

    /**
     * Create a new notification
     */
    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        $notification = static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);

        // Send email notification if enabled
        if (Setting::getValue('email_notifications', true)) {
            // Queue email sending
            dispatch(function () use ($notification) {
                $notification->sendEmail();
            })->afterResponse();
        }

        return $notification;
    }

    /**
     * Send email notification
     */
    public function sendEmail()
    {
        try {
            \App\Services\NotificationService::sendEmailNotification($this);
        } catch (\Exception $e) {
            \Log::error('Failed to send email for notification ' . $this->id . ': ' . $e->getMessage());
        }
    }
}
