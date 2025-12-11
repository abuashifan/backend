<?php

declare(strict_types=1);

namespace App\Services\Calculators;

use App\Models\Tax;

class SaleCalculator
{
    /**
     * Calculate subtotal from quantity and unit price.
     */
    public function subtotal(float $quantity, float $unitPrice): float
    {
        return $quantity * $unitPrice;
    }

    /**
     * Calculate tax amount given a tax id (nullable) and taxable base.
     */
    public function taxAmount(?int $taxId, float $taxBase): float
    {
        if ($taxId === null) {
            return 0.0;
        }

        $tax = Tax::find($taxId);

        if (! $tax) {
            return 0.0;
        }

        return round($taxBase * ((float) $tax->rate / 100), 2);
    }

    /**
     * Compute totals: subtotal, tax, and total amount.
     *
     * @return array{subtotal:float,tax_amount:float,total_amount:float}
     */
    public function compute(float $quantity, float $unitPrice, float $discountAmount, ?int $taxId): array
    {
        $subtotal = $this->subtotal($quantity, $unitPrice);
        $taxBase = $subtotal - $discountAmount;
        $taxAmount = $this->taxAmount($taxId, $taxBase);
        $total = $subtotal - $discountAmount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ];
    }
}
