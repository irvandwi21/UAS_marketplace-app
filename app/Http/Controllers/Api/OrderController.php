<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan beserta relasi item-nya.
     * Menggunakan try-catch bawaan Anda untuk keamanan ekstra.
     */
    public function index()
    {
        try {
            // Eager loading 'items' untuk menghindari N+1 query dan mencegah Error 500
            $orders = Order::with('items')->latest()->get();
            
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pesanan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan pesanan baru (Checkout).
     * Mempertahankan kalkulasi otomatis total_price & subtotal serta kode invoice bawaan Anda.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'        => 'required|string|max:255',
            'phone'                => 'required|string|max:20',
            'address'              => 'required|string',
            'payment_method'       => 'required|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required',
            'items.*.product_name' => 'required|string',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            // Menggunakan format kode invoice bawaan Anda
            $orderCode = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Menghitung total harga secara dinamis dari array items
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['price'] * $item['qty'];
            }

            // 1. Membuat data utama di tabel orders
            $order = Order::create([
                'order_code'     => $orderCode,
                'customer_name'  => $request->customer_name,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'total_price'    => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'PENDING',
                'shipping_status'=> 'PENDING',
                'tracking_number'=> null,
            ]);

            // 2. Membuat data item di tabel order_items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'] ?? 0,
                    'product_name' => $item['product_name'],
                    'qty'          => $item['qty'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['price'] * $item['qty'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data'    => $order->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui status pengiriman berdasarkan order_code.
     * Variabel $id diubah menjadi $order_code agar sesuai dengan parameter route.
     */
    public function updateStatus(Request $request, $order_code)
    {
        $request->validate([
            'shipping_status' => 'required|string'
        ]);

        // Cari pesanan menggunakan field order_code
        $order = Order::where('order_code', $order_code)->firstOrFail();

        $updateData = [
            'shipping_status' => $request->shipping_status
        ];

        // Otomatisasi generate nomor resi jika status berubah menjadi 'Dikirim'
        if ($request->shipping_status === 'Dikirim' && empty($order->tracking_number)) {
            $updateData['tracking_number'] = 'RES-' . strtoupper(Str::random(8));
        }

        $order->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'data'    => $order->fresh(['items']) // Fresh data sekaligus memuat relasi items-nya
        ]);
    }

    /**
     * Menghapus pesanan beserta item-item terkait di dalamnya menggunakan order_code.
     * Menggunakan Database Transaction untuk mencegah yatim/piatu data (data loss).
     */
    public function destroy($order_code)
    {
        // Cari pesanan berdasarkan order_code, memicu 404 jika tidak ditemukan
        $order = Order::where('order_code', $order_code)->firstOrFail();

        DB::beginTransaction();

        try {
            // 1. Hapus semua data anak di tabel order_items terlebih dahulu
            $order->items()->delete();

            // 2. Hapus data induk di tabel orders
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pesanan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}