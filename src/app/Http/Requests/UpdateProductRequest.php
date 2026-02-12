<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $this->user()?->can('update', $product) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug') && $this->filled('name') && $this->input('slug') === '') {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }

        // Si tu veux auto-générer slug quand slug est absent mais name présent :
        if (! $this->has('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }
    }

    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:64',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],

            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => [
                'integer',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $hasChildren = Category::query()->where('parent_id', $value)->exists();
                    if ($hasChildren) {
                        $fail("La catégorie {$value} n'est pas une feuille (elle a des sous-catégories).");
                    }
                },
            ],

            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:500'],
            'weight' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],

            'short_description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'description' => ['sometimes', 'nullable', 'string'],

            'stock' => ['sometimes', 'required', 'integer', 'min:0', 'max:500'],
            'active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
