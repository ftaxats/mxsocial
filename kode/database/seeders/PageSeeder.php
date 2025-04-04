<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Admin\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        collect([
                "terms-and-conditions",
                "cookies-policy",
                "privacy-policy"
           ])->except(Page::pluck('slug')->toArray())
            ->each(fn(string $title ,int $index): Page=>
                       Page::create([
                            'title'       => k2t($title),
                            'serial_id'   => $index,
                            'description' => $title,
                            'show_in_footer' => StatusEnum::true->status(),
                            'slug'        => $title])
                    );
                
    }
}
