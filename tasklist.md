# Task List Toko Online

Dokumen ini merangkum task implementasi berdasarkan PRD dan status project Laravel saat ini.

## 1. Fondasi Project

- [x] Inisialisasi aplikasi Laravel di workspace
- [x] Integrasi tema Sneat untuk admin panel
- [x] Konfigurasi `.env.example` untuk MySQL `tokoonline_db`
- [x] Install dependency Composer dan NPM
- [x] Generate app key
- [x] Setup Vite build agar sukses
- [x] Jalankan migrasi dan seeder awal
- [x] Commit setup awal Laravel + tema

## 2. Database dan Domain Model

- [x] Tambah field `phone` dan `role` pada `users`
- [x] Buat migration `categories`
- [x] Buat migration `products`
- [x] Buat migration `addresses`
- [x] Buat migration `carts` dan `cart_items`
- [x] Buat migration `orders` dan `order_items`
- [x] Buat migration `payments`
- [x] Buat migration `shipments`
- [x] Buat migration `coupons`
- [x] Buat model Eloquent untuk entitas inti
- [ ] Tambah tabel `product_images`
- [ ] Tambah tabel `product_variants` bila variasi produk dibutuhkan
- [ ] Tambah tabel audit/log status order

## 3. Seeder dan Data Awal

- [x] Seed admin demo
- [x] Seed customer demo
- [x] Seed kategori awal
- [x] Seed produk contoh
- [ ] Seed order contoh
- [ ] Seed payment dan shipment contoh
- [ ] Seed coupon contoh

## 4. Storefront

- [x] Halaman beranda toko
- [x] Halaman katalog produk
- [x] Halaman detail produk
- [x] Halaman keranjang sebagai placeholder
- [ ] Implementasi tambah ke keranjang
- [ ] Implementasi update quantity keranjang
- [ ] Implementasi hapus item keranjang
- [ ] Implementasi pencarian yang lebih lengkap
- [ ] Implementasi filter kategori, harga, dan stok
- [ ] Implementasi sort produk
- [ ] Tambah halaman riwayat pesanan pelanggan
- [ ] Tambah halaman detail pesanan pelanggan

## 5. Checkout dan Order Flow

- [ ] Implementasi checkout form
- [ ] Implementasi alamat pengiriman pelanggan
- [ ] Validasi stok saat checkout
- [ ] Generate `order_number` otomatis
- [ ] Simpan snapshot item ke `order_items`
- [ ] Hitung subtotal, diskon, ongkir, dan grand total
- [ ] Simpan order ke database dari flow checkout
- [ ] Redirect ke halaman sukses checkout

## 6. Pembayaran dan Pengiriman

- [ ] Tambah pilihan metode pembayaran
- [ ] Integrasi payment gateway
- [ ] Simpan `transaction_reference`
- [ ] Update `payment_status` dari callback/webhook
- [ ] Tambah pilihan kurir dan layanan pengiriman
- [ ] Simpan data shipment dan nomor resi
- [ ] Update `shipping_status`

## 7. Autentikasi dan Otorisasi

- [x] Implementasi login customer
- [x] Implementasi registrasi customer
- [x] Implementasi logout customer
- [x] Implementasi reset password
- [x] Implementasi login admin
- [ ] Pisahkan middleware admin dan customer
- [x] Lindungi route admin
- [x] Batasi akses CRUD hanya untuk admin

## 8. Admin Panel

- [x] Dashboard admin awal
- [x] Listing kategori
- [x] Listing produk
- [x] Listing pesanan
- [x] Listing pelanggan
- [ ] CRUD kategori
- [ ] CRUD produk
- [ ] Upload gambar produk
- [ ] Manajemen stok dari admin
- [ ] Detail pesanan admin
- [ ] Update status pesanan
- [ ] Dashboard metrik penjualan real
- [ ] Manajemen kupon promo

## 9. UX dan Konten

- [x] Ganti branding default Sneat menjadi toko online
- [x] Ganti menu demo menjadi menu toko online
- [ ] Rapikan copywriting demo yang masih generik
- [ ] Tambah empty state yang lebih baik
- [x] Tambah flash message sukses/gagal
- [ ] Tambah pagination pada listing admin dan katalog
- [ ] Optimasi mobile layout untuk checkout dan admin

## 10. Sistem dan Integrasi

- [ ] Konfigurasi mailer nyata selain `log`
- [ ] Kirim email konfirmasi order
- [ ] Tambah invoice sederhana
- [ ] Tambah halaman kebijakan pengiriman
- [ ] Tambah halaman pengembalian
- [ ] Tambah halaman privasi
- [ ] Tambah queue job untuk email/notifikasi

## 11. Testing

- [x] Smoke test default Laravel lulus
- [ ] Tambah feature test storefront
- [x] Tambah feature test admin
- [ ] Tambah test checkout
- [ ] Tambah test migration/schema penting
- [x] Tambah test otorisasi admin/customer

## 12. Cleanup Teknis

- [ ] Hapus controller dan view demo Sneat yang tidak dipakai
- [ ] Rapikan asset build agar hanya memuat file yang dibutuhkan
- [ ] Kurangi chunk Vite yang terlalu besar
- [ ] Rapikan `package.json` dari dependency demo yang tidak diperlukan
- [ ] Tambah README project khusus toko online

## 13. Prioritas Berikutnya

1. Implementasi autentikasi admin dan customer.
2. Implementasi keranjang yang benar-benar tersimpan.
3. Implementasi checkout sampai order tercatat di database.
4. Implementasi CRUD kategori dan produk di admin.
5. Integrasi pembayaran dan pembaruan status order.
