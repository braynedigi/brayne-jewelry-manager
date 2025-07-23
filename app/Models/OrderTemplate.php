<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'name',
        'description',
        'products',
        'default_notes',
        'priority',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'products' => 'array',
        'default_notes' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    /**
     * Get products with their configurations
     */
    public function getProductsWithConfig(): array
    {
        return $this->products ?? [];
    }

    /**
     * Create an order from this template
     */
    public function createOrder(array $orderData = []): Order
    {
        $distributor = $this->distributor;
        $isInternational = $distributor->is_international;

        // Calculate total amount
        $totalAmount = 0;
        $products = [];

        foreach ($this->products as $productConfig) {
            $product = Product::find($productConfig['product_id']);
            if ($product) {
                $price = $product->getPriceForMetal($productConfig['metal'], $isInternational);
                if ($price !== null) {
                    $totalAmount += $price * $productConfig['quantity'];
                    $products[] = $productConfig;
                }
            }
        }

        // Create the order
        $order = Order::create([
            'distributor_id' => $this->distributor_id,
            'customer_id' => $orderData['customer_id'] ?? null,
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => $totalAmount,
            'order_status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'priority' => $orderData['priority'] ?? $this->priority,
            'notes' => $orderData['notes'] ?? ($this->default_notes['notes'] ?? null),
            'template_id' => $this->id,
        ]);

        // Attach products
        foreach ($products as $productConfig) {
            $product = Product::find($productConfig['product_id']);
            if ($product) {
                $price = $product->getPriceForMetal($productConfig['metal'], $isInternational);
                $order->products()->attach($productConfig['product_id'], [
                    'quantity' => $productConfig['quantity'],
                    'price' => $price,
                    'metal' => $productConfig['metal'],
                    'font' => $productConfig['font'] ?? null,
                ]);
            }
        }

        // Increment usage count
        $this->increment('usage_count');

        return $order;
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for distributor templates
     */
    public function scopeForDistributor($query, $distributorId)
    {
        return $query->where('distributor_id', $distributorId);
    }

    /**
     * Get priority options
     */
    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        return self::getPriorities()[$this->priority] ?? 'Normal';
    }

    /**
     * Get priority color
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'urgent' => 'danger',
            default => 'primary',
        };
    }
}
