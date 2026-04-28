@extends('layouts.storefront')

@section('title', 'Checkout')

@section('content')
@php
    $selectedCourier = old('courier', array_key_first($shippingOptions));
    $availableServices = $shippingOptions[$selectedCourier] ?? [];
    $selectedService = old('service', array_key_first($availableServices));
    $shippingTotal = $selectedService ? ($availableServices[$selectedService] ?? 0) : 0;
    $grandTotal = (float) $cart->subtotal + (float) $shippingTotal;
@endphp

<section class="container-xxl py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <h1 class="mb-1">Checkout</h1>
            <p class="text-body-secondary mb-0">Lengkapi alamat pengiriman dan metode pembayaran untuk membuat pesanan.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">Kembali ke Keranjang</a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('checkout.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label">Nama Penerima</label>
                            <input
                                id="customer_name"
                                type="text"
                                name="customer_name"
                                value="{{ old('customer_name', $defaultAddress?->recipient_name ?? $user?->name) }}"
                                class="form-control @error('customer_name') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label for="customer_phone" class="form-label">Nomor Telepon</label>
                            <input
                                id="customer_phone"
                                type="text"
                                name="customer_phone"
                                value="{{ old('customer_phone', $defaultAddress?->phone ?? $user?->phone) }}"
                                class="form-control @error('customer_phone') is-invalid @enderror">
                        </div>
                        <div class="col-12">
                            <label for="customer_email" class="form-label">Email</label>
                            <input
                                id="customer_email"
                                type="email"
                                name="customer_email"
                                value="{{ old('customer_email', $user?->email) }}"
                                class="form-control @error('customer_email') is-invalid @enderror">
                        </div>
                        <div class="col-12">
                            <label for="shipping_address" class="form-label">Alamat Pengiriman</label>
                            <textarea
                                id="shipping_address"
                                name="shipping_address"
                                rows="4"
                                class="form-control @error('shipping_address') is-invalid @enderror">{{ old('shipping_address', $defaultAddress?->address_line) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="shipping_city" class="form-label">Kota</label>
                            <input
                                id="shipping_city"
                                type="text"
                                name="shipping_city"
                                value="{{ old('shipping_city', $defaultAddress?->city) }}"
                                class="form-control @error('shipping_city') is-invalid @enderror">
                        </div>
                        <div class="col-md-4">
                            <label for="shipping_province" class="form-label">Provinsi</label>
                            <input
                                id="shipping_province"
                                type="text"
                                name="shipping_province"
                                value="{{ old('shipping_province', $defaultAddress?->province) }}"
                                class="form-control @error('shipping_province') is-invalid @enderror">
                        </div>
                        <div class="col-md-4">
                            <label for="shipping_postal_code" class="form-label">Kode Pos</label>
                            <input
                                id="shipping_postal_code"
                                type="text"
                                name="shipping_postal_code"
                                value="{{ old('shipping_postal_code', $defaultAddress?->postal_code) }}"
                                class="form-control @error('shipping_postal_code') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label for="courier" class="form-label">Kurir</label>
                            <select id="courier" name="courier" class="form-select @error('courier') is-invalid @enderror">
                                @foreach ($shippingOptions as $courier => $services)
                                    <option value="{{ $courier }}" @selected($selectedCourier === $courier)>{{ strtoupper($courier) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="service" class="form-label">Layanan</label>
                            <select
                                id="service"
                                name="service"
                                data-selected="{{ $selectedService }}"
                                class="form-select @error('service') is-invalid @enderror">
                                @foreach ($availableServices as $service => $price)
                                    <option value="{{ $service }}" @selected($selectedService === $service)>{{ strtoupper($service) }} - Rp {{ number_format($price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label d-block">Metode Pembayaran</label>
                            <div class="row g-3">
                                @foreach ($paymentMethods as $method => $label)
                                    <div class="col-md-4">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2 w-100">
                                            <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method', 'bank_transfer') === $method)>
                                            <span>{{ $label }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Catatan Pesanan</label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Contoh: kirim di jam kerja, titip satpam, atau detail lain yang diperlukan.">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Buat Pesanan</button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg">Kembali ke Keranjang</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Ringkasan Pesanan</h5>
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach ($cart->items as $item)
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $item->product?->name }}</div>
                                    <div class="text-body-secondary small">{{ $item->quantity }} x Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-semibold">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>Rp {{ number_format((float) $cart->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <strong data-shipping-total>Rp {{ number_format((float) $shippingTotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Diskon</span>
                        <strong>Rp 0</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Grand Total</span>
                        <strong class="fs-5" data-grand-total>Rp {{ number_format((float) $grandTotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-2">Catatan</h6>
                    <p class="text-body-secondary mb-0">
                        Setelah checkout, stok akan langsung dikurangi dan pesanan masuk ke dashboard admin dengan status awal `pending`.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const shippingOptions = @json($shippingOptions);
    const courierSelect = document.getElementById('courier');
    const serviceSelect = document.getElementById('service');
    const shippingTotalOutput = document.querySelector('[data-shipping-total]');
    const grandTotalOutput = document.querySelector('[data-grand-total]');
    const subtotal = Number(@json((float) $cart->subtotal));

    const formatCurrency = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;

    const syncTotals = () => {
      const courier = courierSelect.value;
      const service = serviceSelect.value;
      const shippingTotal = (shippingOptions[courier] || {})[service] || 0;

      shippingTotalOutput.textContent = formatCurrency(shippingTotal);
      grandTotalOutput.textContent = formatCurrency(subtotal + shippingTotal);
      serviceSelect.dataset.selected = service;
    };

    const syncServices = () => {
      const courier = courierSelect.value;
      const services = shippingOptions[courier] || {};
      const rememberedService = serviceSelect.dataset.selected;

      serviceSelect.innerHTML = '';

      Object.entries(services).forEach(([service, price], index) => {
        const option = document.createElement('option');
        option.value = service;
        option.textContent = `${service.toUpperCase()} - ${formatCurrency(price)}`;

        if (service === rememberedService || (!rememberedService && index === 0)) {
          option.selected = true;
        }

        serviceSelect.appendChild(option);
      });

      if (!serviceSelect.value && serviceSelect.options.length > 0) {
        serviceSelect.selectedIndex = 0;
      }

      syncTotals();
    };

    courierSelect.addEventListener('change', () => {
      serviceSelect.dataset.selected = '';
      syncServices();
    });

    serviceSelect.addEventListener('change', syncTotals);

    syncServices();
  });
</script>
@endsection
