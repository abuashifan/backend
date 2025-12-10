<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\StockTransfer */
class StockTransferResource extends JsonResource
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
            'transfer_date' => $this->transfer_date,
            'quantity' => (float) $this->quantity,
            'notes' => $this->notes,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'sku' => $this->product->sku,
                    'name' => $this->product->name,
                ];
            }),
            'from_warehouse' => $this->whenLoaded('fromWarehouse', function () {
                return [
                    'id' => $this->fromWarehouse->id,
                    'code' => $this->fromWarehouse->code,
                    'name' => $this->fromWarehouse->name,
                ];
            }),
            'to_warehouse' => $this->whenLoaded('toWarehouse', function () {
                return [
                    'id' => $this->toWarehouse->id,
                    'code' => $this->toWarehouse->code,
                    'name' => $this->toWarehouse->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
