<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $assets = ChartOfAccount::create([
            'code' => '1000',
            'name' => 'Assets',
            'type' => 'asset',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $currentAssets = ChartOfAccount::create([
            'code' => '1100',
            'name' => 'Current Assets',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'is_active' => true,
        ]);

        $cash = ChartOfAccount::create([
            'code' => '1110',
            'name' => 'Cash',
            'type' => 'asset',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1111',
            'name' => 'Petty Cash',
            'type' => 'asset',
            'parent_id' => $cash->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1112',
            'name' => 'Cash On Hand',
            'type' => 'asset',
            'parent_id' => $cash->id,
            'is_active' => true,
        ]);

        $bank = ChartOfAccount::create([
            'code' => '1120',
            'name' => 'Bank',
            'type' => 'asset',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1121',
            'name' => 'Bank Giro',
            'type' => 'asset',
            'parent_id' => $bank->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1122',
            'name' => 'Bank Savings',
            'type' => 'asset',
            'parent_id' => $bank->id,
            'is_active' => true,
        ]);

        $accountsReceivable = ChartOfAccount::create([
            'code' => '1130',
            'name' => 'Accounts Receivable',
            'type' => 'asset',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1131',
            'name' => 'Allowance for Doubtful Accounts',
            'type' => 'asset',
            'parent_id' => $accountsReceivable->id,
            'is_active' => true,
        ]);

        $inventory = ChartOfAccount::create([
            'code' => '1140',
            'name' => 'Inventory',
            'type' => 'asset',
            'parent_id' => $currentAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1141',
            'name' => 'Merchandise Inventory',
            'type' => 'asset',
            'parent_id' => $inventory->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1142',
            'name' => 'Raw Material Inventory',
            'type' => 'asset',
            'parent_id' => $inventory->id,
            'is_active' => true,
        ]);

        $fixedAssets = ChartOfAccount::create([
            'code' => '1200',
            'name' => 'Fixed Assets',
            'type' => 'asset',
            'parent_id' => $assets->id,
            'is_active' => true,
        ]);

        $fixedAssetsMain = ChartOfAccount::create([
            'code' => '1210',
            'name' => 'Fixed Assets',
            'type' => 'asset',
            'parent_id' => $fixedAssets->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1211',
            'name' => 'Equipment',
            'type' => 'asset',
            'parent_id' => $fixedAssetsMain->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1212',
            'name' => 'Vehicles',
            'type' => 'asset',
            'parent_id' => $fixedAssetsMain->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1213',
            'name' => 'Buildings',
            'type' => 'asset',
            'parent_id' => $fixedAssetsMain->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '1220',
            'name' => 'Accumulated Depreciation',
            'type' => 'asset',
            'parent_id' => $fixedAssets->id,
            'is_active' => true,
        ]);

        $liabilities = ChartOfAccount::create([
            'code' => '2000',
            'name' => 'Liabilities',
            'type' => 'liability',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $currentLiabilities = ChartOfAccount::create([
            'code' => '2100',
            'name' => 'Current Liabilities',
            'type' => 'liability',
            'parent_id' => $liabilities->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '2110',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'parent_id' => $currentLiabilities->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '2120',
            'name' => 'Tax Payable',
            'type' => 'liability',
            'parent_id' => $currentLiabilities->id,
            'is_active' => true,
        ]);

        $longTermLiabilities = ChartOfAccount::create([
            'code' => '2200',
            'name' => 'Long-term Liabilities',
            'type' => 'liability',
            'parent_id' => $liabilities->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '2210',
            'name' => 'Bank Loan Long-term',
            'type' => 'liability',
            'parent_id' => $longTermLiabilities->id,
            'is_active' => true,
        ]);

        $equity = ChartOfAccount::create([
            'code' => '3000',
            'name' => 'Equity',
            'type' => 'equity',
            'parent_id' => null,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '3100',
            'name' => 'Owner Capital',
            'type' => 'equity',
            'parent_id' => $equity->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '3200',
            'name' => 'Retained Earnings',
            'type' => 'equity',
            'parent_id' => $equity->id,
            'is_active' => true,
        ]);

        $revenue = ChartOfAccount::create([
            'code' => '4000',
            'name' => 'Revenue',
            'type' => 'revenue',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $operatingRevenue = ChartOfAccount::create([
            'code' => '4100',
            'name' => 'Operating Revenue',
            'type' => 'revenue',
            'parent_id' => $revenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '4110',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '4120',
            'name' => 'Service Revenue',
            'type' => 'revenue',
            'parent_id' => $operatingRevenue->id,
            'is_active' => true,
        ]);

        $otherRevenue = ChartOfAccount::create([
            'code' => '4200',
            'name' => 'Other Revenue',
            'type' => 'revenue',
            'parent_id' => $revenue->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '4210',
            'name' => 'Interest Income',
            'type' => 'revenue',
            'parent_id' => $otherRevenue->id,
            'is_active' => true,
        ]);

        $expenses = ChartOfAccount::create([
            'code' => '5000',
            'name' => 'Expenses',
            'type' => 'expense',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $cogs = ChartOfAccount::create([
            'code' => '5100',
            'name' => 'COGS',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5110',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'parent_id' => $cogs->id,
            'is_active' => true,
        ]);

        $operatingExpenses = ChartOfAccount::create([
            'code' => '5200',
            'name' => 'Operating Expenses',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5210',
            'name' => 'Salary Expense',
            'type' => 'expense',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5220',
            'name' => 'Utilities Expense',
            'type' => 'expense',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5230',
            'name' => 'Rent Expense',
            'type' => 'expense',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5240',
            'name' => 'Depreciation Expense',
            'type' => 'expense',
            'parent_id' => $operatingExpenses->id,
            'is_active' => true,
        ]);

        $otherExpenses = ChartOfAccount::create([
            'code' => '5300',
            'name' => 'Other Expenses',
            'type' => 'expense',
            'parent_id' => $expenses->id,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'code' => '5310',
            'name' => 'Interest Expense',
            'type' => 'expense',
            'parent_id' => $otherExpenses->id,
            'is_active' => true,
        ]);
    }
}
