@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Produk')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Produk</h5>
        <small class="text-body-secondary">Tambahkan produk baru ke katalog publik dan dashboard admin.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.store') }}">
            @include('admin.products._form', ['submitLabel' => 'Simpan Produk'])
        </form>
    </div>
</div>
@endsection
