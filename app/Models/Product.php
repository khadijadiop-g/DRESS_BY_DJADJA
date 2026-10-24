<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'subtitle',
        'description',
        'fabric_details',
        'care_guide',
        'base_price_xof',
        'is_featured',
        'is_active',
        'is_new_arrival',
    ];

    protected $casts = [
        'base_price_xof' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    protected $appends = [
        'main_image',
        'hover_image',
        'formatted_price_xof',
        'formatted_price_eur',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function getMainImageAttribute(): ?string
    {
        $firstImage = $this->images->firstWhere('is_hover', false) ?? $this->images->first();
        return $firstImage?->url;
    }

    public function getHoverImageAttribute(): ?string
    {
        $hover = $this->images->firstWhere('is_hover', true);
        return $hover?->url ?? $this->images->skip(1)->first()?->url ?? $this->main_image;
    }

    public function getFormattedPriceXofAttribute(): string
    {
        return number_format($this->base_price_xof, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedPriceEurAttribute(): string
    {
        $eur = round($this->base_price_xof / 655.957, 2);
        return number_format($eur, 2, ',', ' ') . ' €';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }
}
