<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'phone',
        'address',
        'total_price',
        'payment_method',
        'payment_status',
        'shipping_status',
        'tracking_number'
    ];

    /**
     * Relasi ke OrderItem (Satu Order memiliki banyak Items)
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}