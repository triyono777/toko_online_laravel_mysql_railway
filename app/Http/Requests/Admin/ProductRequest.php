<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(Product::class, 'slug')->ignore($productId)],
            'sku' => ['required', 'string', 'max:100', Rule::unique(Product::class, 'sku')->ignore($productId)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'gte:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'featured' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slugSource = $this->filled('slug') ? $this->string('slug')->toString() : $this->string('name')->toString();

        $this->merge([
            'slug' => Str::slug($slugSource),
            'sku' => Str::upper(trim((string) $this->input('sku'))),
            'price' => $this->input('price'),
            'compare_price' => $this->filled('compare_price') ? $this->input('compare_price') : null,
            'stock' => $this->input('stock', 0),
            'weight' => $this->filled('weight') ? $this->input('weight') : null,
            'is_active' => $this->boolean('is_active'),
            'featured' => $this->boolean('featured'),
            'cover_image' => $this->filled('cover_image') ? trim((string) $this->input('cover_image')) : null,
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ((string) $this->input('slug') === '') {
                    $validator->errors()->add('slug', 'Slug produk tidak boleh kosong.');
                }
            },
        ];
    }
}
