<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Blog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            "we-launch-pulsar-template-this-week",
            "template-this-week",
            "AI-content",
            "social-posting",
            "post-management",
       ])->except(Blog::pluck('slug')->toArray())
        ->each(fn(string $title ,int $index): Blog=>
                Blog::create([
                                'title'       => k2t($title),
                                'description' => 'description',
                                'is_feature' => StatusEnum::true->status(),
                                'slug'        => $title])
                );
    }
}
