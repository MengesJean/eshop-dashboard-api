<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Category $product */
        $category = $this->route('category');

        return $this->user()?->can('update', $category) ?? false;
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Category $category */
        $category = $this->route('category');
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($category->id),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'active' => ['sometimes', 'required', 'boolean'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'level' => ['sometimes', 'required', 'integer', 'between:1,100'],
        ];
    }
}
