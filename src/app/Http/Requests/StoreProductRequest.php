<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if(!$this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
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
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:500'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'stock' => ['required', 'numeric', 'min:0', 'max:500'],
            'active' => ['required', 'boolean'],

        ];
    }
}
