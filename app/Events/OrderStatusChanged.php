<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $previousStatus;
    public $newStatus;
    public $changedBy;
    public $notes;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $previousStatus, string $newStatus, User $changedBy, ?string $notes = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
        $this->notes = $notes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to admin users
        $channels[] = new PrivateChannel('admin');

        // Broadcast to factory users if status is production-related
        $productionStatuses = ['approved', 'in_production', 'finishing', 'ready_for_delivery'];
        if (in_array($this->newStatus, $productionStatuses)) {
            $channels[] = new PrivateChannel('factory');
        }

        // Broadcast to distributor who owns the order
        if ($this->order->distributor) {
            $channels[] = new PrivateChannel('distributor.' . $this->order->distributor_id);
        }

        // Broadcast to specific order channel for real-time updates
        $channels[] = new PrivateChannel('order.' . $this->order->id);

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'status_label' => $this->order->getOrderStatusLabel(),
            'changed_by' => $this->changedBy->name,
            'changed_by_role' => $this->changedBy->role,
            'notes' => $this->notes,
            'timestamp' => now()->toISOString(),
            'customer_name' => $this->order->customer ? $this->order->customer->name : 'Unknown',
            'distributor_name' => $this->order->distributor ? $this->order->distributor->name : 'Unknown',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.status.changed';
    }
}
