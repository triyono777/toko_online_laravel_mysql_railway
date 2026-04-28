# PRD Toko Online Berbasis Laravel dan MySQL

## 1. Ringkasan

Dokumen ini mendefinisikan kebutuhan produk untuk aplikasi toko online yang menjual produk fisik secara direct-to-consumer dengan stack implementasi utama:

- backend: `Laravel`
- database: `MySQL`
- database development saat ini: `tokoonline_db` pada MySQL dari `XAMPP`

Asumsi dasar:

- model bisnis: `single-vendor`
- platform awal: web responsive
- target rilis pertama: MVP untuk katalog, checkout, dan operasional admin dasar
- arsitektur awal: aplikasi monolith Laravel

## 2. Latar Belakang

Banyak UMKM dan brand lokal masih bergantung pada chat manual atau marketplace pihak ketiga untuk menjual produk. Hal ini membatasi kontrol atas branding, data pelanggan, promosi, dan pengalaman checkout.

Produk ini ditujukan untuk menyediakan kanal penjualan mandiri yang:

- memudahkan pelanggan menemukan dan membeli produk
- memudahkan admin mengelola katalog, pesanan, dan stok
- meningkatkan konversi dan repeat order

## 3. Tujuan Produk

### Tujuan bisnis

- meningkatkan penjualan langsung melalui website
- mengurangi proses manual pada pemesanan dan konfirmasi pembayaran
- membangun database pelanggan untuk retensi dan promosi

### Tujuan pengguna

- pelanggan bisa menemukan produk dengan cepat
- pelanggan bisa checkout dengan alur sederhana
- admin bisa memproses pesanan tanpa pencatatan manual yang berulang

## 4. Metrik Keberhasilan

### Metrik utama

- conversion rate pengunjung ke pembeli
- jumlah order per minggu
- average order value
- cart abandonment rate

### Metrik operasional

- waktu rata-rata dari order masuk ke status diproses
- akurasi stok
- persentase order yang selesai tanpa intervensi manual

## 5. Persona

### Persona 1: Pembeli cepat

- usia 20-35
- terbiasa belanja online lewat mobile
- ingin checkout cepat tanpa langkah berlebihan

### Persona 2: Pembeli teliti

- membandingkan produk berdasarkan harga, foto, dan ulasan
- butuh informasi produk yang jelas sebelum membeli

### Persona 3: Admin toko

- mengelola produk, stok, pesanan, dan promosi
- membutuhkan dashboard sederhana dan efisien

## 6. Problem Statement

### Pelanggan

- sulit menemukan produk yang relevan
- kurang yakin pada detail produk
- checkout terlalu panjang atau membingungkan

### Admin

- update stok manual rawan salah
- pelacakan pesanan tidak terpusat
- laporan penjualan tidak mudah dipantau

## 7. Ruang Lingkup MVP

### Fitur pelanggan

- registrasi, login, dan logout
- lihat katalog produk
- cari, filter, dan urutkan produk
- halaman detail produk
- tambah ke keranjang
- ubah jumlah item di keranjang
- checkout
- pilih alamat pengiriman
- pilih metode pengiriman
- pilih metode pembayaran
- lihat riwayat pesanan
- lihat status pesanan

### Fitur admin

- login admin
- dashboard ringkas penjualan
- CRUD kategori
- CRUD produk
- upload gambar produk
- pengelolaan stok
- daftar pesanan
- update status pesanan
- lihat data pelanggan
- manajemen promo sederhana dengan kode kupon

### Fitur sistem

- notifikasi email dasar untuk order dibuat
- invoice sederhana
- halaman kebijakan toko: pengiriman, pengembalian, privasi
- desain responsive untuk mobile dan desktop

## 8. Di Luar Scope MVP

- marketplace multi-vendor
- live chat real-time
- loyalty points
- wishlist
- review dan rating produk
- integrasi ERP
- multi-language
- native mobile app

## 9. User Flow Utama

### Flow pembelian

