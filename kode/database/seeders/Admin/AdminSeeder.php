<?php

namespace Database\Seeders\Admin;

use App\Enums\StatusEnum;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Admin::firstOrCreate(['super_admin' => StatusEnum::true->status()],[
                'username'          => 'admin',
                'name'              => 'superadmin',
                'phone'             => '011111111',
                'email'             => 'admin@gmail.com',
                "email_verified_at" =>  Carbon::now(),
                "password"          =>  '123123',
            ]);
    }
}
