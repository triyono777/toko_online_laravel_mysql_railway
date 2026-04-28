<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'products' => Schema::hasTable('products') ? Product::query()->count() : 0,
            'customers' => Schema::hasTable('users') ? User::query()->where('role', 'customer')->count() : 0,
            'orders' => Schema::hasTable('orders') ? Order::query()->count() : 0,
            'revenue' => Schema::hasTable('orders')
                ? (float) Order::query()->where('payment_status', 'paid')->sum('grand_total')
                : 0,
        ];

        $latestOrders = Schema::hasTable('orders')
            ? Order::query()->latest()->take(5)->get()
            : collect();

        $lowStockProducts = Schema::hasTable('products')
            ? Product::query()->where('stock', '<=', 10)->orderBy('stock')->take(5)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'latestOrders', 'lowStockProducts'));
    }
}
