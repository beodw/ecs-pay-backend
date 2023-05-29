<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToWaitingListRequest;
use App\Models\WaitingListRequest;

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
    public function store(AddToWaitingListRequest $request)
    {
        if($request->email != null ){
            $waitingListRequest = WaitingListRequest::where("email", "=", $request->email)->first();
        }
        elseif ($request->phone != null){
            $waitingListRequest = WaitingListRequest::where("phone", "=", $request->phone)
                                                        ->where("country_code", "=", $request->country_code)
                                                        ->first();
        }else {
            return response()->json(["Error" => "Email and Phone cannot be null."], 400);
        }

        if($waitingListRequest != null){
            return response()->json(["Error" => "This email or phone has already signed up."], 409);
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
