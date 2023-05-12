<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class isJson implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
         try{
            if(gettype($value) === "array"){
                if(!array_is_list($value)){
                    return true;
                }
            }
        }
        catch(Exception $e){
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ":attribute must be a json object.";
    }
}
