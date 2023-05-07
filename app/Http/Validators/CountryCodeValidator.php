<?php

namespace App\Http\Validators;

use Illuminate\Validation\Validator;

use \Symfony\Component\Intl\Countries;

class CountryCodeValidator
{
     public function isValidCode ($attribute, $value, $fail) {
        $validCountryCodes = Countries::getNames();
        if (!isset($validCountryCodes[$value])) {
            $fail('The country code is invalid.');
        }
    }
}