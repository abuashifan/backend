<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Purchase */
class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                    'email' => $this->supplier->email,
                ];
            }),
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                ];
            }),
            'tax' => $this->whenLoaded('tax', function () {
                return [
                    'id' => $this->tax->id,
                    'name' => $this->tax->name,
                    'rate' => (float) $this->tax->rate,
                ];
            }),
            'account_payable' => $this->whenLoaded('accountPayable', function () {
                return [
                    'id' => $this->accountPayable->id,
                    'remaining_amount' => (float) $this->accountPayable->remaining_amount,
                    'status' => $this->accountPayable->status,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
