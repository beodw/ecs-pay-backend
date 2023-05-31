<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CountryCode;
use App\Models\OTPCollection;
use App\Enums\UserRole;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\GenerateOtpRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTP;
use App\SMS\SmsOtpManager;

use Twilio\Exceptions\TwilioException;

class PassportAuthController extends Controller
{
    private function sendSms(Request $request){
        //Generate otp for sms.
        $otp = rand(1000,9999);

        //send sms otp
        $smsOtpManager = new SmsOtpManager();
        
        $country_phone_details = CountryCode::where("code", $request->country_code)->first();
        
        //add country code to phone number string.
        $phone_number = "".$country_phone_details['dial_code']; 
        
        // concatenate number without inital zero.
        $phone_number = $phone_number.substr($request->whatsapp_number, 1);

        $smsOtpManager->sendMessage("Thank you for using ECS Pay. This is your signup otp: $otp. If this request was not generated by you please reset your password as soon as possible.", $phone_number);

        // store otp for verification later
        OTPCollection::create(['otp'=> $otp, "phone" => $phone_number]);

        return $otp;
    }

    /**
    * @OA\Post(
    * path="/api/register",
    * operationId="Register",
    * tags={"Register"},
    * summary="User Registeration",
    * description="Register User Here",
    *   @OA\RequestBody(
    *    required=true,
    *    description="Pass user credentials",
    *    @OA\JsonContent(
    *       required={"password", "password_confirmation", "whatsappNumber", "countryCode", "user_name", "first_name", "last_name", "country"},
    *       @OA\Property(property="first_name", type="string", format="string", example="John"),
    *       @OA\Property(property="last_name", type="string", format="string", example="Doe"),
    *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
    *       @OA\Property(property="user_name", type="string", format="string", example="JohnD"),
    *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
    *       @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
    *       @OA\Property(property="whatsapp_number", format="phone", type="string", example="0502342345"),
    *       @OA\Property(property="country_code", type="string", example="GH"),
    *       @OA\Property(property="country", type="string", example="Ghana"),
    *       @OA\Property(property="town_city", type="string", example="Accra"),
    *    ),
    * ),
    *      @OA\Response(
    *          response=201,
    *          description="Registered Successfully",
    *          @OA\JsonContent()
    *       ),
    *      
    *    
    * )
    */
    public function register(RegisterUserRequest $request)
    {
        //Check if country code exists.
        $countryIsValid = CountryCode::where("code", $request->country_code)->firstOrFail(); 
        
        //Check if user with email and whatsapp number already exists.
        $existingUser = User::where("email", $request->email)
                            ->orWhere( "whatsapp_number", $request->whatsapp_number)
                            ->first();
        
        if($existingUser?->email != null && $existingUser?->email === $request->email){
            return response()->json(["Error" => "User with that email already exists."], 400);
        }

        if($existingUser?->whatsapp_number === $request->whatsapp_number && $existingUser?->country_code === $request->country_code){
            return response()->json(["Error" => "User with that whatsapp number already exists."], 400);
        }

        //find dial number for given country code.
        try {
            $country_phone_details = CountryCode::where("code", $request->country_code)->firstOrFail();
        }
        catch(Exception $e) {
            return response()->json(["Error" => "Invalid country code. Could not find a country with specified code."], 400);
        }
        
        //Check if phone matches country.
        if($request->country != $country_phone_details['name']){
            return response()->json(["Error" => "Phone number does not match selected country."], 400);
        }
        
        //add country code to phone number string.
        $phone_number = "".$country_phone_details['dial_code']; 
        
        // concatenate number without inital zero.
        $phone_number = $phone_number.substr($request->whatsapp_number, 1);

        //send sms otp
        try {
            $otp = $this->sendSms($request);
        }
        catch(TwilioException $e){
            return response()->json(["Error" => "Invalid phone number. Could not send otp via sms."], 400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_role' => UserRole::Sender,
            'whatsapp_number' => $request->whatsapp_number,
            'country_code' => $request->country_code,
            'is_verified' => false,
            'town_city' => $request->town_city,
            'country' => $request->country,
            'zip' =>  $request->zip
        ]);

        //To do store otp and link to user. Ensure it has been generated within 1 hr.

        if($request->email != null){
            //Send email otp.
            $email = [
                'user_name' => $request->user_name,
            ];
            Mail::to($request->email)->send(new OTP($email));
        }

        return response(["status" => 200, "message" => "OTP sent to $phone_number. Kindly, check your sms to verify."]);
    }


