<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Perlengkapan Outdoor',
            'slug' => '',
            'description' => 'Kategori baru untuk perlengkapan alam terbuka.',
            'sort_order' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::query()->firstOrFail();

        $this->assertSame('perlengkapan-outdoor', $category->slug);

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Perlengkapan Camping',
            'slug' => 'camping',
            'description' => 'Kategori yang diperbarui.',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Perlengkapan Camping',
            'slug' => 'camping',
            'is_active' => 0,
        ]);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_admin_cannot_delete_category_that_still_has_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::query()->create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'description' => 'Kategori elektronik.',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Speaker Mini',
            'slug' => 'speaker-mini',
            'sku' => 'SPK-001',
            'price' => 99000,
            'stock' => 5,
            'is_active' => true,
            'featured' => false,
        ]);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
