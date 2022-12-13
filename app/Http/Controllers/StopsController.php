<?php

namespace App\Http\Controllers;

use App\Events\LeaveRideEvent;
use App\Events\JoinRideEvent;
use App\Models\MultipleStop;
use App\Models\Ride;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Parser\Multiple;

class StopsController extends Controller
{


    public function rideResInfo(Request $request)
    {
        $rides = MultipleStop::where('routes_id', $request->rideid)->get();
        return response($rides);
    }


    function fetchRideStops(Request $request)
    {


        $id = $request->driverroutes_id;
        $stops = MultipleStop::where('routes_id', $id)->get(); $stop_arr = [];
        foreach($stops as $stop) {
            $data = $stop;
            $data->diffrence = $this->calculateDistanceBetweenCoordinates([$request->points['long'], $request->points['lat']], [$stop->longitude, $stop->latitude]);
            $stop_arr[] = $data;
        }
        return response([
            'data' => $stop_arr
        ], 200);
    }



    public static function calculateDistanceBetweenCoordinates($coordinateOne, $coordinateTwo)
    {
        //coordinateOne is an array that will have the passengers long and lat
        //coordinatetwo is an array that will have the drivers long and lat

        $xDifference = abs($coordinateOne[0] - $coordinateTwo[0]);
        $yDifference = abs($coordinateOne[1] - $coordinateTwo[1]);

        $squaredXDifference = pow($xDifference, 2);
        $squaredYDifference = pow($yDifference, 2);

        //this will return the distance of each driver pickup point and the passenger

        return sqrt($squaredXDifference + $squaredYDifference);
    }


    public function joinride(Request $request)
    {

        $rideid = $request->id;
        $userid = Auth::user()->id;

        $passenger = User::where('id', $userid)->first();

        $data = Route::where('id', $rideid)->select('no_of_availabe_seats', 'user_id')->first();

        $seatsavailable = $data->no_of_availabe_seats;

        $driverid = $data->user_id;

        $driver = User::where('id', $driverid)->first();

        if ($seatsavailable == "0") {
            return back()->with('error', 'There are no available seats');
        } else {

            $saveride = Ride::create([
                'status' => 'pending',
                'passenger' => $userid,
                'driver' => $driverid,
                'route_id' => $rideid,
            ]);

            event(new JoinRideEvent($passenger, $driver));

            if ($saveride)
                Route::where('id', $rideid)
                    ->update([
                        'no_of_availabe_seats' => DB::raw('no_of_availabe_seats - 1'),
                    ]);

            return back()->with('success', 'You have joined this ride');
        }
    }


    public function leaveride(Request $request)
    {
        $rideid = $request->id;
        $userid = Auth::user()->id;

        $passenger = User::where('id', $userid)->first();

        $data = Route::where('id', $rideid)
            ->select('user_id')
            ->get();

        $driverid = $data[0]->userid;

        $driver = User::where('id', $driverid)->first();

        if ($data[0]->status) {
            $deleteride = Ride::where(['route_id' => $rideid, 'passenger' => $userid ])->delete();

            if ($deleteride) {

                event(new LeaveRideEvent($passenger, $driver));

                //increase number of seats
                Route::where('id', $rideid)
                    ->update([
                        'seats_available' => DB::raw('seats_available + 1'),
                    ]);
                return back()->with('success', 'You have left the ride');
            }
        } else {
            return back()->with('error', 'You cannot leave this ride');
        }
    }


    public function joinedrides(Request $request){
        $joinedride = DB::table('rides')
                        ->where('passenger', Auth::user()->id)
                        ->get();

        return view('joinedride', ['rides' => $joinedride]);
    }


    public function startride(Request $request){
        $id = $request->startride;
        $getdeparturetime = Route::where('id', $id)->get();
        $start = Route::where('id', $id)
        ->update([
            'status' => 2
        ]);
        if ($start) {
            Ride::where('route_id', $id)
                ->update([
                    'status' => 'active',
                    'starttime' => time()
                ]);
            return back()->with('success', 'You have started the ride');
        }
    }


    public function endride(Request $request)
    {
        $id = $request->endride;
        $end = Route::where('id', $id)
            ->update([
                'status' => 3
            ]);
        if($end){
            Ride::where('route_id', $id)
                ->update([
                    'status' => 'ended',
                    'endtime' => time()
                ]);
            return back()->with('success', 'You have ended the ride');
        }
    }

