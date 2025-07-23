<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'distributor_id',
        'customer_id',
        'courier_id',
        'order_number',
        'total_amount',
        'payment_status',
        'order_status',
        'priority',
        'estimated_start_date',
        'estimated_production_complete',
        'estimated_finishing_complete',
        'estimated_delivery_ready',
        'production_started_at',
        'production_completed_at',
        'finishing_started_at',
        'finishing_completed_at',
        'estimated_production_hours',
        'estimated_finishing_hours',
        'production_notes',
        'notes',
        'template_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'estimated_start_date' => 'datetime',
        'estimated_production_complete' => 'datetime',
        'estimated_finishing_complete' => 'datetime',
        'estimated_delivery_ready' => 'datetime',
        'production_started_at' => 'datetime',
        'production_completed_at' => 'datetime',
        'finishing_started_at' => 'datetime',
        'finishing_completed_at' => 'datetime',
    ];

    /**
     * Get the distributor that owns the order
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    /**
     * Get the customer for this order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the courier for this order
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }

    /**
     * Get the template used for this order
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(OrderTemplate::class, 'template_id');
    }

    /**
     * Get the products for this order
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity', 'price', 'metal', 'font')
                    ->withTimestamps();
    }

    /**
     * Get the status history for this order
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Check if order is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'fully_paid';
    }

    /**
     * Check if order is partially paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partially_paid';
    }

    /**
     * Check if order is unpaid
     */
    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Check if order is approved (visible to factory)
     */
    public function isApproved(): bool
    {
        return in_array($this->order_status, ['approved', 'in_production', 'finishing', 'ready_for_delivery']);
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Unpaid',
            'partially_paid' => '50% Paid',
            'fully_paid' => 'Fully Paid',
            default => 'Unknown'
        };
    }

    /**
     * Get payment status color for badges
     */
    public function getPaymentStatusColor(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'danger',
            'partially_paid' => 'warning',
            'fully_paid' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get order status label
     */
    public function getOrderStatusLabel(): string
    {
        return match($this->order_status) {
            'pending_payment' => 'Pending 50% Payment',
            'approved' => 'Approved',
            'in_production' => 'In Production',
            'finishing' => 'Finishing',
            'ready_for_delivery' => 'Ready for Delivery',
            'delivered_to_brayne' => 'Delivered to Brayne Jewelry',
            'delivered_to_client' => 'Delivered to Client',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get order status color for badges
     */
    public function getOrderStatusColor(): string
    {
        return match($this->order_status) {
            'pending_payment' => 'warning',
            'approved' => 'info',
            'in_production' => 'primary',
            'finishing' => 'info',
            'ready_for_delivery' => 'success',
            'delivered_to_brayne' => 'success',
            'delivered_to_client' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get available statuses for admin
     */
    public static function getAdminStatuses(): array
    {
        return [
            'pending_payment' => 'Pending 50% Payment',
            'approved' => 'Approved',
            'in_production' => 'In Production',
            'finishing' => 'Finishing',
            'ready_for_delivery' => 'Ready for Delivery',
            'delivered_to_brayne' => 'Delivered to Brayne Jewelry',
            'delivered_to_client' => 'Delivered to Client',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get available statuses for factory
     */
    public static function getFactoryStatuses(): array
    {
        return [
            'approved' => 'Queue',
            'in_production' => 'In Production',
            'finishing' => 'Finishing',
            'ready_for_delivery' => 'Ready for Delivery',
        ];
    }

    /**
     * Get available statuses for distributor (read-only)
     */
    public static function getDistributorStatuses(): array
    {
        return [
            'pending_payment' => 'Pending 50% Payment',
            'approved' => 'Approved',
            'in_production' => 'In Production',
            'finishing' => 'Finishing',
            'ready_for_delivery' => 'Ready for Delivery',
            'delivered_to_brayne' => 'Delivered to Brayne Jewelry',
            'delivered_to_client' => 'Delivered to Client',
        ];
    }

    /**
     * Check if user can update order status
     */
    public function canUpdateStatus($user): bool
    {
        if ($user->isAdmin()) {
            return true; // Admin can update any status
        }
        
        if ($user->isFactory()) {
            // Factory can only update approved orders to production statuses
            return in_array($this->order_status, ['approved', 'in_production', 'finishing', 'ready_for_delivery']);
        }
        
        return false; // Distributors cannot update status
    }

    /**
     * Get next available statuses for current user based on refined workflow
     */
    public function getNextAvailableStatuses($user): array
    {
        if ($user->isAdmin()) {
            // Admin can move orders through the entire workflow
            return match($this->order_status) {
                'pending_payment' => ['approved' => 'Approved', 'cancelled' => 'Cancelled'],
                'approved' => ['in_production' => 'In Production', 'cancelled' => 'Cancelled'],
                'in_production' => ['finishing' => 'Finishing', 'cancelled' => 'Cancelled'],
                'finishing' => ['ready_for_delivery' => 'Ready for Delivery', 'cancelled' => 'Cancelled'],
                'ready_for_delivery' => ['delivered_to_brayne' => 'Delivered to Brayne Jewelry', 'cancelled' => 'Cancelled'],
                'delivered_to_brayne' => ['delivered_to_client' => 'Delivered to Client'],
                'delivered_to_client' => [], // Final status
                'cancelled' => [], // Cannot move from cancelled
                default => []
            };
        }
        
        if ($user->isFactory()) {
            // Factory can only move forward in the production process
            return match($this->order_status) {
                'approved' => ['in_production' => 'In Production'],
                'in_production' => ['finishing' => 'Finishing'],
                'finishing' => ['ready_for_delivery' => 'Ready for Delivery'],
                'ready_for_delivery' => [], // Factory stops here, admin handles final delivery
                default => []
            };
        }
        
        return []; // Distributors cannot update status
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'low' => 'Low',
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            default => 'Normal'
        };
    }

    /**
     * Get priority color for badges
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'secondary',
            'normal' => 'info',
            'urgent' => 'danger',
            default => 'info'
        };
    }

    /**
     * Get priority icon
     */
    public function getPriorityIcon(): string
    {
        return match($this->priority) {
            'low' => 'fas fa-arrow-down',
            'normal' => 'fas fa-minus',
            'urgent' => 'fas fa-exclamation-triangle',
            default => 'fas fa-minus'
        };
    }

    /**
     * Calculate production progress percentage
     */
    public function getProductionProgress(): int
    {
        if ($this->order_status === 'approved') {
            return 0;
        } elseif ($this->order_status === 'in_production') {
            return 33;
        } elseif ($this->order_status === 'finishing') {
            return 66;
        } elseif ($this->order_status === 'ready_for_delivery') {
            return 100;
        }
        
        return 0;
    }

    /**
     * Get estimated completion date based on current status
     */
    public function getEstimatedCompletionDate(): ?string
    {
        return match($this->order_status) {
            'approved' => $this->estimated_production_complete?->format('M d, Y'),
            'in_production' => $this->estimated_finishing_complete?->format('M d, Y'),
            'finishing' => $this->estimated_delivery_ready?->format('M d, Y'),
            'ready_for_delivery' => $this->estimated_delivery_ready?->format('M d, Y'),
            default => null
        };
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        $completionDate = $this->getEstimatedCompletionDate();
        if (!$completionDate) return false;
        
        return now()->isAfter($this->estimated_delivery_ready);
    }

    /**
     * Get total estimated hours
     */
    public function getTotalEstimatedHours(): int
    {
        return ($this->estimated_production_hours ?? 0) + ($this->estimated_finishing_hours ?? 0);
    }

    /**
     * Scope for orders by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for overdue orders
     */
    public function scopeOverdue($query)
    {
        return $query->where('estimated_delivery_ready', '<', now());
    }

    /**
     * Scope for orders due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('estimated_delivery_ready', today());
    }

    /**
     * Scope for orders due this week
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('estimated_delivery_ready', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
}
