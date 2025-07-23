<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'distributor_id',
        'name',
        'email',
        'phone',
        'street',
        'barangay',
        'city',
        'province',
        'country',
    ];

    /**
     * Get the distributor that owns the customer
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    /**
     * Get the orders for this customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the full address as a formatted string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];
        
        if ($this->street) {
            $parts[] = $this->street;
        }
        if ($this->barangay) {
            $parts[] = $this->barangay;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        if ($this->province) {
            $parts[] = $this->province;
        }
        if ($this->country) {
            $parts[] = $this->country;
        }
        
        return implode(', ', $parts);
    }

    /**
     * Check if customer has any address information
     */
    public function hasAddress(): bool
    {
        return !empty($this->street) || !empty($this->barangay) || 
               !empty($this->city) || !empty($this->province) || 
               !empty($this->country);
    }
}
