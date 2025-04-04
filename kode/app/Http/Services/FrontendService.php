<?php
namespace App\Http\Services;



use Illuminate\Http\Request;

use App\Traits\Fileable;
use App\Models\Admin\Frontend;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
class FrontendService
{

    use Fileable ,ModelAction;
    


     public function save (Request $request) :array {


        $response = response_status('Saved successfully');
        
        try {
            DB::transaction(function() use ($request) {

                switch (true) {
                    case !$request->input('id') && $request->input('type') == 'element':
                                    $frontend      = new Frontend();
                                    $frontend->key =  $request->input('type') . "_" . $request->input('key');
                        break;
                    
                    default:
                           
                            $frontend = Frontend::with(['file'])
                                                        ->when($request->input('id'),
                                                            fn (Builder $query) :Frontend  => $query->find($request->input('id'))
                                                            ,fn (Builder $query)  :Frontend => $query->firstOrNew(['key' => $request->input('type') . "_" . $request->input('key'),"parent_id" => $request->input('parent_id')]));
                        break;
                }


                $frontend->value     = $request->except(['_token', 'key', 'id', 'type', 'image_input','files','parent_id']);
                $frontend->parent_id = ($request->input('parent_id') &&  $request->input('parent_id') !='') ?  $request->input('parent_id') : null;
                $frontend->save();

                if($request->input('image_input')){

                    collect($request->input('image_input'))->map(function(\Illuminate\Http\UploadedFile $file,string $key) use($frontend){
                            $oldFile = $frontend->file()->where('type', $key)->first();
                            $this->saveFile($frontend ,$this->storeFile(
                                file       : $file, 
                                location   : config("settings")['file_path']['frontend']['path'],
                                removeFile : @$oldFile
                            )
                            ,$key);
                    });
                

                }
           });
        } catch (\Exception $ex) {

            $response = response_status(strip_tags($ex->getMessage()),"error");
        }

        return $response;



     }

}
