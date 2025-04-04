<?php

namespace Database\Seeders\Admin;

use App\Enums\Gateway\MailGatewayEnum;
use App\Models\Admin\MailGateway;
use Illuminate\Database\Seeder;

class MailGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(MailGatewayEnum::getGatewayCredential())
        ->except(Mailgateway::pluck('code')->toArray())
        ->each(fn(array $gateway , string $code):Mailgateway => 
            Mailgateway::firstOrCreate(['code' =>  $code ],$gateway)
        );
    }
}
