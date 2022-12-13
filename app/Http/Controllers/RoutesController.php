<?php

namespace App\Http\Controllers;

use App\Models\DriverCar;
use Illuminate\Http\Request;
use App\Models\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoutesController extends Controller
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departuretime' => 'required',
            'date' => 'required',
            'longitude' => 'required',
            'latitude' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $car = DriverCar::where(['isactive' => 1, 'user_id' =>  auth()->user()->id])->first();

        if(!$car){
            return response([
                'message' => 'No active car found',
            ], 422);
        }

        //create driver ride
        $driverride = Route::create([
            'departuretime' => strtotime($request->departuretime),
            'date' => $request->date,
            'no_of_availabe_seats' => $car->available_seats,
            'car_id' => $car->id,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'data' => $driverride,
            'message' => 'Ride successfully, proceed to add stops'
        ], 200);
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


    public function deleteride(Request $request){

        $id = $request->delride;

        Route::where('id', $id)->delete();

        return redirect('/driver-dashboard')->with('success', 'Deleted Successfully');

    }
}
