<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::query()->create([
            'name' => 'Aksesoris',
            'slug' => 'aksesoris',
            'description' => 'Kategori aksesoris.',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Mouse Wireless',
            'slug' => '',
            'sku' => 'mse-001',
            'excerpt' => 'Mouse ringkas untuk kerja harian.',
            'description' => 'Mouse wireless ergonomis.',
            'price' => 175000,
            'compare_price' => 199000,
            'stock' => 15,
            'weight' => 250,
            'cover_image' => 'assets/img/elements/4.png',
            'is_active' => '1',
            'featured' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::query()->firstOrFail();

        $this->assertSame('mouse-wireless', $product->slug);
        $this->assertSame('MSE-001', $product->sku);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Mouse Wireless Pro',
            'slug' => 'mouse-wireless-pro',
            'sku' => 'mse-010',
            'excerpt' => 'Versi terbaru.',
            'description' => 'Mouse wireless generasi baru.',
            'price' => 225000,
            'compare_price' => 250000,
            'stock' => 8,
            'weight' => 280,
            'cover_image' => 'assets/img/elements/5.jpg',
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mouse Wireless Pro',
            'slug' => 'mouse-wireless-pro',
            'sku' => 'MSE-010',
            'featured' => 0,
            'is_active' => 0,
        ]);

        $product->refresh();

        $this->actingAs($admin)->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_create_page_is_accessible_to_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Category::query()->create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Kategori fashion.',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Tambah Produk')
            ->assertSee('Pilih kategori');
    }
}
