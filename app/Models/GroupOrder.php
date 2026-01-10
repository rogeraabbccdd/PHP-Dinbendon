<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class GroupOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'store_id',
        'user_id',
        'status',
        'menu_snapshot',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'is_public' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that the group order belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the orders for the group order.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the order items for the group order.
     */
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Order::class);
    }

    /**
     * Interact with the group order's menu snapshot.
     *
     */
    protected function menuSnapshot(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value === null) {
                    return [];
                }
                $menu = json_decode($value, true);

                return array_map(function ($item) {
                    if (isset($item['price'])) {
                        $item['price'] = (float) $item['price'];
                    }

                    return $item;
                }, $menu);
            },
            set: fn ($value) => json_encode($value),
        );
    }
}
