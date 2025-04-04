<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;

class TranslationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $id,$model,$column,$title,$lang_code ,$message = '';
    public function __construct(string $column , string $lang_code =  null)
    {
        $this->column = $column;
        $this->lang_code = $lang_code;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){

        if(!isset($value['default'])){
            $this->message = translate( "Default ".ucfirst($this->column)." required");
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return  $this->message;
    }
}
