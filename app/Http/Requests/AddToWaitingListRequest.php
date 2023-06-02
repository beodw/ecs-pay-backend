<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToWaitingListRequest extends FormRequest
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
            "email" => "email",
            "whatsapp_number" => [
                'regex:/^\d{9,15}$/',
            ],
            'country_code' => 'required_unless:phone,==,null',
        ];
    }
}
