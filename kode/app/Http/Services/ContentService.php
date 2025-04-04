<?php
namespace App\Http\Services;

use App\Enums\StatusEnum;
use App\Models\Content;
use App\Traits\ModelAction;
use Illuminate\Http\Request;

class ContentService
{

    use ModelAction;


    /**
     * store a content 
     *
     * @param Request $request
     * @return array
     */
    public function save(Request $request) :array{

        $content            =  new Content();
        $content->name      =  $request->input('name');
        $content->content   =  $request->input('content');
        $content->save();
        return response_status('Content created successfully');
      
    }



    /**
     * Update a content 
     *
     * @param Request $request
     * @param Content $content
     * @return void
     */
    public function update(Request $request , Content $content) :array{

        $content->name      =  $request->input('name');
        $content->content   =  $request->input('content');
        $content->save();
        return response_status('Content updated successfully');
    }


}
