<?php

namespace App\Http\Requests\Storefront;

use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_province' => ['required', 'string', 'max:255'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'courier' => ['required', 'string', 'in:jne,sicepat,anteraja'],
            'service' => ['required', 'string', 'max:50'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', array_keys(app(CheckoutService::class)->paymentMethods()))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $shippingOptions = app(CartService::class)->shippingOptions();
                $courier = (string) $this->input('courier');
                $service = (string) $this->input('service');

                if ($courier === '' || $service === '') {
                    return;
                }

                if (! isset($shippingOptions[$courier][$service])) {
                    $validator->errors()->add('service', 'Layanan pengiriman tidak valid untuk kurir yang dipilih.');
                }
            },
        ];
    }
}
