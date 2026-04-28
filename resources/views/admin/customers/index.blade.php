@extends('layouts/contentNavbarLayout')

@section('title', 'Pelanggan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pelanggan</h5>
        <small class="text-body-secondary">Data customer yang tersimpan dalam sistem.</small>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?: '-' }}</td>
                        <td><span class="badge bg-label-primary">{{ strtoupper($customer->role) }}</span></td>
                        <td>{{ optional($customer->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-body-secondary">Belum ada pelanggan yang tersimpan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
