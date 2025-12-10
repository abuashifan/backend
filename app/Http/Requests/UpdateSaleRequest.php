<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale')?->id;

        return [
            'invoice_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('sales', 'invoice_number')->ignore($saleId)],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'required', Rule::in(['open', 'partial', 'paid', 'cancelled'])],
        ];
    }
}
