@extends('layouts/contentNavbarLayout')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Kategori</h5>
        <small class="text-body-secondary">Buat kategori baru untuk struktur katalog toko online.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @include('admin.categories._form', ['submitLabel' => 'Simpan Kategori'])
        </form>
    </div>
</div>
@endsection
