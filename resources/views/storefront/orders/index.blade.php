@extends('layouts.storefront')

@section('title', 'Pesanan Saya')

@section('content')
<section class="container-xxl py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <h1 class="mb-1">Riwayat Pesanan</h1>
            <p class="text-body-secondary mb-0">Daftar pesanan yang sudah dibuat oleh akun pelanggan Anda.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Belanja Lagi</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if ($orders->isEmpty())
                <div class="p-4">
                    <div class="alert alert-info mb-0">Belum ada pesanan. Setelah checkout, daftar pesanan akan muncul di halaman ini.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->order_number }}</td>
                                    <td>{{ optional($order->placed_at)->format('d M Y H:i') }}</td>
                                    <td><span class="badge bg-label-warning text-uppercase">{{ $order->status }}</span></td>
                                    <td><span class="badge bg-label-info text-uppercase">{{ $order->payment_status }}</span></td>
                                    <td class="fw-semibold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
