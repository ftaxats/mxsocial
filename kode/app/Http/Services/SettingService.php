<?php
namespace App\Http\Services;

use Illuminate\Http\File;

use Illuminate\Support\Str;
use App\Models\Core\Setting;
use App\Enums\StatusEnum;
use App\Models\Core\File as CoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Traits\Fileable;
use Illuminate\Validation\Rule;
class SettingService 
{

    use Fileable;

    /**
     * update  settings
     * @param array $request_data
     */
    public function updateSettings(array $request_data) :void {
       

        $json_keys = Arr::get(config('settings'),'json_object' ,[]);
        
        foreach(($request_data) as $key=>$value){

            if(in_array($key , $json_keys)){
                $value = json_encode($value);
            }
            
            try {
                Setting::updateOrInsert(
                    ['key'    => $key],
                    ['value'  => $value]
                );
            } catch (\Throwable $th) {
                
            }
        }

        Cache::forget('site_settings');
     
    }


    /**
     * logo settings
     *
     * @param Request $request
     * @return void
     */
    public function logoSettings(array $request) :void{
    

        $logoSections =  Arr::get(config('settings'),'logo_keys' ,[]);

        foreach($logoSections as $key){
            
            if(isset($request['site_settings'][$key]) && is_file($request['site_settings'][$key]->getPathname())){
                $setting   = Setting::with('file')
                                     ->where('key',$key)
                                     ->first();
                if(!$setting){
                    $setting = Setting::create([
                        "key" => $key
                    ]);
                }
                $oldFile   = $setting->file()?->where('type',$key)->first();

                $response  = $this->storeFile(
                    file        : $request['site_settings'][$key], 
                    location    : config("settings")['file_path'][$key]['path'],
                    removeFile  : $oldFile ?? $oldFile
                );

                if(isset($response['status'])){

                    $image = new CoreFile([
                        'name'      => Arr::get($response, 'name', '#'),
                        'disk'      => Arr::get($response, 'disk', 'local'),
                        'type'      => $key,
                        'size'      => Arr::get($response, 'size', ''),
                        'extension' => Arr::get($response, 'extension', ''),
                    ]);
                    $setting->value = Arr::get( $response ,'name',"#");
                    $setting->save();
                    $setting->file()->save($image);
                }
          
            }
        }
        Cache::forget('site_logos');

    }


    /**
     * settings validations
     * @return array
     */
    public function validationRules(array $request_data ,string $key = 'site_settings') :array{

        $rules      = [];
        $message    = [];

        $numreicKey = ["expired_data_delete_after","pagination_number",'vistors','web_route_rate_limit','api_route_rate_limit' ,'max_login_attemtps','otp_expired_in','default_max_result','ai_result_length'];

        foreach(array_keys($request_data) as $data){
        
            if(in_array($data ,$numreicKey)){

                $rules[$key.".".$data] =  $data == "default_max_result" ? ['required','numeric','gt:-2','max:50000'] :['required','numeric','gt:0','max:50000'];
            }
            else{
                $rules[$key.".".$data] = ['required'];
            }
      
            $message[$key.".".$data.'.required'] = ucfirst(str_replace('_',' ',$data)).' '.translate('Feild is Required');
        }


        return [
            
            'rules'   => $rules,
            'message' => $message
        ];
    }

    /**
     * Update Status
     *
     * @param $request
     * @return array
     */
    public function statusUpdate(Request $request) :array{


        $request->validate([
            "key"              => ["required","max:155"],
            "status"           => ["required",Rule::in(StatusEnum::toArray())],
        ]);

        if( $request->input('key') =='force_ssl' &&   $request->input("status") == (StatusEnum::true)->status()  && !$request->secure()){

            return [
                'status'  => false,
                'message' => translate('Your request is not secure!! to enable this module'),
            ];

        }

        $response['status']    = false;
        $response['message']   = translate('Some thing went wrong!!');

        try {
          
            Setting::updateOrInsert(
                ['key'   => $request->input('key')],
                ['value' =>  $request->input("status")]
            );
    
            if($request->input('key') == 'app_debug'){
                if($request->input("status") ==  (StatusEnum::true)->status()){
                    update_env('APP_DEBUG',"true");
                }
                else{
                    update_env('APP_DEBUG',"false");
                }
            }
         
            $response['status']  = true;
            $response['message'] = translate('Status Updated Successfully');
  
        } catch (\Exception $ex) {
            $response['message']  = $ex->getMessage();
        }

        Cache::forget('site_settings');
        return $response;
    }



    /**
     * 
     * @param $request
     * 
     */
    public function customPrompt(Request $request ,string $key = "ticket_settings") :array{

        $status             =  false;
        $promptInputs       = [];
        foreach ($request->input('custom_inputs') as $index => $field) {
            $newField = $field;
            if (is_null($field['name'])) {
                $newField['name'] = t2k($newField['labels']);
            }
            $promptInputs[$index] = $newField;
        }

        $request->merge(['custom_inputs' => $promptInputs]);

        try {
            $status   =  true;
            $message  =  translate("Setting has been updated");
         
            Setting::updateOrInsert(
                ['key'   =>  $key],
                ['value' =>  json_encode($promptInputs)]
            );
    
          } catch (\Exception $exception) {
     
            $message = $exception->getMessage();
         }

         return [
            'status'=>  $status,
            'message'=>  $message,
         ];
    }
    

}