<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Total pendapatan
        $totalRevenue = Order::sum('total_price');

        // Total pesanan
        $totalOrders = Order::count();

        // Total customer unik
        $totalCustomers = Order::distinct('phone')->count('phone');

        // Total produk
        $totalProducts = Product::count();

        // Penjualan per bulan
        $salesChart = Order::selectRaw("
                MONTH(created_at) as month,
                SUM(total_price) as sales
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {

                $months = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];

                return [
                    'month' => $months[$item->month],
                    'sales' => (int) $item->sales
                ];
            });

        // Produk terlaris
        $topProducts = Order::select(
                'product_name',
                DB::raw('SUM(qty) as sold')
            )
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'sold' => (int) $item->sold
                ];
            });

        // Top customer
        $topCustomers = Order::select(
                'customer_name',
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('customer_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'summary' => [
                'revenue' => $totalRevenue,
                'orders' => $totalOrders,
                'customers' => $totalCustomers,
                'products' => $totalProducts,
            ],

            'salesChart' => $salesChart,

            'topProducts' => $topProducts,

            'topCustomers' => $topCustomers
        ]);
    }
}