<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Schema::hasTable('orders')
            ? Order::query()->latest()->get()
            : collect();

        return view('admin.orders.index', compact('orders'));
    }
}
