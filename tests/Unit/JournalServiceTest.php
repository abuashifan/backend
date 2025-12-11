<?php

namespace Tests\Unit;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\JournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_purchase_posts_balanced_journal_entries(): void
    {
        // create required chart of accounts used by recordPurchase
        ChartOfAccount::create(['code' => '1141', 'name' => 'Inventory', 'type' => 'asset', 'parent_id' => null, 'is_active' => true]);
        ChartOfAccount::create(['code' => '2110', 'name' => 'Accounts Payable', 'type' => 'liability', 'parent_id' => null, 'is_active' => true]);

        $product = Product::factory()->create(['cost_price' => 1000]);

        $purchase = Purchase::factory()->create([
            'invoice_number' => 'PUR-TEST-1',
            'invoice_date' => now(),
            'subtotal' => 1000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 1000,
        ]);

        /** @var JournalService $journal */
        $journal = app(JournalService::class);

        $journal->recordPurchase($purchase, $product, 1);

        $entries = JournalEntry::where('reference_type', Purchase::class)
            ->where('reference_id', $purchase->id)
            ->get();

        $this->assertCount(2, $entries);

        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        $this->assertEquals($totalDebit, $totalCredit, 'Journal entries are not balanced');
    }
}
