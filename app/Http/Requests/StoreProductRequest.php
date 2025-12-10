<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')],
            'name' => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'product_unit_id' => ['required', 'integer', 'exists:product_units,id'],
            'default_tax_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
