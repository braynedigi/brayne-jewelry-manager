<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'sku', 'category', 'sub_category', 'custom_sub_category',
        'metals', 'local_pricing', 'international_pricing', 'fonts', 'font_requirement', 
        'stones', 'requires_stones', 'requires_ring_size', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metals' => 'array',
        'local_pricing' => 'array',
        'international_pricing' => 'array',
        'fonts' => 'array',
        'font_requirement' => 'integer',
        'stones' => 'array',
        'requires_stones' => 'boolean',
        'requires_ring_size' => 'boolean',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product')
                    ->withPivot('quantity', 'price', 'metal', 'font', 'names', 'stones', 'ring_size')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods for categories
    public static function getCategories(): array
    {
        return ProductCategory::active()->parents()->ordered()->pluck('name')->toArray();
    }

    public static function getSubCategories(string $category = null): array
    {
        if (!$category) {
            return [];
        }
        
        $parentCategory = ProductCategory::where('name', $category)->first();
        if (!$parentCategory) {
            return [];
        }
        
        return $parentCategory->children()->active()->ordered()->pluck('name')->toArray();
    }

    public function categoryRelation()
    {
        return $this->belongsTo(ProductCategory::class, 'category', 'name');
    }

    public function subCategoryRelation()
    {
        return $this->belongsTo(ProductCategory::class, 'sub_category', 'name');
    }

    public static function getMetals(): array
    {
        return [
            'Stainless',
            'Brass Gold',
            '925 Pure Sterling Silver',
            '10K Real Gold',
            '14K Real Gold',
            '18K Real Gold'
        ];
    }

    public function getPriceForMetal(string $metal, bool $isInternational = false): ?float
    {
        $pricing = $isInternational ? $this->international_pricing : $this->local_pricing;
        
        if (!$pricing || !isset($pricing[$metal])) {
            return null;
        }
        return (float) $pricing[$metal];
    }

    public function getFormattedPriceForMetal(string $metal, bool $isInternational = false): ?string
    {
        $price = $this->getPriceForMetal($metal, $isInternational);
        if ($price === null) {
            return null;
        }
        
        $symbol = $isInternational ? '$' : '₱';
        return $symbol . number_format($price, 2);
    }

    public function getMinPrice(bool $isInternational = false): ?float
    {
        $pricing = $isInternational ? $this->international_pricing : $this->local_pricing;
        
        if (!$pricing || empty($pricing)) {
            return null;
        }
        return min(array_values($pricing));
    }

    public function getMaxPrice(bool $isInternational = false): ?float
    {
        $pricing = $isInternational ? $this->international_pricing : $this->local_pricing;
        
        if (!$pricing || empty($pricing)) {
            return null;
        }
        return max(array_values($pricing));
    }

    public function getPriceRange(bool $isInternational = false): ?string
    {
        $minPrice = $this->getMinPrice($isInternational);
        $maxPrice = $this->getMaxPrice($isInternational);
        
        if ($minPrice === null || $maxPrice === null) {
            return null;
        }
        
        $symbol = $isInternational ? '$' : '₱';
        
        if ($minPrice === $maxPrice) {
            return $symbol . number_format($minPrice, 2);
        }
        
        return $symbol . number_format($minPrice, 2) . ' - ' . $symbol . number_format($maxPrice, 2);
    }

    public function hasFonts(): bool
    {
        return !empty($this->fonts) && $this->font_requirement > 0;
    }

    public function getFontRequirementText(): string
    {
        if ($this->font_requirement <= 0) {
            return 'No fonts required';
        }
        
        return "Font {$this->font_requirement} (Only {$this->font_requirement} " . 
               ($this->font_requirement === 1 ? 'font' : 'fonts') . ")";
    }

    public function getAvailableFonts(): array
    {
        return \App\Models\ProductFont::active()->ordered()->pluck('name')->toArray();
    }

    public function getAvailableStones(): array
    {
        return \App\Models\ProductStone::active()->ordered()->pluck('name')->toArray();
    }

    public function getAvailableRingSizes(): array
    {
        return \App\Models\RingSize::active()->ordered()->pluck('size')->toArray();
    }

    public function hasStones(): bool
    {
        return $this->requires_stones && !empty($this->stones);
    }

    public function hasRingSize(): bool
    {
        return $this->requires_ring_size;
    }

    /**
     * Get the image URL for the product
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Check if the image exists in storage
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Check if the product has a valid image
     */
    public function hasImage(): bool
    {
        return $this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image);
    }
}
