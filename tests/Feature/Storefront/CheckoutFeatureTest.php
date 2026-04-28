<?php

namespace Tests\Feature\Storefront;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_checkout(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_customer_can_checkout_and_create_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct([
            'price' => 149000,
            'stock' => 5,
        ]);

        $this->actingAs($customer)
            ->post(route('cart.items.store', $product), [
                'quantity' => 2,
            ])->assertRedirect();

        $this->actingAs($customer)
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Checkout')
            ->assertSee($product->name);

        $response = $this->actingAs($customer)->post('/checkout', [
            'customer_name' => 'Budi Customer',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jalan Mawar No. 10',
            'shipping_city' => 'Jakarta Selatan',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '12345',
            'courier' => 'jne',
            'service' => 'regular',
            'payment_method' => 'bank_transfer',
            'notes' => 'Tolong kirim siang hari.',
        ]);

        $order = Order::query()->firstOrFail();
        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-[A-Z0-9]{6}$/', $order->order_number);
        $this->assertSame('pending', $order->status);
        $this->assertEquals(316000.0, (float) $order->grand_total);

        $this->actingAs($customer)
            ->get(route('checkout.success', $order))
            ->assertOk()
            ->assertSee($order->order_number);
        $this->actingAs($customer)
            ->get(route('account.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);
        $this->actingAs($customer)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee($order->customer_name);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => 'bank_transfer',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'courier' => 'jne',
            'service' => 'regular',
            'status' => 'queued',
        ]);
        $this->assertDatabaseHas('addresses', [
            'user_id' => $customer->id,
            'label' => 'Utama',
            'city' => 'Jakarta Selatan',
            'is_default' => 1,
        ]);

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(0, $cart->fresh()->total_items);
    }

    public function test_checkout_rejects_invalid_shipping_service_combination(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct();

        $this->actingAs($customer)
            ->post(route('cart.items.store', $product), [
                'quantity' => 1,
            ])->assertRedirect();

        $this->from('/checkout')
            ->actingAs($customer)
            ->post('/checkout', [
                'customer_name' => 'Budi Customer',
                'customer_email' => 'budi@example.com',
                'customer_phone' => '081234567890',
                'shipping_address' => 'Jalan Melati No. 3',
                'shipping_city' => 'Bandung',
                'shipping_province' => 'Jawa Barat',
                'shipping_postal_code' => '40123',
                'courier' => 'jne',
                'service' => 'best',
                'payment_method' => 'bank_transfer',
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('service');

        $this->assertDatabaseCount('orders', 0);
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::query()->create([
            'name' => 'Kategori ' . Str::random(5),
            'slug' => 'kategori-' . Str::lower(Str::random(8)),
            'description' => 'Kategori test.',
            'is_active' => true,
        ]);

        return Product::query()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Produk ' . Str::random(5),
            'slug' => 'produk-' . Str::lower(Str::random(8)),
            'sku' => 'SKU-' . Str::upper(Str::random(6)),
            'excerpt' => 'Produk untuk testing checkout.',
            'description' => 'Produk untuk testing checkout.',
            'price' => 99000,
            'compare_price' => 125000,
            'stock' => 10,
            'weight' => 400,
            'is_active' => true,
            'featured' => false,
            'cover_image' => 'assets/img/elements/3.jpg',
        ], $overrides));
    }
}
