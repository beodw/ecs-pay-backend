<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Currency;
use App\Models\Platform;
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
      // return ["orders" => auth()->user()->isAdmin()];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
      // if (!auth()->user()->isSender()){
      //   return response()->json(["Error" => "Only a sender can create an order."], 403);
      // }

      Currency::findOrFail($request->currencyId);
      Currency::findOrFail($request->recipientCurrencyId);

      $platform = Platform::findOrFail($request->platformId)->first();


      return ["platform"=>$platform->details];

      Order::create($request);
      return ["order" => $request];
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
        //
    }
}
