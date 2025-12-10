<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxesSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'Non Tax',
                'rate' => 0.00,
                'description' => 'No tax applied',
            ],
            [
                'name' => 'VAT 10%',
                'rate' => 10.00,
                'description' => 'Value Added Tax 10%',
            ],
            [
                'name' => 'VAT 11%',
                'rate' => 11.00,
                'description' => 'Value Added Tax 11%',
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::firstOrCreate(
                ['name' => $tax['name']],
                ['rate' => $tax['rate'], 'description' => $tax['description']]
            );
        }
    }
}
