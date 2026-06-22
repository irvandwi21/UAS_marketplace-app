<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar customer
     */
    public function index()
    {
        $customers = Order::selectRaw("
                customer_name,
                phone,
                address,
                COUNT(*) as totalOrder,
                SUM(total_price) as totalBelanja
            ")
            ->groupBy(
                'customer_name',
                'phone',
                'address'
            )
            ->orderBy('customer_name')
            ->get()
            ->map(function ($customer, $index) {

                return [
                    'id' => $index + 1,

                    'name' => $customer->customer_name,

                    // karena di tabel orders tidak ada email
                    'email' => '-',

                    'phone' => $customer->phone,

                    'address' => $customer->address,

                    'totalOrder' => (int) $customer->totalOrder,

                    'totalBelanja' => (float) $customer->totalBelanja,

                    // sementara otomatis aktif
                    'status' => 'Aktif',
                ];
            });

        return response()->json($customers);
    }

    /**
     * Detail customer berdasarkan nomor HP
     */
    public function show($phone)
    {
        $customer = Order::where('phone', $phone)
            ->selectRaw("
                customer_name,
                phone,
                address,
                COUNT(*) as totalOrder,
                SUM(total_price) as totalBelanja
            ")
            ->groupBy(
                'customer_name',
                'phone',
                'address'
            )
            ->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'name' => $customer->customer_name,
            'email' => '-',
            'phone' => $customer->phone,
            'address' => $customer->address,
            'totalOrder' => (int) $customer->totalOrder,
            'totalBelanja' => (float) $customer->totalBelanja,
            'status' => 'Aktif'
        ]);
    }
}