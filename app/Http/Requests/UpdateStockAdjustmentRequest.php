<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adjustment_date' => ['sometimes', 'required', 'date'],
            'reason' => ['sometimes', 'required', 'string'],
            'approved_by' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
