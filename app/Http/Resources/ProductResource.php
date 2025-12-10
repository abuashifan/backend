<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'product_category' => $this->whenLoaded('productCategory', function () {
                return [
                    'id' => $this->productCategory->id,
                    'name' => $this->productCategory->name,
                ];
            }),
            'product_unit' => $this->whenLoaded('productUnit', function () {
                return [
                    'id' => $this->productUnit->id,
                    'name' => $this->productUnit->name,
                ];
            }),
            'default_tax' => $this->whenLoaded('defaultTax', function () {
                return [
                    'id' => $this->defaultTax->id,
                    'name' => $this->defaultTax->name,
                    'rate' => (float) $this->defaultTax->rate,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
