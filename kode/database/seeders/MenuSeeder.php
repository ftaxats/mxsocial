<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Admin\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        $sections = [];

        foreach(get_appearance(true,false) as $key => $appearance ){
            if (isset($appearance['builder']) && $appearance['builder'] && !@$appearance['no_selection']){
                $sections[] =  $key;  
            }
        }


        $menus = [

            "/" => [
                'name'     => "Home",
                'section'  =>  $sections ,
                'default'  =>  StatusEnum::true->status(),
            ],

            "contact" => ['name'     => "Contact"],

            "blogs" => ['name'     => "Blogs"],

            "plans" => ['name'     => "Plans"]

        ];
        $keys = Menu::pluck('url')->toArray();
        $serial = 0;
        foreach($menus as $key => $section){
            if(!in_array($key ,$keys )){
                Menu::create([
                    "url"          => $key,
                    "serial_id"    => $serial,
                    "name"         => Arr::get($section,"name",'home'),
                    "section"      => Arr::get($section,"section",[]),
                    "is_default"   => Arr::get($section,"default",StatusEnum::false->status()),
                    'meta_title'   => Arr::get($section,"name",'home'),
                ]);

                $serial ++;
              
            }
        }

    }
}
