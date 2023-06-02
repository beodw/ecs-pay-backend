<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToWaitingListRequest;
use App\Models\WaitingListRequest;
use App\SMS\SmsOtpManager;
use App\Models\CountryCode;
use App\Mail\WaitingListEmail;
use Illuminate\Support\Facades\Mail;

class WaitingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
    * @OA\Post(
    * path="/api/waiting-list-registration",
    * operationId="AddToWaitingList",
    * tags={"WaitingList"},
    * summary="Waiting list for users",
    * description="Add user to waiting list",
    *   @OA\RequestBody(
    *    required=true,
    *    description="Pass user credentials",
    *    @OA\JsonContent(
    *       
    *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
    *       @OA\Property(property="whatsapp_number", format="phone", type="string", example="0502342345"),
    *       @OA\Property(property="country_code", type="string", example="GH"),
    *    ),
    * ),
    *      @OA\Response(
    *          response=201,
    *          description=" 'Success' => 'You have successfully been added to the waitlist.' ",
    *          @OA\JsonContent()
    *       ),
    *      
    *    
    * )
    */
    public function store(AddToWaitingListRequest $request)
    {
        if($request->email != null ){
            $waitingListRequest = WaitingListRequest::where("email", "=", $request->email)->first();
            if($waitingListRequest != null){
                return response()->json(["Error" => "This email has already beend used to sign up for the waiting list."], 409);
            }
            Mail::to($request->email)->send(new WaitingListEmail());
        }
        elseif ($request->whatsapp_number != null){
            $waitingListRequest = WaitingListRequest::where("phone", "=", $request->whatsapp_number)
                                                        ->where("country_code", "=", $request->country_code)
                                                        ->first();
            if($waitingListRequest != null){
                return response()->json(["Error" => "This email has already been used to sign up for the waiting list."], 409);
            }

            //find dial number for given country code.
            try {
                $country_phone_details = CountryCode::where("code", $request->country_code)->firstOrFail();
            }
            catch(Exception $e) {
                return response()->json(["Error" => "Invalid country code. Could not find a country with specified code."], 400);
            }
            
            //add country code to phone number string.
            $phone_number = "".$country_phone_details['dial_code']; 
            
            // concatenate number without inital zero.
            // $phone_number = $phone_number.substr($request->whatsapp_number, 1);

            //send sms otp
            try {
                $message = "Thank you for signing up to the waiting list for ECS. We'll keep in touch when the platform goes live!";
                $smsManager = new SmsOtpManager();
                $smsManager->sendSms($request, $message);
            }
            catch(TwilioException $e){
                return response()->json(["Error" => "Invalid phone number. Could not send otp via sms."], 400);
            }
           
        }else {
            return response()->json(["Error" => "Email and Phone cannot be null."], 400);
        }

        WaitingListRequest::create($request->all());
        return response()->json(["Success" => "You have successfully been added to the waitlist."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
