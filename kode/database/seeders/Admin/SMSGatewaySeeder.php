<?php

namespace Database\Seeders\Admin;

use App\Enums\Gateway\SMSGatewayEnum;
use App\Models\Admin\SmsGateway;
use Illuminate\Database\Seeder;


class SMSGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        collect(SMSGatewayEnum::getGatewayCredential())
            ->except(SmsGateway::pluck('code')->toArray())
            ->each(fn(array $gateway , string $code):SmsGateway => 
                SmsGateway::firstOrCreate(['code' =>  $code ],$gateway)
        );
       
    }
}
