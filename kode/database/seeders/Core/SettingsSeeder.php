<?php

namespace Database\Seeders\Core;

use App\Models\Core\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $settings =  collect(config('site_settings'))
        ->except(Setting::pluck('key')->toArray())
        ->map(fn(mixed $value , string $key): array =>
                 array(
                    'uid' => Str::uuid(),
                    'key' => $key,
                    'value' =>$value)
            )->values()->all();
        Setting::insert($settings);
        optimize_clear();
    }
}
