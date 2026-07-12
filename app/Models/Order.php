<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'receipt_no',
        'subtotal',
        'tax',
        'total',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax'      => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /**
     * The employee/cashier who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The line items belonging to this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