1. Pengguna membuka homepage.
2. Pengguna menelusuri kategori atau mencari produk.
3. Pengguna membuka detail produk.
4. Pengguna menambahkan produk ke keranjang.
5. Pengguna membuka keranjang dan menyesuaikan jumlah.
6. Pengguna login atau daftar saat checkout jika belum masuk.
7. Pengguna mengisi alamat pengiriman.
8. Pengguna memilih kurir dan metode pembayaran.
9. Pengguna meninjau ringkasan order.
10. Pengguna menyelesaikan checkout.
11. Sistem membuat order dan mengirim konfirmasi.

### Flow admin

1. Admin login ke dashboard.
2. Admin menambah atau mengubah produk dan stok.
3. Admin menerima order baru.
4. Admin memverifikasi pembayaran bila diperlukan.
5. Admin mengubah status order menjadi diproses, dikirim, dan selesai.

## 10. Kebutuhan Fungsional

### 10.1 Autentikasi

- pengguna dapat daftar dengan nama, email, nomor telepon, dan password
- pengguna dapat login dengan email dan password
- pengguna dapat reset password melalui email
- admin memiliki akses terpisah dari pelanggan

### 10.2 Katalog produk

- sistem menampilkan daftar produk dengan nama, harga, foto, dan status stok
- produk dikelompokkan berdasarkan kategori
- pengguna dapat mencari produk berdasarkan kata kunci
- pengguna dapat filter berdasarkan kategori, harga, dan ketersediaan stok
- pengguna dapat mengurutkan berdasarkan terbaru, harga terendah, harga tertinggi, dan terlaris

### 10.3 Detail produk

- halaman detail menampilkan galeri foto, nama produk, harga, deskripsi, stok, variasi bila ada, dan tombol tambah ke keranjang
- sistem menampilkan produk terkait

### 10.4 Keranjang

- pengguna dapat menambah item ke keranjang
- pengguna dapat menghapus item dari keranjang
- pengguna dapat mengubah kuantitas
- sistem menghitung subtotal secara otomatis

### 10.5 Checkout

- pengguna dapat memilih atau menambah alamat
- sistem menghitung ongkir berdasarkan metode pengiriman
- pengguna dapat memasukkan kode promo
- sistem menampilkan ringkasan pembayaran: subtotal, diskon, ongkir, total
- order tidak dapat dibuat jika stok tidak mencukupi

### 10.6 Pembayaran

- sistem mendukung minimal transfer bank dan payment gateway
- sistem menyimpan status pembayaran: menunggu pembayaran, dibayar, gagal, dikembalikan
- admin dapat melihat detail pembayaran pada order

### 10.7 Pesanan

- sistem membuat nomor order unik
- pelanggan dapat melihat riwayat order dan statusnya
- admin dapat mengubah status order: baru, dibayar, diproses, dikirim, selesai, dibatalkan
- sistem menyimpan timestamp untuk setiap perubahan status penting

### 10.8 Admin produk dan stok

- admin dapat membuat, mengubah, menghapus, dan menyembunyikan produk
- admin dapat mengatur SKU, harga, diskon, berat, stok, dan foto
- stok berkurang ketika order berhasil dibayar atau sesuai aturan bisnis yang dipilih

### 10.9 Promo

- admin dapat membuat kode kupon
- kupon dapat dibatasi berdasarkan masa berlaku, minimum pembelian, dan kuota penggunaan

### 10.10 Laporan dasar

- dashboard admin menampilkan total penjualan, jumlah order, produk terlaris, dan order terbaru

## 11. Kebutuhan Non-Fungsional

- performa halaman utama dan katalog tetap nyaman di koneksi mobile
- UI responsive untuk ukuran mobile, tablet, dan desktop
- keamanan dasar: hashing password, CSRF protection, validasi input, otorisasi role
- ketersediaan log aktivitas untuk error penting dan perubahan status order
- SEO dasar untuk halaman produk dan kategori
- seluruh fitur inti berjalan stabil pada Laravel LTS atau versi Laravel aktif yang dipilih tim
- desain schema dan query harus kompatibel dengan MySQL 8+

## 12. Batasan Teknis

