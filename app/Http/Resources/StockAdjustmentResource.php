<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\StockAdjustment */
class StockAdjustmentResource extends JsonResource
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
            'adjustment_date' => $this->adjustment_date,
            'quantity_difference' => (float) $this->quantity_difference,
            'reason' => $this->reason,
            'approved_by' => $this->approved_by,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'sku' => $this->product->sku,
                    'name' => $this->product->name,
                ];
            }),
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'code' => $this->warehouse->code,
                    'name' => $this->warehouse->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
