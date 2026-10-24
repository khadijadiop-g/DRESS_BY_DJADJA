<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color_name',
        'color_hex',
        'sku',
        'stock_quantity',
        'price_xof_override',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'price_xof_override' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restockSubscriptions(): HasMany
    {
        return $this->hasMany(BackInStockSubscription::class);
    }

    public function getEffectivePriceXofAttribute(): int
    {
        return $this->price_xof_override ?? $this->product->base_price_xof;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }
}
