<?php

namespace App\Http\Requests\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jika perlu otorisasi
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')
                    ->ignore($categoryId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
