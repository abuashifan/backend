<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Receipt;
use App\Models\Sale;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class JournalService
{
    private function getAccountByCode(string $code): ChartOfAccount
    {
        return ChartOfAccount::where('code', $code)->firstOrFail();
    }

    private function createEntry(
        int $chartOfAccountId,
        CarbonInterface $date,
        string $description,
        float $debit,
        float $credit,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): JournalEntry {
        if (($debit > 0 && $credit > 0) || ($debit <= 0 && $credit <= 0)) {
            throw new InvalidArgumentException('Exactly one of debit or credit must be greater than zero.');
        }

        if ($debit > 0 && $credit !== 0.0) {
            throw new InvalidArgumentException('Credit must be zero when debit is greater than zero.');
        }

        if ($credit > 0 && $debit !== 0.0) {
            throw new InvalidArgumentException('Debit must be zero when credit is greater than zero.');
        }

        return JournalEntry::create([
            'entry_date' => $date,
            'chart_of_account_id' => $chartOfAccountId,
            'description' => $description,
            'debit' => $debit,
            'credit' => $credit,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function recordSale(Sale $sale, Product $product, float $quantity): void
    {
        $entryDate = $this->toCarbonDate($sale->invoice_date);
        $total = (float) $sale->total_amount;
        $hpp = (float) $product->cost_price * $quantity;

        $accountsReceivable = $this->getAccountByCode('1130');
        $salesRevenue = $this->getAccountByCode('4110');
        $cogs = $this->getAccountByCode('5110');
        $inventory = $this->getAccountByCode('1141');

        $description = "Sale #{$sale->invoice_number}";

        $this->createEntry(
            $accountsReceivable->id,
            $entryDate,
            $description,
            $total,
            0,
            Sale::class,
            $sale->id
        );

        $this->createEntry(
            $salesRevenue->id,
            $entryDate,
            $description,
            0,
            $total,
            Sale::class,
            $sale->id
        );

        $cogsDescription = "COGS for sale #{$sale->invoice_number}";

        $this->createEntry(
            $cogs->id,
            $entryDate,
            $cogsDescription,
            $hpp,
            0,
            Sale::class,
            $sale->id
        );

        $this->createEntry(
            $inventory->id,
            $entryDate,
            $cogsDescription,
            0,
            $hpp,
            Sale::class,
            $sale->id
        );
    }

    public function recordPurchase(Purchase $purchase, Product $product, float $quantity): void
    {
        $entryDate = $this->toCarbonDate($purchase->invoice_date);
        $total = (float) $purchase->total_amount;

        $inventory = $this->getAccountByCode('1141');
        $accountsPayable = $this->getAccountByCode('2110');

        $description = "Purchase #{$purchase->invoice_number}";

        $this->createEntry(
            $inventory->id,
            $entryDate,
            $description,
            $total,
            0,
            Purchase::class,
            $purchase->id
        );

        $this->createEntry(
            $accountsPayable->id,
            $entryDate,
            $description,
            0,
            $total,
            Purchase::class,
            $purchase->id
        );
    }

    public function recordPayment(Payment $payment, AccountPayable $ap): void
    {
        $entryDate = $this->toCarbonDate($payment->payment_date);
        $amount = (float) $payment->amount;

        $accountsPayable = $this->getAccountByCode('2110');
        $cash = $this->getAccountByCode('1110');

        $description = "Payment #{$payment->payment_number}";

        $this->createEntry(
            $accountsPayable->id,
            $entryDate,
            $description,
            $amount,
            0,
            Payment::class,
            $payment->id
        );

        $this->createEntry(
            $cash->id,
            $entryDate,
            $description,
            0,
            $amount,
            Payment::class,
            $payment->id
        );
    }

    public function recordReceipt(Receipt $receipt, AccountReceivable $ar): void
    {
        $entryDate = $this->toCarbonDate($receipt->receipt_date);
        $amount = (float) $receipt->amount;

        $cash = $this->getAccountByCode('1110');
        $accountsReceivable = $this->getAccountByCode('1130');

        $description = "Receipt #{$receipt->receipt_number}";

        $this->createEntry(
            $cash->id,
            $entryDate,
            $description,
            $amount,
            0,
            Receipt::class,
            $receipt->id
        );

        $this->createEntry(
            $accountsReceivable->id,
            $entryDate,
            $description,
            0,
            $amount,
            Receipt::class,
            $receipt->id
        );
    }

    private function toCarbonDate(mixed $date): CarbonInterface
    {
        if ($date instanceof CarbonInterface) {
            return $date;
        }

        return Carbon::parse($date);
    }
}
