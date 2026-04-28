@extends('layouts.storefront')

@section('title', 'Beranda')

@section('content')
<section class="container-xxl py-5">
    <div class="hero-surface rounded-4 p-4 p-lg-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-label-primary mb-3">Belanja Online</span>
                <h1 class="display-5 fw-bold mb-3">Temukan produk pilihan untuk kebutuhan harian Anda.</h1>
                <p class="fs-5 text-body-secondary mb-4">
                    Nikmati pengalaman belanja yang cepat, nyaman, dan rapi dengan katalog terkurasi, checkout praktis, dan layanan yang siap mendukung aktivitas belanja Anda.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Belanja Sekarang</a>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg">Lihat Keranjang</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card metric-card shadow-none border bg-white">
                            <div class="card-body">
                                <small class="text-body-secondary">Kategori</small>
                                <h3 class="mb-0 mt-2">{{ $categories->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card metric-card shadow-none border bg-white">
                            <div class="card-body">
                                <small class="text-body-secondary">Produk unggulan</small>
                                <h3 class="mb-0 mt-2">{{ $featuredProducts->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card shadow-none border bg-white">
                            <div class="card-body">
                                <p class="mb-2 fw-semibold">Keunggulan layanan</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-label-success">Produk Pilihan</span>
                                    <span class="badge bg-label-success">Checkout Cepat</span>
                                    <span class="badge bg-label-info">Pembayaran Mudah</span>
                                    <span class="badge bg-label-info">Pengiriman Terjadwal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Kategori utama</h2>
            <p class="text-body-secondary mb-0">Pilih kategori untuk menemukan produk yang paling sesuai dengan kebutuhan Anda.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Lihat semua produk</a>
    </div>
    <div class="row g-4 mb-5">
        @forelse ($categories as $category)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <span class="badge bg-label-primary mb-3">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                        <h4>{{ $category->name }}</h4>
                        <p class="text-body-secondary mb-0">{{ $category->description }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">Kategori belum tersedia saat ini. Silakan cek kembali dalam beberapa saat.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">Produk terbaru</h2>
            <p class="text-body-secondary mb-0">Produk terbaru yang siap Anda lihat dan pesan hari ini.</p>
        </div>
    </div>
    <div class="row g-4">
        @forelse ($latestProducts as $product)
            <div class="col-md-6 col-xl-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <img
                        src="{{ asset($product->cover_image ?: 'assets/img/elements/1.jpg') }}"
                        alt="{{ $product->name }}"
                        class="card-img-top product-cover">
                    <div class="card-body">
                        <div class="text-body-secondary small mb-2">{{ $product->category?->name }}</div>
                        <h5 class="mb-2">{{ $product->name }}</h5>
                        <p class="text-body-secondary small">{{ $product->excerpt }}</p>
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <strong>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</strong>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">Produk belum tersedia saat ini. Silakan cek kembali dalam beberapa saat.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection
