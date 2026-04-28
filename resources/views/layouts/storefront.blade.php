@extends('layouts/commonMaster')

@section('layoutContent')
<div class="min-vh-100 d-flex flex-column bg-body">
    <header class="storefront-header sticky-top border-bottom">
        <div class="container-xxl">
            <div class="d-flex align-items-center justify-content-between py-3">
                <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none gap-2">
                    <span class="app-brand-logo demo">@include('_partials.macros')</span>
                    <span class="fw-bold text-heading fs-4">{{ config('variables.templateName') }}</span>
                </a>
                <nav class="d-flex align-items-center gap-3 gap-md-4">
                    <a href="{{ route('home') }}" class="text-body">Beranda</a>
                    <a href="{{ route('products.index') }}" class="text-body">Produk</a>
                    <a href="{{ route('admin.dashboard') }}" class="text-body">Admin</a>
                    <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm">Keranjang</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="border-top bg-white">
        <div class="container-xxl py-4 d-flex flex-column flex-md-row justify-content-between gap-2">
            <div class="text-body-secondary">
                {{ config('variables.templateDescription') }}
            </div>
            <div class="text-body-secondary">
                Laravel + MySQL + Sneat Theme
            </div>
        </div>
    </footer>
</div>
@endsection
