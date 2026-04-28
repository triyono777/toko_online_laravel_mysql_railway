@extends('layouts/blankLayout')

@section('title', 'Masuk Admin')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card px-sm-6 px-0 border-primary">
                <div class="card-body">
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">@include('_partials.macros')</span>
                            <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                        </a>
                    </div>

                    <h4 class="mb-1">Masuk ke dashboard admin</h4>
                    <p class="mb-6">Gunakan akun administrator untuk mengelola produk, pesanan, dan pelanggan.</p>

                    <form class="mb-6" action="{{ route('admin.login.store') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="email" class="form-label">Email Admin</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email admin" autofocus />
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="Masukkan password" />
                                <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="remember-admin" name="remember" value="1" />
                                <label class="form-check-label" for="remember-admin">Remember Me</label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100" type="submit">Masuk Admin</button>
                    </form>

                    <p class="text-center mb-0">
                        <a href="{{ route('login') }}">Kembali ke login pelanggan</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
