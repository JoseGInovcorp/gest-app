<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VatRate;

class VatRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vatRates = [
            [
                'name' => 'IVA Normal',
                'rate' => 23.00,
                'is_default' => true,
                'active' => true,
            ],
            [
                'name' => 'IVA Intermédio',
                'rate' => 13.00,
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'IVA Reduzido',
                'rate' => 6.00,
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'Isento',
                'rate' => 0.00,
                'is_default' => false,
                'active' => true,
            ],
        ];

        foreach ($vatRates as $vatRate) {
            VatRate::create($vatRate);
        }

        $this->command->info('✅ 4 taxas de IVA criadas com sucesso!');
    }
}
