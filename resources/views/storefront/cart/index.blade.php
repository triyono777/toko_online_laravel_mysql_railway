@extends('layouts.storefront')

@section('title', 'Keranjang')

@section('content')
<section class="container-xxl py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-3">Keranjang Belanja</h2>
                    @if ($cartItems->isEmpty())
                        <div class="alert alert-info mb-0">
                            Keranjang masih kosong. Struktur halaman checkout sudah disiapkan, tetapi logika cart belum dihubungkan ke session/database pada tahap ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Ringkasan</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <strong>Rp 0</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Total</span>
                        <strong>Rp 0</strong>
                    </div>
                    <button class="btn btn-primary w-100" disabled>Checkout segera hadir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h3 class="mb-4">Produk yang bisa Anda tambahkan</h3>
        <div class="row g-4">
            @foreach ($suggestedProducts as $product)
                <div class="col-md-6 col-xl-3">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <img src="{{ asset($product->cover_image ?: 'assets/img/elements/2.jpg') }}" alt="{{ $product->name }}" class="card-img-top product-cover">
                        <div class="card-body">
                            <h6>{{ $product->name }}</h6>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <strong>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</strong>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
