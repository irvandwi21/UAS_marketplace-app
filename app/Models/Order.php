<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dokumentasi Properti untuk VS Code / PHP Intelephense
 * 
 * @property string $order_code
 * @property string $customer_name
 * @property string $phone
 * @property string $address
 * @property string $product_name
 * @property int $qty
 * @property float $total_price
 * @property string $payment_method
 * @property string $payment_status
 * @property string $shipping_status
 * @property string $tracking_number
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'phone',
        'address',
        'product_name',
        'qty',
        'total_price',
        'payment_method',
        'payment_status',
        'shipping_status',
        'tracking_number'
    ];
}