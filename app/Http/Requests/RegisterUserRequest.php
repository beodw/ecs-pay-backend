<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Symfony\Component\Intl\Countries;

use App\Http\Validators\CountryCodeValidator;


class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_name' => 'required|min:4',
            'email' => 'email',
            'password' => 'required|confirmed|min:8',
            'whatsapp_number' => [
                'required',
                'regex:/^0\d{9,15}$/',
            ],
            'country_code' => 'required'
        ];
    }
}
