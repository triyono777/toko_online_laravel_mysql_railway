# Toko Online Laravel

Project ini adalah aplikasi toko online berbasis Laravel 12, MySQL, dan tema Sneat untuk admin panel. Sampai tahap saat ini, aplikasi sudah memiliki storefront, autentikasi customer dan admin, keranjang tersimpan, checkout, riwayat pesanan customer, serta CRUD kategori dan produk di admin.

## Stack

- PHP 8.2
- Laravel 12
- MySQL
- Vite
- Sneat admin theme
- Railway CLI untuk deployment

## Demo Domain

- Demo Railway: [https://web-production-b59ff6.up.railway.app/](https://web-production-b59ff6.up.railway.app/)

## Sumber Template

- Template dasar admin yang dipakai di project ini berasal dari Sneat HTML Free:
  [https://github.com/themeselection/sneat-bootstrap-html-admin-template-free](https://github.com/themeselection/sneat-bootstrap-html-admin-template-free)
- Integrasi Laravel pada project ini kemudian disesuaikan lagi untuk kebutuhan toko online, termasuk storefront, autentikasi, cart, checkout, dan admin CRUD.

## Fitur Saat Ini

- Storefront: beranda, katalog, detail produk
- Autentikasi: login/register customer, login admin, reset password
- Cart: tambah, update quantity, hapus item
- Checkout: alamat pengiriman, kurir, metode pembayaran, pembuatan order
- Customer area: riwayat pesanan dan detail pesanan
- Admin: dashboard awal, listing pesanan/pelanggan, CRUD kategori, CRUD produk

## Akun Demo

- Admin
  - Email: `admin@tokoonline.test`
  - Password: `password`
- Customer
  - Email: `customer@tokoonline.test`
  - Password: `password`

## Clone Repository

Repository GitHub project ini:

- [https://github.com/triyono777/toko_online_laravel_mysql_railway](https://github.com/triyono777/toko_online_laravel_mysql_railway)

Clone project lalu masuk ke folder kerja:

```bash
git clone https://github.com/triyono777/toko_online_laravel_mysql_railway.git
cd toko_online_laravel_mysql_railway
```

## Menjalankan Lokal

1. Clone repository lalu masuk ke folder project.

```bash
git clone https://github.com/triyono777/toko_online_laravel_mysql_railway.git
cd toko_online_laravel_mysql_railway
```

2. Install dependency backend.

```bash
composer install
```

3. Install dependency frontend.

```bash
npm install
```

4. Buat file environment dari contoh.

```bash
cp .env.example .env
```

5. Generate app key.

```bash
php artisan key:generate
```

6. Sesuaikan koneksi database lokal XAMPP di `.env`.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tokoonline_db
DB_USERNAME=root
DB_PASSWORD=
```

7. Jalankan migrasi dan seeder.

```bash
php artisan migrate --seed
```

8. Build asset frontend.

```bash
npm run build
```

9. Jalankan aplikasi.

```bash
php artisan serve
```

## Deploy ke Railway Dengan Railway CLI

Bagian ini mengikuti proses yang sudah dipakai pada project ini.

### 1. Prasyarat

- Sudah install Railway CLI
- Sudah login Railway
- Sudah berada di root project

Cek cepat:

```bash
railway --version
railway whoami
```

### 2. Konfigurasi Deploy di Repo

Project ini memakai [railway.json](/Users/triyono/Projek/web%203/toko_online/railway.json) agar Railway:

- build asset dengan `npm run build`
- menjalankan aplikasi dengan `php artisan serve` pada port Railway
- menjalankan healthcheck di `/up`
- otomatis menjalankan `php artisan migrate --force` saat env database sudah lengkap

### 3. Buat Project Railway

```bash
railway init -n toko-online-laravel
```

Project Railway yang dipakai saat ini:

- Project name: `toko-online-laravel`
- Project URL: [https://railway.com/project/ecf539de-f0a0-4a0a-a71f-d81205cbac5f](https://railway.com/project/ecf539de-f0a0-4a0a-a71f-d81205cbac5f)

### 4. Tambahkan Service Database dan App

Tambahkan MySQL:

```bash
railway add -d mysql
```

Tambahkan service aplikasi:

```bash
railway add -s web
```

Setelah itu, pastikan service app yang aktif adalah `web`:

```bash
railway service status
```

### 5. Set Variable Aplikasi

Generate key Laravel:

```bash
php artisan key:generate --show
```

Lalu set variable ke service `web`. Contoh yang dipakai untuk deploy ini:

```bash
railway variable set -s web --skip-deploys \
  APP_NAME='Toko Online' \
  APP_ENV=production \
  APP_KEY='base64:YOUR_APP_KEY' \
  APP_DEBUG=false \
  APP_URL='https://${{RAILWAY_PUBLIC_DOMAIN}}' \
  APP_LOCALE=id \
  APP_FALLBACK_LOCALE=id \
  APP_FAKER_LOCALE=id_ID \
  APP_MAINTENANCE_DRIVER=file \
  LOG_CHANNEL=stderr \
  LOG_LEVEL=info \
  LOG_STDERR_FORMATTER='\\Monolog\\Formatter\\JsonFormatter' \
  DB_CONNECTION=mysql \
  DB_HOST='${{MySQL.MYSQLHOST}}' \
  DB_PORT='${{MySQL.MYSQLPORT}}' \
  DB_DATABASE='${{MySQL.MYSQLDATABASE}}' \
  DB_USERNAME='${{MySQL.MYSQLUSER}}' \
  DB_PASSWORD='${{MySQL.MYSQLPASSWORD}}' \
  SESSION_DRIVER=database \
  SESSION_LIFETIME=120 \
  SESSION_ENCRYPT=false \
  SESSION_PATH=/ \
  CACHE_STORE=database \
  QUEUE_CONNECTION=sync \
  BROADCAST_CONNECTION=log \
  FILESYSTEM_DISK=local \
  MAIL_MAILER=log \
  MAIL_FROM_ADDRESS='admin@tokoonline.test' \
  MAIL_FROM_NAME='Toko Online' \
  VITE_APP_NAME='Toko Online'
```

Catatan:

- `DB_*` memakai reference ke service `MySQL`, bukan hardcode password lokal.
- `QUEUE_CONNECTION=sync` dipilih untuk tahap ini karena worker terpisah belum dibuat di Railway.
- `APP_URL` akan benar setelah public domain dibuat.

### 6. Deploy Aplikasi

Deploy source code dari folder lokal ini:

```bash
railway up -s web -c
```

Flag `-c` dipakai untuk mode CI agar proses deploy tampil langsung di terminal.

### 7. Buat Domain Publik

```bash
railway domain
```

Domain publik yang dibuat untuk tahap ini:

- [https://web-production-b59ff6.up.railway.app](https://web-production-b59ff6.up.railway.app)

### 8. Jalankan Seeder Demo Setelah Deploy Pertama

Karena data demo tidak dijalankan otomatis di setiap deploy, jalankan seed sekali setelah aplikasi dan database siap.

Jika Anda menjalankan dari laptop dengan `railway run`, gunakan host public proxy MySQL Railway, karena `mysql.railway.internal` hanya bisa di-resolve dari dalam jaringan Railway:

```bash
railway run -s web env \
  DB_HOST=switchback.proxy.rlwy.net \
  DB_PORT=41450 \
  php artisan db:seed --force
```

Jika perlu memastikan migrasi lagi:

```bash
railway run -s web env \
  DB_HOST=switchback.proxy.rlwy.net \
  DB_PORT=41450 \
  php artisan migrate --force
```

Jika nanti Anda sudah menyiapkan SSH key Railway, Anda juga bisa menjalankan command langsung di container dengan:

```bash
railway ssh -s web php artisan db:seed --force
```

### 9. Verifikasi Deploy

Periksa status service:

```bash
railway service status -s web
railway service status -s MySQL
```

Periksa log aplikasi:

```bash
railway logs -s web
```

Periksa variable service:

```bash
railway variable list -s web -k
```

## Struktur Deploy Railway Saat Ini

- Project: `toko-online-laravel`
- App service: `web`
- Database service: `MySQL`
- Service status: `SUCCESS`
- Public URL: [https://web-production-b59ff6.up.railway.app](https://web-production-b59ff6.up.railway.app)

## File Penting

- Aplikasi web: [routes/web.php](/Users/triyono/Projek/web%203/toko_online/routes/web.php)
- Cart service: [app/Services/CartService.php](/Users/triyono/Projek/web%203/toko_online/app/Services/CartService.php)
- Checkout service: [app/Services/CheckoutService.php](/Users/triyono/Projek/web%203/toko_online/app/Services/CheckoutService.php)
- Railway config: [railway.json](/Users/triyono/Projek/web%203/toko_online/railway.json)
- PRD: [docs/prd-toko-online.md](/Users/triyono/Projek/web%203/toko_online/docs/prd-toko-online.md)
- Task list: [tasklist.md](/Users/triyono/Projek/web%203/toko_online/tasklist.md)

## Next Step

1. Buat admin order detail dan update status order supaya operasional toko bisa berjalan dari dashboard.
2. Tambahkan payment gateway sungguhan dan sinkronkan `payment_status` dari callback/webhook.
3. Pisahkan worker Railway untuk queue jika nanti email, notifikasi, atau job berat mulai dipakai.
4. Tambahkan object storage untuk upload gambar produk, karena saat ini `cover_image` masih path manual.
5. Tambahkan custom domain, mailer produksi, dan HTTPS-ready `APP_URL` final.
6. Tambahkan pagination, invoice, dan email konfirmasi order.
7. Tambahkan observability minimum: log review, healthcheck monitoring, dan backup strategy database.
