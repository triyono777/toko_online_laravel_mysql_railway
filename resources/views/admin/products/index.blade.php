@extends('layouts/contentNavbarLayout')

@section('title', 'Produk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Produk</h5>
            <small class="text-body-secondary">Manajemen katalog produk untuk toko online.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Lihat katalog publik</a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">Tambah Produk</a>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>SKU</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset($product->cover_image ?: 'assets/img/elements/4.png') }}" alt="{{ $product->name }}" class="rounded">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                    <small class="text-body-secondary">{{ $product->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            <span class="badge bg-label-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $product->featured ? 'warning' : 'secondary' }}">
                                {{ $product->featured ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-body-secondary">Belum ada produk. Tambahkan produk pertama untuk katalog publik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
