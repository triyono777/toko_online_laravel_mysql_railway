<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Schema::hasTable('products')
            ? Product::query()->with('category')->latest()->get()
            : collect();

        return view('admin.products.index', compact('products'));
    }
}
