<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\MediaPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        collect(Arr::get(config('settings'),'platforms' ,[]))->except(MediaPlatform::pluck('slug')->toArray())
                           ->each(fn(array $config,string $name): MediaPlatform=>
                                    MediaPlatform::create([
                                            "name"            =>  Arr::get($config,'name',$name),
                                            "slug"            => make_slug($name),
                                            "url"             => '@@',
                                            "description"     => '@@',
                                            "configuration"   => Arr::get($config,'credential',[]),
                                            "is_integrated"   => Arr::get($config,'is_integrated',StatusEnum::false->status()),
                                            "is_feature"      => Arr::get($config,'is_feature',StatusEnum::false->status()),
                                    ])
         );

      
    }
}