- backend wajib menggunakan Laravel
- database relasional utama wajib menggunakan MySQL
- environment database lokal saat ini menggunakan MySQL dari XAMPP
- nama database development yang dipakai saat ini adalah `tokoonline_db`
- autentikasi menggunakan mekanisme Laravel yang standar dan maintainable
- migrasi database harus dikelola melalui Laravel migrations
- operasi background seperti email dan notifikasi sebaiknya memakai Laravel queue
- seluruh akses database aplikasi dilakukan melalui layer Laravel, bukan query manual yang tersebar tanpa kontrol
- detail koneksi seperti `DB_HOST`, `DB_PORT`, `DB_USERNAME`, dan `DB_PASSWORD` harus dikelola melalui file `.env` sesuai instalasi XAMPP lokal

## 13. Kebutuhan Data Inti

Minimal entitas data yang perlu tersedia:

- users
- roles
- addresses
- categories
- products
- product_images
- product_variants jika variasi diaktifkan
- carts
- cart_items
- orders
- order_items
- payments
- shipments
- coupons

Catatan desain awal:

- setiap produk memiliki SKU unik
- setiap order memiliki nomor order unik
- relasi order ke item bersifat snapshot agar histori harga tetap konsisten
- indeks MySQL perlu diprioritaskan pada kolom pencarian dan relasi seperti `email`, `sku`, `category_id`, `order_number`, dan `status`

## 14. Aturan Bisnis

- satu order hanya bisa diproses jika data pelanggan, alamat, dan item valid
- stok tidak boleh menjadi negatif
- kupon tidak dapat digunakan di luar masa aktif
- order yang dibatalkan tidak dihitung sebagai penjualan selesai
- jika pembayaran belum selesai dalam batas waktu tertentu, order dapat diubah menjadi kadaluarsa atau dibatalkan

## 15. Dependensi

- payment gateway
- layanan email/SMTP
- kurir atau integrasi ongkir
- media storage untuk gambar produk

## 16. Risiko

- sinkronisasi stok salah saat order tinggi
- integrasi pembayaran menambah kompleksitas QA
- admin tetap memakai proses manual di luar sistem jika dashboard terlalu rumit
- performa katalog menurun jika optimasi gambar dan query diabaikan

## 17. Tahapan Rilis

### Phase 1: MVP

- autentikasi pelanggan dan admin
- katalog, detail produk, keranjang, checkout
- order management dasar
- pembayaran dasar
- dashboard admin sederhana

### Phase 2

- wishlist
- review produk
- notifikasi WhatsApp
- promo yang lebih fleksibel
- laporan penjualan lebih detail

### Phase 3

- loyalty program
- rekomendasi produk
- aplikasi mobile

## 18. Open Questions

- apakah toko hanya menjual produk fisik atau juga digital
- apakah variasi produk seperti ukuran dan warna wajib di MVP
- kapan stok dianggap berkurang: saat checkout atau saat pembayaran sukses
- payment gateway apa yang akan dipakai
- apakah ongkir dihitung dari API pihak ketiga atau flat rate
- apakah ada kebutuhan role tambahan selain admin dan pelanggan

## 19. Acceptance Criteria MVP

- pengguna dapat menyelesaikan checkout end-to-end tanpa bantuan admin
- admin dapat menambah produk dan stok dari dashboard
- admin dapat melihat order baru dan mengubah statusnya
- sistem menghitung total belanja, diskon, dan ongkir dengan benar
- halaman utama, katalog, detail produk, keranjang, checkout, dan dashboard admin usable di mobile
- migrasi database Laravel dapat dijalankan bersih di MySQL tanpa error
- relasi inti order, payment, shipment, product, dan user tervalidasi di database

## 20. Rekomendasi Teknis Awal

- backend: Laravel dengan Blade untuk storefront dan dashboard admin
- database: MySQL 8+
- database lokal awal: `tokoonline_db` di MySQL XAMPP
- admin UI: Laravel Blade dengan admin theme bila dibutuhkan
- pembayaran: gateway lokal yang mendukung VA dan e-wallet
- deployment awal: single Laravel app dengan queue untuk email dan notifikasi
