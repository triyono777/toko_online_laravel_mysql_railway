@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="card metric-card">
            <div class="card-body">
                <span class="badge bg-label-primary mb-3">Katalog</span>
                <h3 class="mb-1">{{ $stats['products'] }}</h3>
                <p class="mb-0 text-body-secondary">Total produk aktif dan draft.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card metric-card">
            <div class="card-body">
                <span class="badge bg-label-info mb-3">Pelanggan</span>
                <h3 class="mb-1">{{ $stats['customers'] }}</h3>
                <p class="mb-0 text-body-secondary">Pelanggan yang sudah tersimpan.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card metric-card">
            <div class="card-body">
                <span class="badge bg-label-warning mb-3">Pesanan</span>
                <h3 class="mb-1">{{ $stats['orders'] }}</h3>
                <p class="mb-0 text-body-secondary">Order yang sudah tercatat.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card metric-card">
            <div class="card-body">
                <span class="badge bg-label-success mb-3">Pendapatan</span>
                <h3 class="mb-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
                <p class="mb-0 text-body-secondary">Akumulasi order berstatus dibayar.</p>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Pesanan Terbaru</h5>
                    <small class="text-body-secondary">Ringkasan penjualan terakhir yang masuk ke sistem.</small>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Semua pesanan</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($latestOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>
                                    <span class="badge bg-label-primary">{{ strtoupper($order->status) }}</span>
                                </td>
                                <td>Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary py-4">Belum ada order. Jalankan seeder atau mulai input data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Stok Rendah</h5>
                <small class="text-body-secondary">Produk yang perlu dipantau agar tidak cepat habis.</small>
            </div>
            <div class="card-body">
                @forelse ($lowStockProducts as $product)
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-body-secondary">{{ $product->sku }}</small>
                        </div>
                        <span class="badge bg-label-warning">{{ $product->stock }} unit</span>
                    </div>
                @empty
                    <div class="text-body-secondary">Belum ada data stok rendah.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
