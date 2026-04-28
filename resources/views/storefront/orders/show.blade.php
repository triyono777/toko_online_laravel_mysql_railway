@extends('layouts.storefront')

@section('title', $order->order_number)

@section('content')
<section class="container-xxl py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
        <div>
            <h1 class="mb-1">{{ $order->order_number }}</h1>
            <p class="text-body-secondary mb-0">Dibuat pada {{ optional($order->placed_at)->format('d M Y H:i') }} oleh {{ $order->customer_name }}.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-label-warning text-uppercase px-3 py-2">{{ $order->status }}</span>
            <span class="badge bg-label-info text-uppercase px-3 py-2">{{ $order->payment_status }}</span>
            <span class="badge bg-label-primary text-uppercase px-3 py-2">{{ $order->shipping_status }}</span>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Item Pesanan</h5>
                    <div class="d-flex flex-column gap-3">
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between gap-3 border rounded-3 p-3">
                                <div>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    <div class="text-body-secondary small">SKU {{ $item->product_sku }}</div>
                                    <div class="text-body-secondary small">{{ $item->quantity }} x Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-semibold">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Alamat Pengiriman</h5>
                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                    <div class="text-body-secondary">{{ $order->customer_phone }}</div>
                    <div class="mt-2">{{ $order->shipping_address }}</div>
                    <div>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</div>
                    @if ($order->notes)
                        <div class="alert alert-light border mt-3 mb-0">{{ $order->notes }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Ringkasan Pembayaran</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon</span>
                        <strong>Rp {{ number_format((float) $order->discount_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <strong>Rp {{ number_format((float) $order->shipping_total, 0, ',', '.') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Grand Total</span>
                        <strong class="fs-5">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Pengiriman dan Pembayaran</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kurir</span>
                        <strong>{{ strtoupper($order->shipment?->courier ?? '-') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Layanan</span>
                        <strong>{{ strtoupper($order->shipment?->service ?? '-') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Metode Bayar</span>
                        <strong>{{ str_replace('_', ' ', strtoupper($order->payment?->method ?? '-')) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status Pembayaran</span>
                        <strong class="text-uppercase">{{ $order->payment?->status ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
