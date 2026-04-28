@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Produk')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Produk</h5>
        <small class="text-body-secondary">Perbarui informasi produk {{ $product->name }}.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @method('PUT')
            @include('admin.products._form', ['submitLabel' => 'Update Produk'])
        </form>
    </div>
</div>
@endsection
