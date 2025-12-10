<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'product_category_id' => ['sometimes', 'required', 'integer', 'exists:product_categories,id'],
            'product_unit_id' => ['sometimes', 'required', 'integer', 'exists:product_units,id'],
            'default_tax_id' => ['sometimes', 'nullable', 'integer', 'exists:taxes,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'cost_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'selling_price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
