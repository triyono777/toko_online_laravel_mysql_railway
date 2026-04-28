@extends('layouts.storefront')

@section('title', $product->name)

@section('content')
<section class="container-xxl py-5">
    <div class="row g-4 align-items-start">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <img
                    src="{{ asset($product->cover_image ?: 'assets/img/elements/5.jpg') }}"
                    alt="{{ $product->name }}"
                    class="w-100"
                    style="aspect-ratio: 4 / 3; object-fit: cover;">
            </div>
        </div>
        <div class="col-lg-6">
            <span class="badge bg-label-primary mb-3">{{ $product->category?->name }}</span>
            <h1 class="mb-2">{{ $product->name }}</h1>
            <div class="mb-3 text-body-secondary">SKU {{ $product->sku }} | Stok tersedia {{ $product->stock }}</div>
            <div class="d-flex align-items-center gap-3 mb-4">
                <h3 class="mb-0">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</h3>
                @if ($product->compare_price)
                    <span class="text-decoration-line-through text-body-secondary">
                        Rp {{ number_format((float) $product->compare_price, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            <p class="text-body-secondary fs-6">{{ $product->description ?: $product->excerpt }}</p>

            <div class="card bg-label-primary border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-body-secondary d-block">Status</small>
                            <strong>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-body-secondary d-block">Berat</small>
                            <strong>{{ $product->weight ? number_format((float) $product->weight, 0, ',', '.') . ' gr' : '-' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-body-secondary d-block">Featured</small>
                            <strong>{{ $product->featured ? 'Ya' : 'Tidak' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if ($product->is_active && $product->stock > 0)
                        <form method="POST" action="{{ route('cart.items.store', $product) }}" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-sm-4 col-lg-3">
                                <label for="quantity" class="form-label">Jumlah</label>
                                <input
                                    id="quantity"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    value="{{ old('quantity', 1) }}"
                                    class="form-control @error('quantity') is-invalid @enderror">
                            </div>
                            <div class="col-sm-8 col-lg-9 d-flex flex-wrap gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">Tambah ke Keranjang</button>
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg">Lihat Keranjang</a>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning mb-0">
                            Produk ini belum bisa ditambahkan ke keranjang karena stok habis atau statusnya nonaktif.
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">Kembali ke Katalog</a>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h3 class="mb-4">Produk terkait</h3>
        <div class="row g-4">
            @forelse ($relatedProducts as $relatedProduct)
                <div class="col-md-6 col-xl-3">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <img src="{{ asset($relatedProduct->cover_image ?: 'assets/img/elements/10.png') }}" alt="{{ $relatedProduct->name }}" class="card-img-top product-cover">
                        <div class="card-body">
                            <h6>{{ $relatedProduct->name }}</h6>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <strong>Rp {{ number_format((float) $relatedProduct->price, 0, ',', '.') }}</strong>
                                <a href="{{ route('products.show', $relatedProduct) }}" class="btn btn-sm btn-outline-primary">Buka</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-body-secondary">Belum ada produk terkait.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
