<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    public function index(): View
    {
        $suggestedProducts = collect();

        if (Schema::hasTable('products')) {
            $suggestedProducts = Product::query()
                ->active()
                ->latest()
                ->take(4)
                ->get();
        }

        return view('storefront.cart.index', [
            'cartItems' => collect(),
            'suggestedProducts' => $suggestedProducts,
        ]);
    }
}