    public function deleteride(Request $request){
        $id = $request->delride;
        //check if joined
        // if no delete rides and stops
            MultipleStop::where(['routes_id' => $id])->delete();
            Route::where('id', $id)->delete();
        return redirect('/driver-dashboard')->with('success', 'Deleted Successfully');

    }



    public function rideInfo($id)
    {
        $driverroutes =Route::find($id);
        $rides = $driverroutes->stops;
        $passengers = Ride::where('route_id', $id)->get();
        $status = $driverroutes->status;
        return view('driver.ride_info', ['id' => $id, 'rides' => $rides, 'passengers' => $passengers, 'status' => $status]);
    }




    public function submitmultiplepoints(Request $request)
    {

        $data = $request->multiplelocation;

        foreach ($data as $key) {

            $name = $key[0];
            $time = $key[1];
            $longitude = $key[2];
            $latitude = $key[3];
            $id = $key[4];



            $position = 1;
            $checkposition = DB::table('multiple_stops')->where('routes_id', $id)->get();
            //check if there is any record, if nothing then assign position one to it.
            if (empty($checkposition->first()) || is_null($checkposition->first())) {
                $save = DB::table('multiple_stops')->insert([
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'name' => $name,
                    'routes_id' => $id
                ]);
            } else {


                $save = DB::table('multiple_stops')->insert([
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'name' => $name,
                    'routes_id' => $id
                ]);
            }
        }
        return false;
    }


    public function submitmultipletime(Request $request)
    {

        $request->validate([
            'time' => 'required',
            'id' => 'required'
        ]);

        $getrouteid = getdriverroutesid($request->id);

        if (getridestatus($getrouteid)) {
            return "false";
        }
        else{
            $update = DB::table('multiplelocations')
                ->where('id', $request->id)
                ->update([
                    'time' => strtotime($request->time)
                ]);
            if ($update) {
                return true;
            }
            return false;

        }


    }

    public function deletemultiplelocation(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $getrouteid = getdriverroutesid($request->id);

        if(getridestatus($getrouteid)){
            return "false";
        }
        else{
            $delete = DB::table('multiple_stops')
            ->where('id', $request->id)
            ->delete();
            if ($delete) {
                return true;
            }
            return false;
        }


    }

    public function editmultiplepickup(Request $request){
        $getrouteid = getdriverroutesid($request->locationid);

        $dateandtime = $request->datetime;


        if(empty($dateandtime)){
            return back()->with('error', "Date and Time Cannot be empty");
        }




        $result = Route::where('id', $getrouteid)->get('status');



        if($result[0]->status == 0 || $result[0]->status == 2){
            return back()->with('error', 'You Cannot Edit Ride Pickup Details');
        }else{
            DB::table('multiple_stops')
                ->where('id', $request->locationid)->update([
                    'name' => $request->location,
                    'latitude' => $request->newlat,
                    'longitude' => $request->newlong,
                ]);
            return back()->with('success', 'Pickup Location Updated');
        }
    }




    public function updateposition(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'position' => 'required'
        ]);

        $getrouteid = getdriverroutesid($request->id);
        if (getridestatus($getrouteid)) {
            return "false";
        }
        else{
            $getpreviousposition = DB::table('multiplelocations')->where('id', $request->id)->get('position');
            //initial position of the sortable
            $old = $getpreviousposition->first()->position;
            $getcurrentposition = DB::table('multiplelocations')->where('position', $request->position)->get('position');
            $current = $getcurrentposition->first()->position;

            if ($old !== $request->position) {
                if ($old > $request->position) {
                    $update = DB::table('multiplelocations')->where('position', '>=', $request->position)->where('position', '<', $old)->update([
                        'position' => DB::raw('position + 1'),
                    ]);
                } elseif ($old < $request->position) {
                    $update = DB::table('multiplelocations')
                        ->where('position', '<=', $request->position)
                        ->where('position', '>', $old)
                        ->update([
                            'position' => DB::raw('position - 1'),
                        ]);
                }
                $updateold = DB::table('multiplelocations')
                    ->where('id', $request->id)
                    ->update([
                        'position' => $request->position
                    ]);

                if ($updateold) {
                    return true;
                }
            }

        }
    }


}
