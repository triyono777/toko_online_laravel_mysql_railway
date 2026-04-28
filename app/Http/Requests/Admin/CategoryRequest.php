<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(Category::class, 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slugSource = $this->filled('slug') ? $this->string('slug')->toString() : $this->string('name')->toString();

        $this->merge([
            'slug' => Str::slug($slugSource),
            'sort_order' => $this->input('sort_order', 0),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ((string) $this->input('slug') === '') {
                    $validator->errors()->add('slug', 'Slug kategori tidak boleh kosong.');
                }
            },
        ];
    }
}
