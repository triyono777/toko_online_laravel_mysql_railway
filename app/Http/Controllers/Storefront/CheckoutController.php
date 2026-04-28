<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
    ) {
    }

    public function create(\Illuminate\Http\Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->current($request)->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $user = $request->user();
        $defaultAddress = $user?->addresses()->where('is_default', true)->first();

        return view('storefront.checkout.create', [
            'cart' => $cart,
            'user' => $user,
            'defaultAddress' => $defaultAddress,
            'shippingOptions' => $this->cartService->shippingOptions(),
            'paymentMethods' => $this->checkoutService->paymentMethods(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->current($request)->load('items.product', 'user');
        $order = $this->checkoutService->createOrder($cart, $request->validated());

        return redirect()->route('checkout.success', $order)->with('status', 'Checkout berhasil. Pesanan Anda sudah dibuat.');
    }

    public function success(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 404);

        return view('storefront.checkout.success', [
            'order' => $order->load('items', 'payment', 'shipment'),
        ]);
    }
}
