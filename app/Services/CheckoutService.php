<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function paymentMethods(): array
    {
        return [
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'cod' => 'Bayar di Tempat',
        ];
    }

    public function createOrder(Cart $cart, array $payload): Order
    {
        $cart->loadMissing('items.product', 'user');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $shippingOptions = $this->cartService->shippingOptions();

        if (! isset($shippingOptions[$payload['courier']][$payload['service']])) {
            throw ValidationException::withMessages([
                'service' => 'Layanan pengiriman tidak valid.',
            ]);
        }

        if (! array_key_exists($payload['payment_method'], $this->paymentMethods())) {
            throw ValidationException::withMessages([
                'payment_method' => 'Metode pembayaran tidak valid.',
            ]);
        }

        return DB::transaction(function () use ($cart, $payload) {
            foreach ($cart->items as $item) {
                $product = $item->product;

                if (! $product instanceof Product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Ada produk di keranjang yang tidak lagi tersedia.',
                    ]);
                }

                if ($item->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok untuk {$product->name} tidak mencukupi.",
                    ]);
                }
            }

            $shippingTotal = $this->cartService->shippingTotal($payload['courier'], $payload['service']);
            $subtotal = (float) $cart->subtotal;
            $discountTotal = 0;
            $grandTotal = $subtotal + $shippingTotal - $discountTotal;

            $order = Order::query()->create([
                'user_id' => $cart->user_id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'queued',
                'customer_name' => $payload['customer_name'],
                'customer_email' => $payload['customer_email'],
                'customer_phone' => $payload['customer_phone'],
                'shipping_address' => $payload['shipping_address'],
                'shipping_city' => $payload['shipping_city'],
                'shipping_province' => $payload['shipping_province'],
                'shipping_postal_code' => $payload['shipping_postal_code'],
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'grand_total' => $grandTotal,
                'notes' => $payload['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $order->payment()->create([
                'method' => $payload['payment_method'],
                'status' => 'pending',
                'amount' => $grandTotal,
            ]);

            $order->shipment()->create([
                'courier' => $payload['courier'],
                'service' => $payload['service'],
                'status' => 'queued',
            ]);

            if ($cart->user_id) {
                $cart->user?->addresses()->updateOrCreate(
                    [
                        'label' => 'Utama',
                    ],
                    [
                        'recipient_name' => $payload['customer_name'],
                        'phone' => $payload['customer_phone'],
                        'address_line' => $payload['shipping_address'],
                        'city' => $payload['shipping_city'],
                        'province' => $payload['shipping_province'],
                        'postal_code' => $payload['shipping_postal_code'],
                        'is_default' => true,
                    ]
                );
            }

            $this->cartService->clear($cart);

            return $order->fresh(['items', 'payment', 'shipment']);
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
