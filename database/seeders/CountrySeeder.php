<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // Países com VIES (União Europeia)
            [
                'code' => 'PT',
                'name' => 'Portugal',
                'name_en' => 'Portugal',
                'iso3' => 'PRT',
                'numeric_code' => 620,
                'phone_prefix' => '351',
                'vies_enabled' => true,
                'vat_formats' => ['999999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Lisbon',
                'active' => true
            ],
            [
                'code' => 'ES',
                'name' => 'Espanha',
                'name_en' => 'Spain',
                'iso3' => 'ESP',
                'numeric_code' => 724,
                'phone_prefix' => '34',
                'vies_enabled' => true,
                'vat_formats' => ['A99999999', '99999999A', 'A9999999A'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Madrid',
                'active' => true
            ],
            [
                'code' => 'FR',
                'name' => 'França',
                'name_en' => 'France',
                'iso3' => 'FRA',
                'numeric_code' => 250,
                'phone_prefix' => '33',
                'vies_enabled' => true,
                'vat_formats' => ['99999999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Paris',
                'active' => true
            ],
            [
                'code' => 'DE',
                'name' => 'Alemanha',
                'name_en' => 'Germany',
                'iso3' => 'DEU',
                'numeric_code' => 276,
                'phone_prefix' => '49',
                'vies_enabled' => true,
                'vat_formats' => ['999999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Berlin',
                'active' => true
            ],
            [
                'code' => 'IT',
                'name' => 'Itália',
                'name_en' => 'Italy',
                'iso3' => 'ITA',
                'numeric_code' => 380,
                'phone_prefix' => '39',
                'vies_enabled' => true,
                'vat_formats' => ['99999999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Rome',
                'active' => true
            ],
            [
                'code' => 'NL',
                'name' => 'Países Baixos',
                'name_en' => 'Netherlands',
                'iso3' => 'NLD',
                'numeric_code' => 528,
                'phone_prefix' => '31',
                'vies_enabled' => true,
                'vat_formats' => ['999999999B99'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Amsterdam',
                'active' => true
            ],
            [
                'code' => 'BE',
                'name' => 'Bélgica',
                'name_en' => 'Belgium',
                'iso3' => 'BEL',
                'numeric_code' => 56,
                'phone_prefix' => '32',
                'vies_enabled' => true,
                'vat_formats' => ['0999999999', '1999999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Brussels',
                'active' => true
            ],
            [
                'code' => 'AT',
                'name' => 'Áustria',
                'name_en' => 'Austria',
                'iso3' => 'AUT',
                'numeric_code' => 40,
                'phone_prefix' => '43',
                'vies_enabled' => true,
                'vat_formats' => ['U99999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Vienna',
                'active' => true
            ],
            [
                'code' => 'IE',
                'name' => 'Irlanda',
                'name_en' => 'Ireland',
                'iso3' => 'IRL',
                'numeric_code' => 372,
                'phone_prefix' => '353',
                'vies_enabled' => true,
                'vat_formats' => ['9S99999L', '999999WI', '9999999WI'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Dublin',
                'active' => true
            ],
            [
                'code' => 'LU',
                'name' => 'Luxemburgo',
                'name_en' => 'Luxembourg',
                'iso3' => 'LUX',
                'numeric_code' => 442,
                'phone_prefix' => '352',
                'vies_enabled' => true,
                'vat_formats' => ['99999999'],
                'currency_code' => 'EUR',
                'timezone' => 'Europe/Luxembourg',
                'active' => true
            ],

            // Países sem VIES mas relevantes
            [
                'code' => 'US',
                'name' => 'Estados Unidos',
                'name_en' => 'United States',
                'iso3' => 'USA',
                'numeric_code' => 840,
                'phone_prefix' => '1',
                'vies_enabled' => false,
                'vat_formats' => null,
                'currency_code' => 'USD',
                'timezone' => 'America/New_York',
                'active' => true
            ],
            [
                'code' => 'GB',
                'name' => 'Reino Unido',
                'name_en' => 'United Kingdom',
                'iso3' => 'GBR',
                'numeric_code' => 826,
                'phone_prefix' => '44',
                'vies_enabled' => false,
                'vat_formats' => ['999999999', '999999999999', 'GD999', 'HA999'],
                'currency_code' => 'GBP',
                'timezone' => 'Europe/London',
                'active' => true
            ],
            [
                'code' => 'BR',
                'name' => 'Brasil',
                'name_en' => 'Brazil',
                'iso3' => 'BRA',
                'numeric_code' => 76,
                'phone_prefix' => '55',
                'vies_enabled' => false,
                'vat_formats' => ['99999999999999'],
                'currency_code' => 'BRL',
                'timezone' => 'America/Sao_Paulo',
                'active' => true
            ],
            [
                'code' => 'CH',
                'name' => 'Suíça',
                'name_en' => 'Switzerland',
                'iso3' => 'CHE',
                'numeric_code' => 756,
                'phone_prefix' => '41',
                'vies_enabled' => false,
                'vat_formats' => ['999999'],
                'currency_code' => 'CHF',
                'timezone' => 'Europe/Zurich',
                'active' => true
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
