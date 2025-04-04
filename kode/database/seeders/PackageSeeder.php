<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $package             = Package::firstOrNew(['is_free' => StatusEnum::true->status()]);
        $package->title      = "Free";
        $package->slug       = "free";
        $package->is_free    = StatusEnum::true->status();
        $package->save();
    }
}
