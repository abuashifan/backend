<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Warehouse;
use App\Services\Calculators\PurchaseCalculator;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly JournalService $journalService,
        private readonly PurchaseCalculator $calculator
    ) {
    }

    /**
     * Create a purchase transaction.
     *
     * This will:
     * - validate supplier/product/warehouse existence
     * - compute subtotal, tax and totals
     * - persist Purchase and AccountPayable
     * - update inventory and journal entries
     *
     * @param  array  $data
     * @return Purchase
     *
     * @throws \DomainException when supplier/product/warehouse are invalid
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::find($data['supplier_id'] ?? null);
            $product = Product::find($data['product_id'] ?? null);
            $warehouse = Warehouse::find($data['warehouse_id'] ?? null);
            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;

            if (!$supplier || !$product || !$warehouse) {
                throw new DomainException('Invalid supplier, product, or warehouse provided.');
            }

            $invoiceDate = $data['invoice_date'] instanceof Carbon
                ? $data['invoice_date']
                : Carbon::parse($data['invoice_date']);

            $dueDate = isset($data['due_date']) && $data['due_date'] !== null
                ? ($data['due_date'] instanceof Carbon ? $data['due_date'] : Carbon::parse($data['due_date']))
                : $invoiceDate;

            $quantity = (float) ($data['quantity'] ?? 0);
            $unitPrice = (float) ($data['unit_price'] ?? 0);
            $discountAmount = (float) ($data['discount_amount'] ?? 0);

            $computed = $this->calculator->compute($quantity, $unitPrice, $discountAmount, $tax?->id);
            $subtotal = $computed['subtotal'];
            $taxAmount = $computed['tax_amount'];
            $totalAmount = $computed['total_amount'];

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'tax_id' => $tax?->id,
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'open',
            ]);

            AccountPayable::create([
                'supplier_id' => $supplier->id,
                'purchase_id' => $purchase->id,
                'invoice_number' => $purchase->invoice_number,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'original_amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'status' => 'open',
            ]);

            $this->stockService->increaseStock(
                $product->id,
                $warehouse->id,
                $quantity,
                'purchase',
                $purchase->id
            );

            $this->journalService->recordPurchase($purchase, $product, $quantity);

            return $purchase->fresh(['supplier', 'warehouse', 'tax', 'accountPayable']);
        });
    }
}
