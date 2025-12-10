<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $purchaseId = $this->route('purchase')?->id;

        return [
            'invoice_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('purchases', 'invoice_number')->ignore($purchaseId)],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'required', Rule::in(['open', 'partial', 'paid', 'cancelled'])],
        ];
    }
}
