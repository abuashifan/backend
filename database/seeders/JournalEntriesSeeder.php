<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Receipt;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class JournalEntriesSeeder extends Seeder
{
    public function run(): void
    {
        $cashAccount = ChartOfAccount::whereIn('code', ['1110', '1111', '1112', '1121', '1122'])->first();
        $accountsReceivableAccount = ChartOfAccount::where('code', '1130')->first();
        $accountsPayableAccount = ChartOfAccount::where('code', '2110')->first();
        $revenueAccount = ChartOfAccount::where('code', '4110')->first();
        $expenseAccount = ChartOfAccount::whereIn('code', ['5110', '5210', '5220', '5230', '5240', '5310'])->first();

        if (! $cashAccount || ! $revenueAccount || ! $expenseAccount) {
            return;
        }

        $referenceModels = [
            Sale::class,
            Purchase::class,
            Payment::class,
            Receipt::class,
            AccountReceivable::class,
            AccountPayable::class,
        ];

        for ($i = 0; $i < 25; $i++) {
            $entryType = fake()->randomElement(['cash_sale', 'ap_payment', 'expense_payment']);
            $amount = fake()->numberBetween(100, 1000);
            $entryDate = now()->subDays(fake()->numberBetween(0, 60));

            [$referenceType, $referenceId] = $this->getRandomReference($referenceModels);

            switch ($entryType) {
                case 'cash_sale':
                    JournalEntry::create([
                        'entry_date' => $entryDate,
                        'chart_of_account_id' => $cashAccount->id,
                        'description' => 'Cash sale receipt',
                        'debit' => $amount,
                        'credit' => 0,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);

                    if ($revenueAccount) {
                        JournalEntry::create([
                            'entry_date' => $entryDate,
                            'chart_of_account_id' => $revenueAccount->id,
                            'description' => 'Sales revenue recognition',
                            'debit' => 0,
                            'credit' => $amount,
                            'reference_type' => $referenceType,
                            'reference_id' => $referenceId,
                        ]);
                    }

                    break;
                case 'ap_payment':
                    if (! $accountsPayableAccount) {
                        break;
                    }

                    JournalEntry::create([
                        'entry_date' => $entryDate,
                        'chart_of_account_id' => $accountsPayableAccount->id,
                        'description' => 'Payment of accounts payable',
                        'debit' => $amount,
                        'credit' => 0,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);

                    JournalEntry::create([
                        'entry_date' => $entryDate,
                        'chart_of_account_id' => $cashAccount->id,
                        'description' => 'Cash disbursement',
                        'debit' => 0,
                        'credit' => $amount,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);

                    break;
                case 'expense_payment':
                    JournalEntry::create([
                        'entry_date' => $entryDate,
                        'chart_of_account_id' => $expenseAccount->id,
                        'description' => 'Payment of expense',
                        'debit' => $amount,
                        'credit' => 0,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);

                    JournalEntry::create([
                        'entry_date' => $entryDate,
                        'chart_of_account_id' => $cashAccount->id,
                        'description' => 'Cash out for expense',
                        'debit' => 0,
                        'credit' => $amount,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);

                    break;
            }
        }
    }

    private function getRandomReference(array $referenceModels): array
    {
        if (fake()->boolean(40)) {
            $modelClass = fake()->randomElement($referenceModels);
            $model = $modelClass::inRandomOrder()->first();

            if ($model) {
                return [strtolower(class_basename($modelClass)), $model->id];
            }
        }

        return [null, null];
    }
}
