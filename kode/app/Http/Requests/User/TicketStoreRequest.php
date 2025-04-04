<?php

namespace App\Http\Requests\User;

use App\Enums\StatusEnum;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validation  =  $this->get_validation(request()->except(['_token']));
        return $validation ['rules'];
    }



    /**
     * get validation message
     *
     * @return array
     */
    public function messages() :array
    {
        $validation  =  $this->get_validation(request()->except(['_token']));
        return $validation ['message'];
    }




    /**
     * get validation rules and mes
     *
     * @param array $request_data
     * @return array
     */
    public function get_validation(array $request_data) :array{

        $rules = [];
        $message = [];
        $ticket_fields = json_decode(site_settings('ticket_settings'),true);
        foreach( $ticket_fields as $fields){
            if($fields['required'] == StatusEnum::true->status()){
                if($fields['type'] == 'file'){
                    $rules['ticket_data.'.$fields['name'].".*"] = ['required', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true),'Ticket File')];
                }
                if($fields['type'] == 'email'){
                    $rules['ticket_data.'.$fields['name']] = ['required','email'];
                    $message['ticket_data.'.$fields['name'].".email"] = ucfirst($fields['name']).translate(' Feild Is Must Be Contain a Valid Email');
                }
                else{
                    $rules['ticket_data.'.$fields['name']] = ['required'];
                }
                $message['ticket_data.'.$fields['name'].".required"] = ucfirst($fields['name']).translate(' Feild Is Required');
            }
        }
       
        return  [
            'rules' => $rules,
            'message' => $message,
        ];
    }
}
