<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->latest()
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'is_active' => true,
                'featured' => false,
                'stock' => 0,
            ]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::query()->create($request->validated());

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categories(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    private function categories()
    {
        return Category::query()->orderBy('name')->get();
    }
}
