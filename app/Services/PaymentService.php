<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountPayable;
use App\Models\Payment;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly JournalService $journalService,
    ) {
    }

    public function payAccountPayable(int $accountPayableId, float $amount, array $extraData = []): Payment
    {
        $accountPayable = AccountPayable::findOrFail($accountPayableId);

        if ($amount <= 0) {
            throw new DomainException('Payment amount must be greater than zero.');
        }

        if ($amount > (float) $accountPayable->remaining_amount) {
            throw new DomainException('Payment amount cannot exceed the remaining balance.');
        }

        return DB::transaction(function () use ($accountPayable, $amount, $extraData) {
            $payment = Payment::create([
                'supplier_id' => $accountPayable->supplier_id,
                'accounts_payable_id' => $accountPayable->id,
                'payment_number' => 'PAY-' . uniqid(),
                'payment_date' => Carbon::parse($extraData['payment_date'] ?? now()),
                'amount' => $amount,
                'method' => $extraData['method'] ?? 'cash',
                'notes' => $extraData['notes'] ?? null,
            ]);

            $accountPayable->remaining_amount = (float) $accountPayable->remaining_amount - $amount;

            if ($accountPayable->remaining_amount <= 0.005) {
                $accountPayable->remaining_amount = 0;
                $accountPayable->status = 'paid';
            } else {
                $accountPayable->status = 'partial';
            }

            $accountPayable->save();

            $this->journalService->recordPayment($payment, $accountPayable);

            return $payment;
        });
    }
}
