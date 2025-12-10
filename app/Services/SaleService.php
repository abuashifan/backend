<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\Warehouse;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly JournalService $journalService,
    ) {
    }

    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::find($data['customer_id'] ?? null);
            $product = Product::find($data['product_id'] ?? null);
            $warehouse = Warehouse::find($data['warehouse_id'] ?? null);
            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;

            if (!$customer || !$product || !$warehouse) {
                throw new DomainException('Invalid customer, product, or warehouse provided.');
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

            $subtotal = $quantity * $unitPrice;
            $taxBase = $subtotal - $discountAmount;
            $taxAmount = $tax ? ($taxBase * ((float) $tax->rate / 100)) : 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            $sale = Sale::create([
                'customer_id' => $customer->id,
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

            AccountReceivable::create([
                'customer_id' => $customer->id,
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'original_amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'status' => 'open',
            ]);

            $this->inventoryService->decreaseStock(
                $product->id,
                $warehouse->id,
                $quantity,
                'sale',
                $sale->id
            );

            $this->journalService->recordSale($sale, $product, $quantity);

            return $sale->fresh(['customer', 'warehouse', 'tax', 'accountReceivable']);
        });
    }
}
