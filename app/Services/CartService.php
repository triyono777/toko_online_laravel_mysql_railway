<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function current(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $user = Auth::user();

        if ($user instanceof Authenticatable) {
            $cartSessionIdBeforeLogin = $request->session()->pull('cart_session_id_before_login', $sessionId);

            return $this->resolveAuthenticatedCart(
                $user->getAuthIdentifier(),
                $sessionId,
                is_string($cartSessionIdBeforeLogin) ? $cartSessionIdBeforeLogin : $sessionId,
            );
        }

        return Cart::query()->firstOrCreate(
            [
                'user_id' => null,
                'session_id' => $sessionId,
            ],
            [
                'subtotal' => 0,
                'total_items' => 0,
            ]
        );
    }

    public function addProduct(Request $request, Product $product, int $quantity = 1): Cart
    {
        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk ini sedang tidak aktif.',
            ]);
        }

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk sedang habis.',
            ]);
        }

        $cart = $this->current($request);
        $existingItem = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existingItem?->quantity ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah melebihi stok yang tersedia.',
            ]);
        }

        $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => $newQuantity,
                'unit_price' => $product->price,
                'line_total' => $product->price * $newQuantity,
            ]
        );

        return $this->recalculate($cart);
    }

    public function updateItem(Request $request, CartItem $item, int $quantity): Cart
    {
        $cart = $this->current($request);
        $this->assertOwnership($cart, $item);

        $product = $item->product;

        if (! $product || ! $product->is_active) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk pada item keranjang ini tidak lagi tersedia.',
            ]);
        }

        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah melebihi stok yang tersedia.',
            ]);
        }

        $item->update([
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'line_total' => $product->price * $quantity,
        ]);

        return $this->recalculate($cart);
    }

    public function removeItem(Request $request, CartItem $item): Cart
    {
        $cart = $this->current($request);
        $this->assertOwnership($cart, $item);

        $item->delete();

        return $this->recalculate($cart);
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->forceFill([
            'subtotal' => 0,
            'total_items' => 0,
        ])->save();
    }

    public function recalculate(Cart $cart): Cart
    {
        $cart = $cart->fresh(['items.product']);

        $subtotal = (float) $cart->items->sum(fn (CartItem $item) => (float) $item->line_total);
        $totalItems = (int) $cart->items->sum('quantity');

        $cart->forceFill([
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
        ])->save();

        return $cart->fresh(['items.product']);
    }

    public function shippingOptions(): array
    {
        return [
            'jne' => [
                'regular' => 18000,
                'yes' => 32000,
            ],
            'sicepat' => [
                'best' => 20000,
                'express' => 35000,
            ],
            'anteraja' => [
                'reguler' => 17000,
                'nextday' => 30000,
            ],
        ];
    }

    public function shippingTotal(string $courier, string $service): int
    {
        return (int) ($this->shippingOptions()[$courier][$service] ?? 0);
    }

    private function resolveAuthenticatedCart(int|string $userId, string $sessionId, string $guestSessionId): Cart
    {
        $userCart = Cart::query()->firstOrCreate(
            ['user_id' => $userId],
            [
                'session_id' => $sessionId,
                'subtotal' => 0,
                'total_items' => 0,
            ]
        );

        if ($userCart->session_id !== $sessionId) {
            $userCart->forceFill([
                'session_id' => $sessionId,
            ])->save();
        }

        $guestCart = Cart::query()
            ->whereNull('user_id')
            ->where('session_id', $guestSessionId)
            ->with('items.product')
            ->first();

        if ($guestCart && $guestCart->id !== $userCart->id) {
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()->where('product_id', $guestItem->product_id)->first();
                $quantity = ($existingItem?->quantity ?? 0) + $guestItem->quantity;
                $stock = $guestItem->product?->stock ?? $quantity;
                $quantity = min($quantity, $stock);

                if ($quantity < 1) {
                    continue;
                }

                $userCart->items()->updateOrCreate(
                    ['product_id' => $guestItem->product_id],
                    [
                        'quantity' => $quantity,
                        'unit_price' => $guestItem->product?->price ?? $guestItem->unit_price,
                        'line_total' => ($guestItem->product?->price ?? $guestItem->unit_price) * $quantity,
                    ]
                );
            }

            $guestCart->items()->delete();
            $guestCart->delete();
        }

        return $this->recalculate($userCart);
    }

    private function assertOwnership(Cart $cart, CartItem $item): void
    {
        if ($item->cart_id !== $cart->id) {
            abort(404);
        }
    }
}
