<?php

namespace App\Http\Requests;

use App\Rules\isJson;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isSender();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "userId" => "required|string",
            "currencyId" => "required|string",
            "platformId" => "required|string",
            "amount" => "required|decimal:0,3",
            "recipient.name" => "required|string",
            "recipient.address" => "string",
            "recipient.phone" => "string",
            "recipient.country_code" => "required_with:recipient.phone|string|regex:/^[A-Z]{2}$/",
            "recipientCurrencyId" => "required|string",
            "platformDetails" => ["required", new isJson],
        ];
    }
}
