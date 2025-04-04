<?php

namespace Database\Seeders\Admin;

use App\Enums\NotificationTemplateEnum;
use App\Models\Admin\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(NotificationTemplateEnum::notificationTemplateEnum())
        ->except(Template::pluck('slug')->toArray())
        ->each(function(array $template , string $slug): void{
            Template::withoutEvents(function() use($template, $slug): void{
                $template['uid']         =  Str::uuid();
                $template['created_by']  =  get_superadmin()->id;
                Template::firstOrCreate(['slug' =>  $slug ],$template);
            });
        });
    }
}
