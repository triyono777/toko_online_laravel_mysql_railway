@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Kategori')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Kategori</h5>
        <small class="text-body-secondary">Perbarui data kategori {{ $category->name }}.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @method('PUT')
            @include('admin.categories._form', ['submitLabel' => 'Update Kategori'])
        </form>
    </div>
</div>
@endsection
