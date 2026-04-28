@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Kategori Produk</h5>
            <small class="text-body-secondary">Daftar kategori utama untuk pengelompokan katalog.</small>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Jumlah Produk</th>
                    <th>Status</th>
                    <th>Urutan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <span class="badge bg-label-{{ $category->is_active ? 'success' : 'secondary' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">Belum ada kategori. Tambahkan kategori pertama untuk katalog produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
