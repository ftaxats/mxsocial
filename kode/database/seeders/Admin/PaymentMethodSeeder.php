<?php

namespace Database\Seeders\Admin;

use App\Enums\Gateway\PaymentGatewayEnum;
use App\Enums\StatusEnum;
use App\Models\Admin\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ 
    public function run(): void
    {
       
        collect(PaymentGatewayEnum::getGatewayCredential())
        ->except(PaymentMethod::pluck('code')->toArray())
        ->each(function(array $gateway , string $code): void{
            PaymentMethod::withoutEvents(function() use($gateway, $code): void{
                $gateway['uid']         =  Str::uuid();
                $gateway['type']        =  StatusEnum::true->status();
                $gateway['created_by']  =  get_superadmin()->id;
                PaymentMethod::firstOrCreate(['code' =>  $code ],$gateway);
            });
        });
    }
}
