<?php

namespace App\Http\Controllers\Passenger;

use App\Events\JoinRideEvent;
use App\Events\LeaveRideEvent;
use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RideController extends Controller
{
    public function joinride(Request $request)
    {

        $rideid = $request->id;
        $userid = Auth::user()->id;

        $passenger = User::where('id', $userid)->first();

        $data = DB::table('driverroutes')
            ->where('id', $rideid)
            ->select('seats_available', 'userid')
            ->get();

        $seatsavailable = $data[0]->seats_available;

        $driverid = $data[0]->userid;

        $driver = User::where('id', $driverid)->first();

        if ($seatsavailable == "0") {
            return back()->with('error', 'There are no available seats');
        } else {


            $saveride = Ride::create([
                'status' => 'pending',
                'passenger' => $userid,
                'driver' => $driverid,
                'driverroutesid' => $rideid
            ]);

            event(new JoinRideEvent($passenger, $driver));

            if ($saveride)
                //reduce number of seats
                DB::table('driverroutes')
                    ->where('id', $rideid)
                    ->update([
                        'seats_available' => DB::raw('seats_available - 1'),
                    ]);

            return back()->with('success', 'You have joined this ride');
        }
    }


    public function leaveride(Request $request)
    {
        $rideid = $request->id;
        $userid = Auth::user()->id;

        $passenger = User::where('id', $userid)->first();

        $data = DB::table('driverroutes')
            ->where('id', $rideid)
            ->select('userid')
            ->get();

        $driverid = $data[0]->userid;

        $driver = User::where('id', $driverid)->first();

        if ($data[0]->status) {
            $deleteride = Ride::where('driverroutesid', $rideid)->delete();

            if ($deleteride) {

                event(new LeaveRideEvent($passenger, $driver));

                //increase number of seats
                DB::table('driverroutes')
                    ->where('id', $rideid)
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
}
