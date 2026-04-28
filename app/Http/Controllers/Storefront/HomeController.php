<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = collect();
        $featuredProducts = collect();
        $latestProducts = collect();

        if (Schema::hasTable('categories')) {
            $categories = Category::query()
                ->active()
                ->orderBy('sort_order')
                ->take(6)
                ->get();
        }

        if (Schema::hasTable('products')) {
            $featuredProducts = Product::query()
                ->with('category')
                ->active()
                ->where('featured', true)
                ->latest()
                ->take(4)
                ->get();

            $latestProducts = Product::query()
                ->with('category')
                ->active()
                ->latest()
                ->take(8)
                ->get();
        }

        return view('storefront.home', compact('categories', 'featuredProducts', 'latestProducts'));
    }
}
