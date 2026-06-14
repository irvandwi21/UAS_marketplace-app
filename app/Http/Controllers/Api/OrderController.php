<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'customer_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'product_name' => 'required',
        'qty' => 'required',
        'total_price' => 'required'
    ]);

    $order = Order::create([
        'order_code' => 'ORD' . time(),

        'customer_name' => $request->customer_name,

        'phone' => $request->phone,

        'address' => $request->address,

        'product_name' => $request->product_name,

        'qty' => $request->qty,

        'total_price' => $request->total_price,

        'payment_status' => 'Pending',

        'shipping_status' => 'Menunggu',

        'tracking_number' => '-'
    ]);

    return response()->json([
        'message' => 'Pesanan berhasil dibuat',
        'data' => $order
    ]);
   }
}