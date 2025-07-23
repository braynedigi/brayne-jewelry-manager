<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    public $type;
    public $targetUser;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(string $title, string $message, string $type = 'info', ?User $targetUser = null, array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->targetUser = $targetUser;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->targetUser) {
            return [new PrivateChannel('user.' . $this->targetUser->id)];
        }

        // Broadcast to all authenticated users
        return [new PrivateChannel('notifications')];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'timestamp' => now()->toISOString(),
            'data' => $this->data,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notification.new';
    }
}
