<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Admin\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $currency                      = Currency::firstOrNew(['base' => StatusEnum::true->status()]);
        $currency->default             = StatusEnum::true->status();
        $currency->name                = "Us Dollar";
        $currency->code                = "USD";
        $currency->symbol              = "$";
        $currency->exchange_rate       = 1;
        $currency->save();
        
    }
}
