<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@tokoonline.test',
        ], [
            'name' => 'Store Admin',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => 'password',
        ]);

        User::query()->updateOrCreate([
            'email' => 'customer@tokoonline.test',
        ], [
            'name' => 'Pelanggan Demo',
            'phone' => '081298765432',
            'role' => 'customer',
            'password' => 'password',
        ]);

        $categories = [
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Gadget, audio, dan aksesoris harian.',
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Produk fashion kasual untuk pria dan wanita.',
            ],
            [
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'description' => 'Peralatan fungsional untuk kebutuhan rumah.',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData + ['is_active' => true]
            );

            Product::query()->updateOrCreate([
                'slug' => $category->slug . '-unggulan',
            ], [
                'category_id' => $category->id,
                'name' => 'Produk Unggulan ' . $category->name,
                'sku' => Str::upper(Str::slug($category->name)) . '-001',
                'excerpt' => 'Produk unggulan untuk peluncuran awal toko online.',
                'description' => 'Contoh produk awal untuk membantu proses demo katalog, dashboard admin, dan halaman detail produk.',
                'price' => 149000,
                'compare_price' => 199000,
                'stock' => 24,
                'weight' => 500,
                'is_active' => true,
                'featured' => true,
                'cover_image' => 'assets/img/elements/12.png',
            ]);
        }
    }
}
