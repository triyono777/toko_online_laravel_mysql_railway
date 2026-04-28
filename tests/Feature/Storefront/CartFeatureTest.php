<?php

namespace Tests\Feature\Storefront;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_update_and_remove_items_from_cart(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct([
            'price' => 149000,
            'stock' => 10,
        ]);

        $this->actingAs($customer)->post(route('cart.items.store', $product), [
            'quantity' => 2,
        ])->assertRedirect();

        $cart = Cart::query()->where('user_id', $customer->id)->firstOrFail();
        $cartItem = $cart->items()->firstOrFail();

        $this->actingAs($customer)
            ->get('/cart')
            ->assertOk()
            ->assertSee($product->name);

        $this->assertSame(2, $cartItem->quantity);
        $this->assertEquals(298000.0, (float) $cart->fresh()->subtotal);

        $this->actingAs($customer)->patch(route('cart.items.update', $cartItem), [
            'quantity' => 3,
        ])->assertRedirect();

        $this->assertSame(3, $cartItem->fresh()->quantity);
        $this->assertEquals(447000.0, (float) $cart->fresh()->subtotal);

        $this->actingAs($customer)->delete(route('cart.items.destroy', $cartItem))->assertRedirect();

        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'total_items' => 0,
        ]);
    }

    public function test_cart_service_merges_guest_cart_after_login_handoff(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = $this->createProduct();
        $guestCart = Cart::query()->create([
            'session_id' => 'guest-merge-session',
            'subtotal' => 125000,
            'total_items' => 1,
        ]);

        $guestCart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'line_total' => $product->price,
        ]);

        $request = Request::create('/cart', 'GET');
        $session = app('session')->driver();
        $session->start();
        $session->setId('customer-session-after-login');
        $session->put('cart_session_id_before_login', 'guest-merge-session');
        $request->setLaravelSession($session);

        Auth::login($customer);

        $cart = app(CartService::class)->current($request);

        Auth::logout();

        $this->assertSame($customer->id, $cart->user_id);
        $this->assertSame(1, $cart->total_items);
        $this->assertDatabaseMissing('carts', [
            'id' => $guestCart->id,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
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
            'excerpt' => 'Produk untuk testing cart.',
            'description' => 'Produk untuk testing cart dan checkout.',
            'price' => 125000,
            'compare_price' => 150000,
            'stock' => 12,
            'weight' => 500,
            'is_active' => true,
            'featured' => false,
            'cover_image' => 'assets/img/elements/1.jpg',
        ], $overrides));
    }
}
