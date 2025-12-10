<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'adjustment_date' => ['required', 'date'],
            'quantity_difference' => ['required', 'numeric', Rule::notIn([0])],
            'reason' => ['required', 'string'],
            'approved_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}
