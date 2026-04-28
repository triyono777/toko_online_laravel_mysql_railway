@php
$containerFooter = !empty($containerNav) ? $containerNav : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                © <script>
                document.write(new Date().getFullYear())
                </script>, dikelola oleh <a href="{{ (!empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '') }}" target="_blank" class="footer-link">{{ (!empty(config('variables.creatorName')) ? config('variables.creatorName') : '') }}</a>
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="{{ route('admin.dashboard') }}" class="footer-link me-4">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="footer-link me-4">Produk</a>
                <a href="{{ route('admin.orders.index') }}" class="footer-link me-4">Pesanan</a>
                <a href="{{ route('home') }}" class="footer-link d-none d-sm-inline-block">Storefront</a>
            </div>
        </div>
    </div>
</footer>
<!--/ Footer-->
