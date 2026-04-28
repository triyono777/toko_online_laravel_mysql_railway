<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = collect();
        $products = collect();

        if (Schema::hasTable('categories')) {
            $categories = Category::query()->active()->orderBy('name')->get();
        }

        if (Schema::hasTable('products')) {
            $products = Product::query()
                ->with('category')
                ->active()
                ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                    $search = $request->string('search')->toString();

                    $query->where(function ($innerQuery) use ($search) {
                        $innerQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%')
                            ->orWhere('excerpt', 'like', '%' . $search . '%');
                    });
                })
                ->when($request->filled('category'), function ($query) use ($request) {
                    $query->whereHas('category', function ($categoryQuery) use ($request) {
                        $categoryQuery->where('slug', $request->string('category')->toString());
                    });
                })
                ->latest()
                ->get();
        }

        return view('storefront.products.index', compact('categories', 'products'));
    }

    public function show(Product $product): View
    {
        $relatedProducts = collect();

        if (Schema::hasTable('products')) {
            $relatedProducts = Product::query()
                ->with('category')
                ->active()
                ->where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->take(4)
                ->get();
        }

        return view('storefront.products.show', compact('product', 'relatedProducts'));
    }
}
