<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();

        $customerProfiles = $this->customerSeedData();
        $this->seedCustomers($customerProfiles);

        $customerEmails = array_column($customerProfiles, 'email');
        $customers = User::query()
            ->whereIn('email', $customerEmails)
            ->get()
            ->keyBy('email');

        $categories = $this->seedCategories($this->categorySeedData());

        Product::query()->whereIn('slug', [
            'elektronik-unggulan',
            'fashion-unggulan',
            'rumah-tangga-unggulan',
        ])->delete();

        $products = $this->seedProducts($categories, $this->productSeedData());

        $this->seedCoupons();
        $this->seedAddresses($customers, $customerProfiles);

        $customers = User::query()
            ->with('addresses')
            ->whereIn('email', $customerEmails)
            ->get()
            ->keyBy('email');

        $this->seedOrders($customers, $products);
        $this->seedCarts($customers, $products);
    }

    private function seedAdmin(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@tokoonline.test',
        ], [
            'name' => 'Store Admin',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => 'password',
        ]);
    }

    private function seedCustomers(array $profiles): void
    {
        foreach ($profiles as $profile) {
            User::query()->updateOrCreate([
                'email' => $profile['email'],
            ], [
                'name' => $profile['name'],
                'phone' => $profile['phone'],
                'role' => 'customer',
                'password' => 'password',
            ]);
        }
    }

    private function seedCategories(array $categories): Collection
    {
        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData + ['is_active' => true]
            );
        }

        return Category::query()
            ->whereIn('slug', array_column($categories, 'slug'))
            ->orderBy('sort_order')
            ->get()
            ->keyBy('slug');
    }

    private function seedProducts(Collection $categories, array $catalog): Collection
    {
        $images = [
            'assets/img/elements/1.jpg',
            'assets/img/elements/2.jpg',
            'assets/img/elements/3.jpg',
            'assets/img/elements/4.jpg',
            'assets/img/elements/5.jpg',
            'assets/img/elements/7.jpg',
            'assets/img/elements/11.jpg',
            'assets/img/elements/12.jpg',
            'assets/img/elements/17.jpg',
            'assets/img/elements/18.jpg',
            'assets/img/elements/19.jpg',
            'assets/img/elements/20.jpg',
        ];

        $position = 0;

        foreach ($catalog as $categorySlug => $items) {
            $category = $categories->get($categorySlug);

            if (! $category instanceof Category) {
                continue;
            }

            foreach ($items as $item) {
                $price = (int) $item['price'];

                Product::query()->updateOrCreate([
                    'sku' => $item['sku'],
                ], [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'excerpt' => $item['excerpt'],
                    'description' => $item['description'] ?? ($item['excerpt'] . ' Cocok untuk kebutuhan harian dengan kualitas yang siap dipakai.'),
                    'price' => $price,
                    'compare_price' => $item['compare_price'] ?? $this->comparePrice($price),
                    'stock' => $item['stock'],
                    'weight' => $item['weight'],
                    'is_active' => $item['is_active'] ?? true,
                    'featured' => $item['featured'] ?? false,
                    'cover_image' => $item['cover_image'] ?? $images[$position % count($images)],
                ]);

                $position++;
            }
        }

        $skus = collect($catalog)
            ->flatten(1)
            ->pluck('sku')
            ->all();

        return Product::query()
            ->whereIn('sku', $skus)
            ->get()
            ->keyBy('sku');
    }

    private function seedCoupons(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'description' => 'Potongan Rp10.000 untuk pembelian pertama.',
                'type' => 'fixed',
                'value' => 10000,
                'minimum_order' => 100000,
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(3),
                'usage_limit' => 250,
                'is_active' => true,
            ],
            [
                'code' => 'ONGKIR15',
                'description' => 'Potongan ongkir Rp15.000 untuk belanja minimum Rp200.000.',
                'type' => 'fixed',
                'value' => 15000,
                'minimum_order' => 200000,
                'starts_at' => now()->subWeeks(2),
                'expires_at' => now()->addMonths(2),
                'usage_limit' => 150,
                'is_active' => true,
            ],
            [
                'code' => 'BELANJA25',
                'description' => 'Diskon Rp25.000 untuk pembelian produk pilihan.',
                'type' => 'fixed',
                'value' => 25000,
                'minimum_order' => 300000,
                'starts_at' => now()->subWeek(),
                'expires_at' => now()->addMonth(),
                'usage_limit' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::query()->updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }

    private function seedAddresses(Collection $customers, array $profiles): void
    {
        foreach ($profiles as $profile) {
            $customer = $customers->get($profile['email']);

            if (! $customer instanceof User) {
                continue;
            }

            foreach ($profile['addresses'] as $index => $address) {
                $customer->addresses()->updateOrCreate([
                    'label' => $address['label'],
                ], [
                    'recipient_name' => $profile['name'],
                    'phone' => $profile['phone'],
                    'address_line' => $address['address_line'],
                    'city' => $address['city'],
                    'province' => $address['province'],
                    'postal_code' => $address['postal_code'],
                    'notes' => $address['notes'] ?? null,
                    'is_default' => $index === 0,
                ]);
            }
        }
    }

    private function seedOrders(Collection $customers, Collection $products): void
    {
        $cartService = app(CartService::class);

        foreach ($this->orderSeedData() as $orderData) {
            $customer = $customers->get($orderData['email']);

            if (! $customer instanceof User) {
                continue;
            }

            $address = $customer->addresses->firstWhere('label', $orderData['address_label'] ?? 'Rumah')
                ?? $customer->addresses->firstWhere('is_default', true)
                ?? $customer->addresses->first();

            if (! $address) {
                continue;
            }

            $placedAt = now()->subDays($orderData['days_ago'])->setTime($orderData['hour'], $orderData['minute']);
            $shippingTotal = $cartService->shippingTotal($orderData['courier'], $orderData['service']);
            $discountTotal = (int) ($orderData['discount_total'] ?? 0);
            $subtotal = collect($orderData['items'])->sum(function (array $item) use ($products): float {
                $product = $products->get($item['sku']);

                return $product instanceof Product
                    ? (float) $product->price * $item['quantity']
                    : 0;
            });

            $grandTotal = max($subtotal + $shippingTotal - $discountTotal, 0);

            $order = Order::query()->updateOrCreate([
                'order_number' => $orderData['order_number'],
            ], [
                'user_id' => $customer->id,
                'status' => $orderData['status'],
                'payment_status' => $orderData['payment_status'],
                'shipping_status' => $orderData['shipping_status'],
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'shipping_address' => $address->address_line,
                'shipping_city' => $address->city,
                'shipping_province' => $address->province,
                'shipping_postal_code' => $address->postal_code,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'grand_total' => $grandTotal,
                'notes' => $orderData['notes'] ?? null,
                'placed_at' => $placedAt,
            ]);

            $order->forceFill([
                'created_at' => $placedAt,
                'updated_at' => $placedAt->copy()->addMinutes(10),
            ])->save();

            $order->items()->delete();

            foreach ($orderData['items'] as $item) {
                $product = $products->get($item['sku']);

                if (! $product instanceof Product) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => (float) $product->price * $item['quantity'],
                ]);
            }

            Payment::query()->updateOrCreate([
                'order_id' => $order->id,
            ], [
                'method' => $orderData['payment_method'],
                'status' => $orderData['payment_status'],
                'transaction_reference' => $orderData['payment_status'] === 'paid'
                    ? 'PAY-' . Str::after($order->order_number, 'ORD-')
                    : null,
                'amount' => $grandTotal,
                'paid_at' => $orderData['payment_status'] === 'paid'
                    ? $placedAt->copy()->addHours(2)
                    : null,
            ]);

            Shipment::query()->updateOrCreate([
                'order_id' => $order->id,
            ], [
                'courier' => $orderData['courier'],
                'service' => $orderData['service'],
                'tracking_number' => in_array($orderData['shipping_status'], ['shipped', 'delivered'], true)
                    ? strtoupper($orderData['courier']) . '-' . Str::padLeft((string) $order->id, 8, '0')
                    : null,
                'status' => $orderData['shipping_status'],
                'shipped_at' => in_array($orderData['shipping_status'], ['packed', 'shipped', 'delivered'], true)
                    ? $placedAt->copy()->addDay()
                    : null,
                'delivered_at' => $orderData['shipping_status'] === 'delivered'
                    ? $placedAt->copy()->addDays(3)
                    : null,
            ]);
        }
    }

    private function seedCarts(Collection $customers, Collection $products): void
    {
        foreach ($this->cartSeedData() as $cartData) {
            $customer = $customers->get($cartData['email']);

            if (! $customer instanceof User) {
                continue;
            }

            $cart = Cart::query()->updateOrCreate([
                'user_id' => $customer->id,
            ], [
                'session_id' => 'seeded-cart-' . $customer->id,
                'subtotal' => 0,
                'total_items' => 0,
            ]);

            $this->syncCartItems($cart, $cartData['items'], $products);
        }
    }

    private function syncCartItems(Cart $cart, array $items, Collection $products): void
    {
        $productIds = [];
        $subtotal = 0;
        $totalItems = 0;

        foreach ($items as $item) {
            $product = $products->get($item['sku']);

            if (! $product instanceof Product) {
                continue;
            }

            $quantity = min($item['quantity'], $product->stock);

            if ($quantity < 1) {
                continue;
            }

            $lineTotal = (float) $product->price * $quantity;
            $productIds[] = $product->id;
            $subtotal += $lineTotal;
            $totalItems += $quantity;

            $cart->items()->updateOrCreate([
                'product_id' => $product->id,
            ], [
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'line_total' => $lineTotal,
            ]);
        }

        $cart->items()
            ->when($productIds !== [], fn ($query) => $query->whereNotIn('product_id', $productIds))
            ->when($productIds === [], fn ($query) => $query)
            ->delete();

        $cart->forceFill([
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
        ])->save();
    }

    private function comparePrice(int $price): int
    {
        return ((int) ceil(($price * 1.18) / 1000)) * 1000;
    }

    private function customerSeedData(): array
    {
        return [
            [
                'name' => 'Pelanggan Demo',
                'email' => 'customer@tokoonline.test',
                'phone' => '081298765432',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Melati Indah No. 12, Condongcatur',
                        'city' => 'Sleman',
                        'province' => 'DI Yogyakarta',
                        'postal_code' => '55281',
                        'notes' => 'Patokan dekat minimarket.',
                    ],
                    [
                        'label' => 'Kantor',
                        'address_line' => 'Jl. Affandi No. 45, Caturtunggal',
                        'city' => 'Sleman',
                        'province' => 'DI Yogyakarta',
                        'postal_code' => '55281',
                    ],
                ],
            ],
            [
                'name' => 'Bella Pratama',
                'email' => 'bella.pratama@tokoonline.test',
                'phone' => '081311223344',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Terusan Buah Batu No. 88',
                        'city' => 'Bandung',
                        'province' => 'Jawa Barat',
                        'postal_code' => '40287',
                    ],
                    [
                        'label' => 'Kantor',
                        'address_line' => 'Jl. Asia Afrika No. 102',
                        'city' => 'Bandung',
                        'province' => 'Jawa Barat',
                        'postal_code' => '40111',
                    ],
                ],
            ],
            [
                'name' => 'Andika Saputra',
                'email' => 'andika.saputra@tokoonline.test',
                'phone' => '081322334455',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Harapan Baru Raya Blok C7 No. 10',
                        'city' => 'Bekasi',
                        'province' => 'Jawa Barat',
                        'postal_code' => '17123',
                    ],
                ],
            ],
            [
                'name' => 'Rani Wulandari',
                'email' => 'rani.wulandari@tokoonline.test',
                'phone' => '081344556677',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Raya Darmo Permai No. 27',
                        'city' => 'Surabaya',
                        'province' => 'Jawa Timur',
                        'postal_code' => '60226',
                    ],
                ],
            ],
            [
                'name' => 'Dimas Pangestu',
                'email' => 'dimas.pangestu@tokoonline.test',
                'phone' => '081355667788',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Tlogosari Kulon No. 19',
                        'city' => 'Semarang',
                        'province' => 'Jawa Tengah',
                        'postal_code' => '50196',
                    ],
                ],
            ],
            [
                'name' => 'Salsa Anggraini',
                'email' => 'salsa.anggraini@tokoonline.test',
                'phone' => '081366778899',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Soekarno Hatta No. 14',
                        'city' => 'Malang',
                        'province' => 'Jawa Timur',
                        'postal_code' => '65141',
                    ],
                ],
            ],
            [
                'name' => 'Reza Maulana',
                'email' => 'reza.maulana@tokoonline.test',
                'phone' => '081377889900',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Boulevard Gading Serpong No. 61',
                        'city' => 'Tangerang',
                        'province' => 'Banten',
                        'postal_code' => '15810',
                    ],
                ],
            ],
            [
                'name' => 'Nabila Putri',
                'email' => 'nabila.putri@tokoonline.test',
                'phone' => '081388990011',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Margonda Raya No. 201',
                        'city' => 'Depok',
                        'province' => 'Jawa Barat',
                        'postal_code' => '16424',
                    ],
                ],
            ],
            [
                'name' => 'Fajar Hidayat',
                'email' => 'fajar.hidayat@tokoonline.test',
                'phone' => '081399001122',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. Pandu Raya No. 39',
                        'city' => 'Bogor',
                        'province' => 'Jawa Barat',
                        'postal_code' => '16152',
                    ],
                ],
            ],
            [
                'name' => 'Putri Ayunda',
                'email' => 'putri.ayunda@tokoonline.test',
                'phone' => '081300112233',
                'addresses' => [
                    [
                        'label' => 'Rumah',
                        'address_line' => 'Jl. AP Pettarani No. 77',
                        'city' => 'Makassar',
                        'province' => 'Sulawesi Selatan',
                        'postal_code' => '90222',
                    ],
                ],
            ],
        ];
    }

    private function categorySeedData(): array
    {
        return [
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Gadget, audio, dan aksesoris teknologi untuk kebutuhan harian.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Pilihan pakaian dan alas kaki untuk gaya kasual hingga aktif.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'description' => 'Perlengkapan fungsional untuk dapur, penyimpanan, dan kebutuhan rumah.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Produk kesehatan keluarga yang praktis dan mudah dipakai di rumah.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Peralatan olahraga ringan untuk aktivitas indoor maupun outdoor.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'description' => 'Produk konsumsi pilihan untuk stok camilan dan minuman favorit.',
                'sort_order' => 6,
            ],
        ];
    }

    private function productSeedData(): array
    {
        return [
            'elektronik' => [
                ['name' => 'Earbuds Nirkabel Pro', 'sku' => 'ELK-001', 'price' => 299000, 'stock' => 42, 'weight' => 200, 'featured' => true, 'excerpt' => 'Earbuds dengan suara jernih dan baterai tahan lama untuk mobilitas harian.'],
                ['name' => 'Speaker Bluetooth Mini', 'sku' => 'ELK-002', 'price' => 249000, 'stock' => 18, 'weight' => 350, 'featured' => true, 'excerpt' => 'Speaker ringkas dengan bass solid untuk meja kerja atau piknik santai.'],
                ['name' => 'Smartwatch Active Fit', 'sku' => 'ELK-003', 'price' => 459000, 'stock' => 14, 'weight' => 120, 'featured' => true, 'excerpt' => 'Jam pintar dengan pelacak langkah, tidur, dan notifikasi harian.'],
                ['name' => 'Power Bank FastCharge 20000', 'sku' => 'ELK-004', 'price' => 329000, 'stock' => 7, 'weight' => 450, 'excerpt' => 'Kapasitas besar dengan pengisian cepat untuk laptop ringan dan ponsel.'],
            ],
            'fashion' => [
                ['name' => 'Kemeja Oxford Pria', 'sku' => 'FSH-001', 'price' => 219000, 'stock' => 26, 'weight' => 300, 'excerpt' => 'Kemeja lengan panjang dengan bahan adem untuk kerja dan acara santai.'],
                ['name' => 'Blouse Linen Wanita', 'sku' => 'FSH-002', 'price' => 189000, 'stock' => 21, 'weight' => 220, 'excerpt' => 'Blouse ringan dengan potongan simpel yang nyaman dipakai seharian.'],
                ['name' => 'Sneakers Urban Flex', 'sku' => 'FSH-003', 'price' => 379000, 'stock' => 16, 'weight' => 700, 'featured' => true, 'excerpt' => 'Sneakers kasual dengan insole empuk untuk aktivitas harian yang padat.'],
                ['name' => 'Jaket Windbreaker Daily', 'sku' => 'FSH-004', 'price' => 289000, 'stock' => 8, 'weight' => 450, 'excerpt' => 'Jaket ringan tahan angin untuk perjalanan sore dan commuting harian.'],
            ],
            'rumah-tangga' => [
                ['name' => 'Blender Serbaguna 2L', 'sku' => 'RMT-001', 'price' => 429000, 'stock' => 11, 'weight' => 2500, 'featured' => true, 'excerpt' => 'Blender kapasitas besar untuk jus, bumbu, dan kebutuhan dapur keluarga.'],
                ['name' => 'Rak Susun Minimalis', 'sku' => 'RMT-002', 'price' => 179000, 'stock' => 6, 'weight' => 1800, 'excerpt' => 'Rak penyimpanan multifungsi untuk kamar, dapur, atau area kerja.'],
                ['name' => 'Set Panci Anti Lengket', 'sku' => 'RMT-003', 'price' => 339000, 'stock' => 17, 'weight' => 3200, 'excerpt' => 'Paket panci harian dengan lapisan anti lengket yang mudah dibersihkan.'],
                ['name' => 'Kotak Penyimpanan Serbaguna', 'sku' => 'RMT-004', 'price' => 99000, 'stock' => 30, 'weight' => 900, 'excerpt' => 'Storage box untuk merapikan perlengkapan rumah dan dokumen kecil.'],
            ],
            'kesehatan' => [
                ['name' => 'Tensimeter Digital Rumah', 'sku' => 'KHT-001', 'price' => 369000, 'stock' => 13, 'weight' => 500, 'featured' => true, 'excerpt' => 'Alat pengukur tekanan darah digital yang praktis untuk penggunaan keluarga.'],
                ['name' => 'Timbangan Badan Digital', 'sku' => 'KHT-002', 'price' => 199000, 'stock' => 19, 'weight' => 1400, 'excerpt' => 'Timbangan dengan layar digital jernih untuk memantau berat badan rutin.'],
                ['name' => 'Vitamin C 1000 mg', 'sku' => 'KHT-003', 'price' => 89000, 'stock' => 40, 'weight' => 100, 'excerpt' => 'Suplemen vitamin C untuk membantu menjaga daya tahan tubuh harian.'],
                ['name' => 'Masker Medis 50 pcs', 'sku' => 'KHT-004', 'price' => 59000, 'stock' => 10, 'weight' => 250, 'excerpt' => 'Masker medis sekali pakai untuk perlindungan saat beraktivitas di luar rumah.'],
            ],
            'olahraga' => [
                ['name' => 'Matras Yoga Flexi', 'sku' => 'OLG-001', 'price' => 159000, 'stock' => 24, 'weight' => 850, 'featured' => true, 'excerpt' => 'Matras empuk dengan grip baik untuk yoga, stretching, dan workout ringan.'],
                ['name' => 'Dumbbell Vinyl 2 kg', 'sku' => 'OLG-002', 'price' => 129000, 'stock' => 5, 'weight' => 2000, 'excerpt' => 'Dumbbell latihan dasar untuk pemula yang ingin olahraga dari rumah.'],
                ['name' => 'Botol Minum Sport 1L', 'sku' => 'OLG-003', 'price' => 79000, 'stock' => 33, 'weight' => 150, 'excerpt' => 'Botol minum anti bocor berkapasitas besar untuk gym dan aktivitas luar ruang.'],
                ['name' => 'Resistance Band Set', 'sku' => 'OLG-004', 'price' => 119000, 'stock' => 15, 'weight' => 300, 'excerpt' => 'Set resistance band dengan beberapa level tarikan untuk latihan fleksibel.'],
            ],
            'makanan-minuman' => [
                ['name' => 'Kopi Arabika Gayo 250 gr', 'sku' => 'MKN-001', 'price' => 98000, 'stock' => 27, 'weight' => 250, 'featured' => true, 'excerpt' => 'Biji kopi arabika dengan karakter floral dan aftertaste yang bersih.'],
                ['name' => 'Granola Madu Almond', 'sku' => 'MKN-002', 'price' => 67000, 'stock' => 22, 'weight' => 300, 'excerpt' => 'Granola renyah untuk sarapan praktis atau camilan sehat di sela aktivitas.'],
                ['name' => 'Teh Melati Premium', 'sku' => 'MKN-003', 'price' => 54000, 'stock' => 28, 'weight' => 200, 'excerpt' => 'Teh melati dengan aroma ringan yang cocok disajikan hangat atau dingin.'],
                ['name' => 'Sambal Roa Nusantara', 'sku' => 'MKN-004', 'price' => 45000, 'stock' => 31, 'weight' => 180, 'excerpt' => 'Sambal roa pedas gurih untuk pelengkap makan siang dan malam.'],
            ],
        ];
    }

    private function orderSeedData(): array
    {
        return [
            [
                'order_number' => 'ORD-DEMO-1001',
                'email' => 'customer@tokoonline.test',
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_status' => 'delivered',
                'courier' => 'jne',
                'service' => 'regular',
                'payment_method' => 'bank_transfer',
                'discount_total' => 15000,
                'days_ago' => 14,
                'hour' => 10,
                'minute' => 15,
                'items' => [
                    ['sku' => 'ELK-001', 'quantity' => 1],
                    ['sku' => 'MKN-001', 'quantity' => 2],
                ],
                'notes' => 'Mohon kemasan aman untuk produk elektronik.',
            ],
            [
                'order_number' => 'ORD-DEMO-1002',
                'email' => 'bella.pratama@tokoonline.test',
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_status' => 'shipped',
                'courier' => 'sicepat',
                'service' => 'best',
                'payment_method' => 'e_wallet',
                'days_ago' => 10,
                'hour' => 13,
                'minute' => 20,
                'address_label' => 'Kantor',
                'items' => [
                    ['sku' => 'FSH-003', 'quantity' => 1],
                    ['sku' => 'FSH-001', 'quantity' => 1],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1003',
                'email' => 'andika.saputra@tokoonline.test',
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'queued',
                'courier' => 'anteraja',
                'service' => 'reguler',
                'payment_method' => 'cod',
                'days_ago' => 2,
                'hour' => 9,
                'minute' => 10,
                'items' => [
                    ['sku' => 'ELK-004', 'quantity' => 1],
                    ['sku' => 'OLG-003', 'quantity' => 2],
                ],
                'notes' => 'Hubungi sebelum pengantaran.',
            ],
            [
                'order_number' => 'ORD-DEMO-1004',
                'email' => 'rani.wulandari@tokoonline.test',
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_status' => 'delivered',
                'courier' => 'jne',
                'service' => 'yes',
                'payment_method' => 'bank_transfer',
                'discount_total' => 25000,
                'days_ago' => 7,
                'hour' => 15,
                'minute' => 40,
                'items' => [
                    ['sku' => 'RMT-001', 'quantity' => 1],
                    ['sku' => 'RMT-003', 'quantity' => 1],
                    ['sku' => 'MKN-003', 'quantity' => 3],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1005',
                'email' => 'dimas.pangestu@tokoonline.test',
                'status' => 'cancelled',
                'payment_status' => 'pending',
                'shipping_status' => 'queued',
                'courier' => 'sicepat',
                'service' => 'express',
                'payment_method' => 'e_wallet',
                'days_ago' => 9,
                'hour' => 11,
                'minute' => 5,
                'items' => [
                    ['sku' => 'KHT-001', 'quantity' => 1],
                    ['sku' => 'KHT-004', 'quantity' => 2],
                ],
                'notes' => 'Order dibatalkan karena pembayaran belum diselesaikan.',
            ],
            [
                'order_number' => 'ORD-DEMO-1006',
                'email' => 'salsa.anggraini@tokoonline.test',
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_status' => 'packed',
                'courier' => 'anteraja',
                'service' => 'nextday',
                'payment_method' => 'e_wallet',
                'days_ago' => 1,
                'hour' => 16,
                'minute' => 30,
                'items' => [
                    ['sku' => 'FSH-002', 'quantity' => 2],
                    ['sku' => 'MKN-002', 'quantity' => 1],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1007',
                'email' => 'reza.maulana@tokoonline.test',
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_status' => 'delivered',
                'courier' => 'jne',
                'service' => 'regular',
                'payment_method' => 'bank_transfer',
                'days_ago' => 4,
                'hour' => 14,
                'minute' => 50,
                'items' => [
                    ['sku' => 'OLG-001', 'quantity' => 1],
                    ['sku' => 'OLG-004', 'quantity' => 1],
                    ['sku' => 'KHT-003', 'quantity' => 2],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1008',
                'email' => 'nabila.putri@tokoonline.test',
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'queued',
                'courier' => 'sicepat',
                'service' => 'best',
                'payment_method' => 'cod',
                'days_ago' => 0,
                'hour' => 9,
                'minute' => 45,
                'items' => [
                    ['sku' => 'FSH-004', 'quantity' => 1],
                    ['sku' => 'MKN-004', 'quantity' => 2],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1009',
                'email' => 'fajar.hidayat@tokoonline.test',
                'status' => 'completed',
                'payment_status' => 'paid',
                'shipping_status' => 'delivered',
                'courier' => 'anteraja',
                'service' => 'reguler',
                'payment_method' => 'bank_transfer',
                'days_ago' => 5,
                'hour' => 8,
                'minute' => 25,
                'items' => [
                    ['sku' => 'ELK-002', 'quantity' => 1],
                    ['sku' => 'RMT-004', 'quantity' => 2],
                ],
            ],
            [
                'order_number' => 'ORD-DEMO-1010',
                'email' => 'putri.ayunda@tokoonline.test',
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_status' => 'shipped',
                'courier' => 'jne',
                'service' => 'yes',
                'payment_method' => 'bank_transfer',
                'days_ago' => 3,
                'hour' => 12,
                'minute' => 55,
                'items' => [
                    ['sku' => 'KHT-002', 'quantity' => 1],
                    ['sku' => 'OLG-002', 'quantity' => 2],
                    ['sku' => 'MKN-001', 'quantity' => 1],
                ],
            ],
        ];
    }

    private function cartSeedData(): array
    {
        return [
            [
                'email' => 'customer@tokoonline.test',
                'items' => [
                    ['sku' => 'ELK-003', 'quantity' => 1],
                    ['sku' => 'MKN-002', 'quantity' => 1],
                ],
            ],
            [
                'email' => 'bella.pratama@tokoonline.test',
                'items' => [
                    ['sku' => 'FSH-002', 'quantity' => 1],
                    ['sku' => 'MKN-003', 'quantity' => 2],
                ],
            ],
            [
                'email' => 'andika.saputra@tokoonline.test',
                'items' => [
                    ['sku' => 'ELK-002', 'quantity' => 1],
                    ['sku' => 'OLG-004', 'quantity' => 1],
                ],
            ],
            [
                'email' => 'nabila.putri@tokoonline.test',
                'items' => [
                    ['sku' => 'KHT-003', 'quantity' => 2],
                ],
            ],
        ];
    }
}
