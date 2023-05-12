<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Currency;
use App\Models\Platform;
use App\Models\CountryCode;
use App\Models\User;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ShowOrderRequest;
use App\Http\Requests\UpdateOrderRequest;



class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $user = auth()->user();
      if($user->isSender()){
        return Order::where('userId', $user->_id)->get();
      }
      return Order::paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       //
    }

    public function checkPlatformDetails($platformId, $details){
      try{
            $platform = Platform::findOrFail($platformId);
      }
      catch(Exception $e){
          return false;
      }
      return sort(array_keys($details)) == sort(array_keys($platform['details']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
      //check user exists.
      User::findOrFail($request->userId);

      //Check if currencies are valid.
      Currency::findOrFail($request->currencyId);
      Currency::findOrFail($request->recipientCurrencyId);

      //Verify platform is supported by us.
      Platform::findOrFail($request->platformId);

      //Check if country code exists.
      CountryCode::where("code", $request->recipient["country_code"])->firstOrFail();
      
      //send alert to recipient
      $platform = Platform::findOrFail($request->platformId);

      //check if fields supplied for platform are correct.
      $platformDetails = $platform['details'];
      $platformDetailsInRequest = array_keys($request->platformDetails);

      $diff= array_diff($platformDetails, $platformDetailsInRequest);

      if($diff != []){
        return response()->json(["Error"=> ["The following platform details were not found." => $diff]], 403);
      }

      $order = [
        "userId" => $request->userId,
        "currencyId"=> $request->currencyId,
        "platformId"=> $request->platformId,
        "amount"=> $request->amount,
        "recipient"=> $request->recipient,
        "recipientCurrencyId"=> $request->recipientCurrencyId,
        "platformDetails" => $request->platformDetails,
      ];

      //send confirmation to recipient that order has ben made to them.

      Order::create($order);
      return ["order" => $order];
    }

    public function search (Request $request){
        return ["q"=> "ssd"];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $orderId)
    {
       $order = Order::findOrFail($orderId);
       $user = auth()->user();
       if ($user->isSender()){
          if ($order->userId != $user->_id){
            return response()->json(["Error" => "Sender can only access their own order."], 403);
          } 
       }
       return ["order" => $order];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditOrderRequest $request)
    {
      // if 
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $orderId)
    {
        //Todo
        //only amount should be updatable by admin and payer
        //once update is made send a notification via email to customer
        //on sender can change recipient details.
        //create a history for an order if edits are made.
        if(!auth()->user()->admin()){
            return response()->json(["Error" => "Only admin can update an order."], 401);
        }
        $validated = $request->validated();
        $currency = Currency::findOrFail($validated);
        $order = Order::find($orderId);
        $order->update($validated);
        return ["order" => $order];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $user = auth()->user();
      if(!$user->isAdmin()){
          return response()->json(["Error"=> ""],403);
      }

      $order = Order::findOrFail($id);
      $order = $order->archived = true;
      $order->update($order);
     
    }
}
