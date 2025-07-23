<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'street',
        'barangay',
        'city',
        'province',
        'country',
        'is_international',
    ];

    protected $casts = [
        'is_international' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getCurrencyAttribute(): string
    {
        return $this->is_international ? 'USD' : 'PHP';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return $this->is_international ? '$' : '₱';
    }
}
