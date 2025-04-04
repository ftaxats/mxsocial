<?php

namespace App\Http\Requests;

use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class KycRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $validation  =  $this->get_validation(request()->except(['_token']));
        return  $validation ['rules'];
    }




    public function messages() :array
    {
     
        $validation  =  $this->get_validation(request()->except(['_token']));

        return $validation ['message'];
    }


    public function get_validation(array $request_data) :array{

        $rules = [];
        $message = [];

        $kycSettings     = !is_array(site_settings('kyc_settings',[])) ?  json_decode(site_settings('kyc_settings',[]),true) : [];

        foreach( $kycSettings as $fields){
                $required =null;
                if($fields['required'] == '1'){
                   $required ="required";
                }
                if($fields['type'] == 'file'){
                    $rules['kyc_data.files.'.$fields['name']] = [$required, new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))];
                }
                elseif($fields['type'] == 'email'){
                    $rules['kyc_data.'.$fields['name']] = [$required,'email'];
                    $message['kyc_data.'.$fields['name'].".email"] = ucfirst($fields['name']).translate(' Feild Is Must Be Contain a Valid Email');
                }
                else{
                    $rules['kyc_data.'.$fields['name']] = [$required];
                }
                $message['kyc_data.'.$fields['name'].".required"] = ucfirst($fields['name']).translate(' Feild Is Required');
            
        }


       
        return  [
            'rules'   => $rules,
            'message' => $message,
        ] ;
    }
}
