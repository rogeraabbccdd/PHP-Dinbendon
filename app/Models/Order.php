<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_order_id',
        'user_id',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'float',
        ];
    }

    /**
     * Get the group order that the order belongs to.
     */
    public function groupOrder(): BelongsTo
    {
        return $this->belongsTo(GroupOrder::class);
    }

    /**
     * Get the user that the order belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the store for the order.
     */
    public function store(): HasOneThrough
    {
        return $this->hasOneThrough(Store::class, GroupOrder::class, 'id', 'id', 'group_order_id', 'store_id');
    }
}
