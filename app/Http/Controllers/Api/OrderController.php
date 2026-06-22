<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::query()->latest()->get()
        );
    }

    /**
     * Update status dan nomor resi pengiriman
     */
            public function updateStatus(Request $request, $order_code)
{
    $request->validate([
        'shipping_status' => 'required|in:Menunggu,Diproses,Dikirim,Selesai,Dibatalkan'
    ]);

    $order = Order::where('order_code', $order_code)->first();

    if (!$order) {
        return response()->json([
            'message' => 'Data transaksi tidak ditemukan'
        ], 404);
    }

    $trackingNumber = $order->tracking_number;

    // otomatis buat resi saat status menjadi Dikirim
    if (
        $request->shipping_status === 'Dikirim'
        && empty($trackingNumber)
    ) {
        $trackingNumber = $this->generateTrackingNumber();
    }

    $order->update([
        'shipping_status' => $request->shipping_status,
        'tracking_number' => $trackingNumber
    ]);

    return response()->json([
        'message' => 'Status berhasil diperbarui',
        'data' => $order->fresh()
    ]);
}
    private function generateTrackingNumber()
{
    return 'RESI-' .
        strtoupper(substr(md5(uniqid()), 0, 8));
}
    /**
     * Hapus data pesanan
     */

        public function store(Request $request)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:30',
        'address' => 'required|string',
        'product_name' => 'required|string|max:255',
        'qty' => 'required|integer|min:1',
        'total_price' => 'required|numeric|min:0',
        'payment_method' => 'required|string'
    ]);

    $paymentStatus = $request->payment_method === 'COD'
        ? 'Pending'
        : 'Lunas';

    $order = Order::create([
        'order_code' => 'ORD-' . now()->format('YmdHis'),
        'customer_name' => $request->customer_name,
        'phone' => $request->phone,
        'address' => $request->address,
        'product_name' => $request->product_name,
        'qty' => $request->qty,
        'total_price' => $request->total_price,

        'payment_method' => $request->payment_method,
        'payment_status' => $paymentStatus,

        'shipping_status' => 'Diproses',

        // awalnya kosong
        'tracking_number' => null
    ]);

    return response()->json([
        'message' => 'Pesanan berhasil dibuat',
        'data' => $order
    ], 201);
}

    public function destroy($order_code)
    {
        $order = Order::where('order_code', $order_code)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Data transaksi tidak ditemukan'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Data transaksi berhasil dihapus'
        ]);
    }
}