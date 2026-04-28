@extends('layouts.storefront')

@section('title', 'Katalog Produk')

@section('content')
<section class="container-xxl py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <h1 class="mb-1">Katalog Produk</h1>
            <p class="text-body-secondary mb-0">Temukan produk terbaik untuk kebutuhan Anda dalam satu katalog yang rapi dan mudah dijelajahi.</p>
        </div>
        <form method="GET" action="{{ route('products.index') }}" class="row g-2">
            <div class="col-12 col-md-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Cari produk atau SKU">
            </div>
            <div class="col-12 col-md-auto">
                <select name="category" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse ($products as $product)
            <div class="col-md-6 col-xl-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <img
                        src="{{ asset($product->cover_image ?: 'assets/img/elements/3.jpg') }}"
                        alt="{{ $product->name }}"
                        class="card-img-top product-cover">
                    <div class="card-body d-flex flex-column">
                        <div class="small text-body-secondary mb-2">{{ $product->category?->name }}</div>
                        <h5>{{ $product->name }}</h5>
                        <p class="text-body-secondary small flex-grow-1">{{ $product->excerpt }}</p>
                        <div class="small text-body-secondary mb-3">Stok: {{ $product->stock }} | SKU: {{ $product->sku }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <strong>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</strong>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">Tidak ada produk yang cocok dengan filter saat ini.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection
