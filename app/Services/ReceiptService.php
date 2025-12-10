<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountReceivable;
use App\Models\Receipt;
use App\Services\JournalService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    public function __construct(
        private readonly JournalService $journalService,
    ) {
    }

    public function receiveAccountReceivable(int $accountReceivableId, float $amount, array $extraData = []): Receipt
    {
        $accountReceivable = AccountReceivable::findOrFail($accountReceivableId);

        if ($amount <= 0) {
            throw new DomainException('Receipt amount must be greater than zero.');
        }

        if ($amount > (float) $accountReceivable->remaining_amount) {
            throw new DomainException('Receipt amount cannot exceed the remaining balance.');
        }

        return DB::transaction(function () use ($accountReceivable, $amount, $extraData) {
            $receipt = Receipt::create([
                'customer_id' => $accountReceivable->customer_id,
                'accounts_receivable_id' => $accountReceivable->id,
                'receipt_number' => 'RCV-' . uniqid(),
                'receipt_date' => Carbon::parse($extraData['receipt_date'] ?? now()),
                'amount' => $amount,
                'method' => $extraData['method'] ?? 'cash',
                'notes' => $extraData['notes'] ?? null,
            ]);

            $accountReceivable->remaining_amount = (float) $accountReceivable->remaining_amount - $amount;

            if ($accountReceivable->remaining_amount <= 0.005) {
                $accountReceivable->remaining_amount = 0;
                $accountReceivable->status = 'paid';
            } else {
                $accountReceivable->status = 'partial';
            }

            $accountReceivable->save();

            $this->journalService->recordReceipt($receipt, $accountReceivable);

            return $receipt;
        });
    }
}
