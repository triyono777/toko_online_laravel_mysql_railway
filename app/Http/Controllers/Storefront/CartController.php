<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddToCartRequest;
use App\Http\Requests\Storefront\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function index(\Illuminate\Http\Request $request): View
    {
        $suggestedProducts = Product::query()
            ->active()
            ->latest()
            ->take(4)
            ->get();

        $cart = $this->cartService->current($request)->load('items.product.category');

        return view('storefront.cart.index', [
            'cart' => $cart,
            'cartItems' => $cart->items,
            'suggestedProducts' => $suggestedProducts,
            'shippingOptions' => $this->cartService->shippingOptions(),
        ]);
    }

    public function store(AddToCartRequest $request, Product $product): RedirectResponse
    {
        $this->cartService->addProduct($request, $product, (int) ($request->validated()['quantity'] ?? 1));

        return back()->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $this->cartService->updateItem($request, $cartItem, (int) $request->validated()['quantity']);

        return back()->with('status', 'Jumlah item keranjang diperbarui.');
    }

    public function destroy(\Illuminate\Http\Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeItem($request, $cartItem);

        return back()->with('status', 'Item dihapus dari keranjang.');
    }
}
