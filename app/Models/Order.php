<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_zone',
        'delivery_address',
        'currency_used',
        'subtotal_xof',
        'shipping_amount_xof',
        'total_amount_xof',
        'total_amount_eur',
        'payment_method',
        'payment_status',
        'order_status',
        'notes',
    ];

    protected $casts = [
        'subtotal_xof' => 'integer',
        'shipping_amount_xof' => 'integer',
        'total_amount_xof' => 'integer',
        'total_amount_eur' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalXofAttribute(): string
    {
        return number_format($this->total_amount_xof, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedTotalEurAttribute(): string
    {
        return number_format($this->total_amount_eur, 2, ',', ' ') . ' €';
    }
}
