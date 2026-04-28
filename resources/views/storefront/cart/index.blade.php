@extends('layouts.storefront')

@section('title', 'Keranjang')

@section('content')
<section class="container-xxl py-5">
    @php
        $isCustomer = auth()->check() && auth()->user()->role === 'customer';
    @endphp
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                        <div>
                            <h2 class="mb-1">Keranjang Belanja</h2>
                            <p class="text-body-secondary mb-0">{{ $cart->total_items }} item siap diproses ke checkout.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Tambah Produk Lain</a>
                    </div>

                    @if ($cartItems->isEmpty())
                        <div class="alert alert-info mb-0">
                            Keranjang masih kosong. Pilih produk dari katalog untuk mulai belanja.
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach ($cartItems as $cartItem)
                                <div class="border rounded-3 p-3">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-2">
                                            <img
                                                src="{{ asset($cartItem->product?->cover_image ?: 'assets/img/elements/2.jpg') }}"
                                                alt="{{ $cartItem->product?->name }}"
                                                class="w-100 rounded-3"
                                                style="aspect-ratio: 1 / 1; object-fit: cover;">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="small text-body-secondary mb-1">{{ $cartItem->product?->category?->name }}</div>
                                            <h5 class="mb-1">{{ $cartItem->product?->name }}</h5>
                                            <div class="text-body-secondary small mb-2">SKU {{ $cartItem->product?->sku }} | Stok {{ $cartItem->product?->stock }}</div>
                                            <div class="fw-semibold">Rp {{ number_format((float) $cartItem->unit_price, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <form method="POST" action="{{ route('cart.items.update', $cartItem) }}" class="d-flex flex-column gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <label for="quantity-{{ $cartItem->id }}" class="form-label mb-0">Jumlah</label>
                                                <input
                                                    id="quantity-{{ $cartItem->id }}"
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    max="{{ $cartItem->product?->stock ?? 1 }}"
                                                    value="{{ $cartItem->quantity }}"
                                                    class="form-control">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Perbarui</button>
                                            </form>
                                        </div>
                                        <div class="col-md-2 text-md-end">
                                            <div class="fw-semibold mb-2">Rp {{ number_format((float) $cartItem->line_total, 0, ',', '.') }}</div>
                                            <form method="POST" action="{{ route('cart.items.destroy', $cartItem) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                        <strong>Rp {{ number_format((float) $cart->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimasi ongkir</span>
                        <strong>Dipilih saat checkout</strong>
                    </div>
                    <div class="text-body-secondary small mb-3">
                        Opsi kurir:
                        @foreach ($shippingOptions as $courier => $services)
                            <span class="badge bg-label-primary me-1">{{ strtoupper($courier) }}</span>
                        @endforeach
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Total</span>
                        <strong>Rp {{ number_format((float) $cart->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    @if ($cartItems->isEmpty())
                        <button class="btn btn-primary w-100" disabled>Keranjang Masih Kosong</button>
                    @elseif (! auth()->check())
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Login untuk Checkout</a>
                    @elseif ($isCustomer)
                        <a href="{{ route('checkout.create') }}" class="btn btn-primary w-100">Lanjut ke Checkout</a>
                    @else
                        <button class="btn btn-primary w-100" disabled>Akun admin tidak bisa checkout</button>
                    @endif
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
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                @if ($product->is_active && $product->stock > 0)
                                    <form method="POST" action="{{ route('cart.items.store', $product) }}" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Tambah</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
