<?php

namespace App\Services;

class InternationalDetectionService
{
    private const PHILIPPINES_COUNTRIES = [
        'Philippines',
        'PH',
        'PHL',
        'Filipinas',
        'Pilipinas'
    ];

    public function isInternational(string $country): bool
    {
        $normalizedCountry = trim(strtolower($country));
        
        foreach (self::PHILIPPINES_COUNTRIES as $philippinesCountry) {
            if (strtolower($philippinesCountry) === $normalizedCountry) {
                return false; // Local
            }
        }
        
        return true; // International
    }

    public function getCurrencySymbol(bool $isInternational): string
    {
        return $isInternational ? '$' : '₱';
    }

    public function getCurrencyCode(bool $isInternational): string
    {
        return $isInternational ? 'USD' : 'PHP';
    }
} 