    /**
    * @OA\Post(
    * path="/api/verifyOtp",
    * operationId="VerifyOtp",
    * tags={"VerifyOtp"},
    * summary="Verify OTP",
    * description="Verify OTP",
    *   @OA\RequestBody(
    *    required=true,
    *    description="Verify user otp",
    *    @OA\JsonContent(
    *       required={"otp", "whatsappNumber", "countryCode"},
    *       @OA\Property(property="otp", type="string", format="string", example="9999"),
    *       @OA\Property(property="whatsapp_number", format="phone", type="string", example="0502342345"),
    *       @OA\Property(property="country_code", type="string", example="GH"),
    *    ),
    * ),
    * @OA\Response(
    *      response=201,
    *         description="Verified Successfully",
    *       @OA\JsonContent()
    *  ),
    *      
    *    
    * )
    */
    public function verifyOtp(VerifyOtpRequest $request){
       //find user with whatsapp and country code and otp

       $otp = OTPCollection::where("phone", "".$request->country_code.$request->whatsapp_number)
                        ->where("otp", $request->otp)
                        ->firstOrFail();
       $user = User::where("whatsapp_number", $request->whatsapp_number)
                        ->where("country_code", $request->country_code)
                        ->where("otp", $request->otp)
                        ->firstOrFail();
       //change user to verified
       $user['is_verified'] = true;
       $user->save();
       return response()->json(["Success" => $user['user_name']." has been successfully verified."],200);
    }

    /**
    * @OA\Post(
    * path="/api/generateOtp",
    * operationId="generateOtp",
    * tags={"Generate Otp"},
    * summary="Generate Otp",
    * description="Generate OTP",
    *   @OA\RequestBody(
    *    required=true,
    *    description="Pass user credentials",
    *    @OA\JsonContent(
    *       required={"whatsapp_number", "country_code"},
    *       @OA\Property(property="whatsapp_number", format="phone", type="string", example="0502342345"),
    *       @OA\Property(property="country_code", type="string", example="GH"),
    *    ),
    * ),
    *      @OA\Response(
    *          response=201,
    *          description="
    *               
    *                   'Success': 'Otp has been sent to +233502785489'
    *                   
    *          ",
    *          @OA\JsonContent()
    *       ),
    *      
    *    
    * )
    */
    public function generateOtp(GenerateOtpRequest $request){
        //find country code if exists.
        $phone_number = CountryCode::where("code", $request->country_code)->firstOrFail();
        //Check if whatsapp number belongs to a registered user.
        $user = User::where("whatsapp_number", $request->whatsapp_number, "country_code", $request->country_code)->firstOrFail();
        
        //send sms
        //Generate phone number with country code.
        $phone_number = $phone_number["dial_code"];
        $phone_number = $phone_number.substr($request->whatsapp_number, 1);
        try{
           $otp = $this->sendSms($request);
        }
        catch(Exception $e){
            return response()->json(["Error" => "Could not send otp to".$phone_number]);
        }

        OTPCollection::create(['otp' => $otp, "phone" => $phone_number]);

        return ["Success" => "Otp has been sent to ".$phone_number];
    }
 
    /**
    * @OA\Post(
    * path="/api/login",
    * operationId="authLogin",
    * tags={"Login"},
    * summary="User Login",
    * description="Login User Here",
    *   @OA\RequestBody(
    *    required=true,
    *    description="Pass user credentials",
    *    @OA\JsonContent(
    *       required={"password"},
    *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
    *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
    *       @OA\Property(property="whatsapp_number", format="phone", type="string", example="0502342345"),
    *       @OA\Property(property="country_code", type="string", example="GH"),
    *    ),
    * ),
    *      @OA\Response(
    *          response=201,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      
    *    
    * )
    */
    public function login(LoginRequest $request)
    { 
        $loginDetails = [];
        if($request->email){
            $loginDetails = [
                "password" => $request->password,
                "email" => $request->email,
            ];
        }
        else {
            $loginDetails = [
                "password" => $request->password,
                "whatsapp_number" => $request->whatsapp_number,
                "country_code" => $request->country_code,
            ];
        }
        $loginSuccess = auth()->attempt($loginDetails);

        if(!$loginSuccess){
            return response()->json(["Error" => "Login details are wrong. Check password and email/whatsappNumber"], 401);
        }

        $user = auth()->user();

        if(!$user['is_verified']){
            return response()->json(['Error' => "User is not verified. Kindly, use the OTP sent via sms and email to verify.", "user" => $user], 403);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);
        
    }   
}