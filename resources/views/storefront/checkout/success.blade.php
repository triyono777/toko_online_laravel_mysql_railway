@extends('layouts.storefront')

@section('title', 'Checkout Berhasil')

@section('content')
<section class="container-xxl py-5">
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-4 p-lg-5">
            <span class="badge bg-label-success mb-3">Pesanan Berhasil Dibuat</span>
            <h1 class="mb-2">Checkout selesai untuk {{ $order->order_number }}</h1>
            <p class="text-body-secondary mb-4">
                Pesanan Anda sudah masuk ke sistem dan menunggu proses pembayaran serta verifikasi admin.
            </p>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block mb-1">Status Pesanan</small>
                        <strong class="text-uppercase">{{ $order->status }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block mb-1">Pembayaran</small>
                        <strong class="text-uppercase">{{ $order->payment_status }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block mb-1">Pengiriman</small>
                        <strong class="text-uppercase">{{ $order->shipping_status }}</strong>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <h5 class="mb-3">Item Pesanan</h5>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between gap-3 border rounded-3 p-3">
                                <div>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    <div class="text-body-secondary small">{{ $item->quantity }} x Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-semibold">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <h5 class="mb-3">Ringkasan Pembayaran</h5>
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkir</span>
                            <strong>Rp {{ number_format((float) $order->shipping_total, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Metode Bayar</span>
                            <strong>{{ str_replace('_', ' ', strtoupper($order->payment?->method ?? '-')) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Grand Total</span>
                            <strong class="fs-5">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-primary">Lihat Detail Pesanan</a>
                <a href="{{ route('account.orders.index') }}" class="btn btn-outline-primary">Riwayat Pesanan</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Lanjut Belanja</a>
            </div>
        </div>
    </div>
</section>
@endsection
