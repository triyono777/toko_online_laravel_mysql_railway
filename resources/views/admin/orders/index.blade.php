@extends('layouts/contentNavbarLayout')

@section('title', 'Pesanan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pesanan</h5>
        <small class="text-body-secondary">Daftar order untuk proses pembayaran, pengemasan, dan pengiriman.</small>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Status Order</th>
                    <th>Status Bayar</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $order->customer_name }}</h6>
                                <small class="text-body-secondary">{{ $order->customer_email }}</small>
                            </div>
                        </td>
                        <td><span class="badge bg-label-primary">{{ strtoupper($order->status) }}</span></td>
                        <td><span class="badge bg-label-info">{{ strtoupper($order->payment_status) }}</span></td>
                        <td>Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                        <td>{{ optional($order->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">Belum ada order yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
