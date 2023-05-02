<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Currency;
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
       $validated = $request->validated();
       Order::create($validated);
       return ["order" => $validated];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $orderId)
    {
       $order = Order::find($orderId);
       return ["order" => $order];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $orderId)
    {
      //
        
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
        //create a hiistory for an order if edits are made.
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
