<?php

namespace App\Traits;

use App\Enums\ActionType;
use App\Enums\StatusEnum;
use App\Models\Core\File;
use App\Models\ModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\Fileable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait ModelAction
{

    use Fileable;


    /**
     * Change a model status
     *
     * @param array $request
     * @param array $modelData
     * @return string
     */
    private function changeStatus(array $request ,array $actionData): string{

        $response['reload']   = Arr::get($actionData,'reload',true);
        $response['status']   = false;
        $response['message']  = trans('default.failed_to_update');
   

        try {
            $data = Arr::get($actionData,'model')::where(Arr::get($actionData,'find_by','uid'),Arr::get($request,'id'))
                   ->when(Arr::get($actionData,'recycle',false) ,
                         fn(Builder $q) :Builder => $q->withTrashed())->when(Arr::get($actionData,'user_id',null), 
                                fn(Builder $q) :Builder =>  $q->where('user_id',Arr::get($actionData,'user_id')))
                   ->firstOrfail();
            $data->{Arr::get($request,'column','status')} =  Arr::get($request,'status');
            $data->save();
            $response['status']  = true;
            $response['message'] = trans('default.updated_successfully');

           
        } catch (\Throwable $th) {
      
        }

        return json_encode($response);

    }




    protected function parseManualParameters() :array{
    
        $parameter = [];
        if (request()->has('field_name')) {
            for ($i = 0; $i < count(request()->field_name); $i++) {
                $arr = [];

                $label = @request()->field_label[$i] ??  request()->field_name[$i];

                if(@request()->instraction[$i]){
                    $arr['instraction'] =  request()->instraction[$i];  
                }

                $arr['field_name']             = t2k(request()->field_name[$i]);
                $arr['field_label']            = $label;
                $arr['type']                   = request()->type[$i];
                $arr['validation']             = request()->validation[$i];
                $parameter[$arr['field_name']] = $arr;
            }
        }
        return $parameter;
    }

    


    /**
     * Bulk action update/delete
     *
     * @param Request $request
     * @param array $actionData
     * @return array
     */
    private function bulkAction(Request $request,array $actionData): array{
        
        $type     = $request->get("type");
        $response = $this->getResponse($type);
        $bulkIds  = json_decode($request->input('bulk_id'), true);
        $request->merge(["bulk_id" =>$bulkIds]);

        $this->validateRequest($request,$actionData);

        $bulkIds  = $request->get('bulk_id');

        $model = Arr::get($actionData,'model')::whereIn('id', $bulkIds);
        $type = $request->get("type");

        switch($type) {
            case 'status':
            case 'is_feature':
            case 'is_blocked':
                $model->when(Arr::get($actionData, 'recycle', false), fn (Builder $q) :Builder => $q->withTrashed())
                    ->lazyById(100)
                    ->each->update([$request->input("type") => $request->input('value')]);
                break;
        
            default:
       
                $model->when(in_array($type, [ActionType::RESTORE->value,ActionType::FORCE_DELETE->value]),
                    fn (Builder $q) :Builder => $q->withTrashed())
                    ->withCount(Arr::get($actionData, 'with_count', []))
                    ->with(Arr::get($actionData, 'with', []))
                    ->cursor()
                    ->each(function (Model $record) use ($type,$actionData) :void {
                        switch ($type) {
                            case ActionType::RESTORE->value:
                                $record->restore();
                                break;
                            case ActionType::FORCE_DELETE->value:
                                $this->handleForceDelete($record, $actionData);
                                break;
                            default:
                                $this->handleDefaultDelete($record, $actionData);
                                break;
                        }
                    });
                break;
        }

        return $response;

    }




    /**
     * Validate bulk action request
     *
     * @param Request $request
     * @param array $actionData
     * @return void
     */
    public function validateRequest(Request $request,array $actionData): void{

        $tableName = Arr::get($actionData,'model',null)->getTable();

        $rules = [
            'bulk_id'    => ['array', 'required'],
            'bulk_id.*'  => ["required",'exists:'.$tableName.',id'],
            'type'       => ['required', Rule::in(['status', 'delete', 'restore',"force_delete","is_feature",'is_blocked'])],
            'value'      => [
                Rule::requiredIf(fn () :bool => in_array($request->get("type"),['status','is_feature','is_blocked'])),
                function (string $attribute, mixed $value, $fail) use ($request) {
                    if (in_array($request->get("type"),['status','is_feature','is_blocked']) && !in_array($value, StatusEnum::toArray())) $fail("The {$attribute} is invalid.");
                },
            ]
        ];

        $request->validate($rules);

    }



    /**
     * Get response
     *
     * @param string $type
     * @return array
     */
    public function getResponse(string $type): array {

    
        switch ($type) {
            case ActionType::RESTORE->value:
                $response = response_status('Items restored successfully');
                break;
            case ActionType::FORCE_DELETE->value:
                $response = response_status('Items without any related data have been permanently deleted');
                break;
            case  ActionType::DELETE->value:
                $response = response_status('Items without any related data have been deleted');
                break;
            default:
                $response = response_status('Items status updated successfully');
                break;
        }

        return $response;

    }


    /**
     * Force delete
     *
     * @param mixed $record
     * @param array $actionData
     * @return void
     */
    private function handleForceDelete(mixed $record, array $actionData) :void {
        if(isset($actionData['force_flag'])) $this->unlinkData($record , $actionData);
        $record->forceDelete();
    }
    
   

    /**
     * regular delete
     *
     * @param Model $record
     * @param array $actionData
     * @return void
     */
    private function handleDefaultDelete(Model $record, array $actionData): void{
        if (!in_array(true, array_map(fn (string $relation) :bool => 
        $record->{$relation . "_count"} > 0
        , Arr::get($actionData, 'with_count', [])))) {
             if(!isset($actionData['force_flag'])) $this->unlinkData($record , $actionData);
             $record->delete();
        }
    }




    /**
     * Unlink and delete relational data
     *
     * @param Model $record
     * @param array $modelData
     * @return void
     */
    private function unlinkData(Model $record , array $modelData): void{

        $fileTypes =  collect(Arr::get($modelData ,'file_unlink',[] ));
        $relations =  collect(Arr::get($modelData ,'with',[] ));


        //unlink files
        $fileTypes->each(fn(string $path , string $type ):bool =>
                            $record->file()->where('type',$type)->each(fn(File $file):bool =>
                                                               $this->unlink(location: $path,file:$file)));

        //delete data
        $relations->filter(fn(string $relation) : bool => $relation !=  'file'
                 )->each(function(string $relation) use ($record): void{
                            if($relation != 'file')  $record->{$relation}()->delete();
                        });   
                        
    
    }




    /**
     * Save seo 
     *
     * @param Model $model
     * @return void
     */
    public static function saveSeo(Model $model) :void {


        $model->fill(Arr::collapse(Arr::map(['meta_title', 'meta_description', 'meta_keywords'],fn (string $key): array =>
             [$key => request()->input($key)]
        )));
    }




    /**
     * Save a model File
     *
     * @param Model $model
     * @param array | null $data
     * @param string | null $type
     * @return void
     */
    private function saveFile(Model $model,? array $response  = null , ? string $type =  null): void{

        if(is_array($response) && Arr::has($response,'status')){
            $image = new File([
                'name'      => Arr::get($response, 'name', 'default'),
                'disk'      => Arr::get($response, 'disk', 'local'),
                'type'      => $type,
                'size'      => Arr::get($response, 'size', ''),
                'extension' => Arr::get($response, 'extension', ''),
            ]);
            $model->file()->save($image);
        }

    }


    /**
     * Save a model Translations
     *
     * @param Model $model
     * @param array $data
     * @param string $key
     * @return void
     */
    private function saveTranslation(Model $model ,array $data ,string $key): void{

        DB::transaction(function() use ($model ,$data , $key) {
            $model->translations()->where("key",$key)->delete();
            $translations = collect($data)
                                ->reject(fn (string | null $value, string $locale) : bool =>  !$value || $locale === 'default')
                                ->map(fn (string $value, string $locale) : ModelTranslation =>
                                     new ModelTranslation([
                                        'locale' => $locale,
                                        'key'    => $key,
                                        'value'  => $value,
                                     ])
                                );

            if (!empty($translations))  $model->translations()->saveMany($translations);
        });

    }
}