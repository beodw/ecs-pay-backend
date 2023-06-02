<?php

namespace App\SMS;
use Illuminate\Http\Request;
use App\Models\CountryCode;

use Twilio\Rest\Client;

/**
 * Sends sms to user using Twilio's programmable sms client
 * @param String $message Body of sms
 * @param Number $recipients string or array of phone number of recepient
 */
class SmsOtpManager{
    public function sendMessage($message, $recipient)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipient, 
                ['from' => $twilio_number, 'body' => $message] );
    }

     public function sendSms(Request $request, $message){        
        $country_phone_details = CountryCode::where("code", $request->country_code)->first();
        
        //add country code to phone number string.
        $phone_number = "".$country_phone_details['dial_code']; 
        
        // concatenate number without inital zero.
        $phone_number = $phone_number.$request->whatsapp_number;

        $this->sendMessage($message, $phone_number);
    }

}
