<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfer_date' => ['sometimes', 'required', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
