<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\DriverCar;
use App\Models\MultipleStop;
use App\Models\Riderequest;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SearchDriverController extends Controller
{
    public function lookForDriver(Request $request)
    {
        // return back()->with('success', 'Found something??');
        $distance = (int)$request->search;
        $passengerdeparturedatetime = $request->datetime;
        $timetolerance = strtotime('+' . $request->timetolerance, strtotime($passengerdeparturedatetime));
        $convertedtime = (string)strtotime($request->datetime);
        $getdriverscoordinates = Route::where([
            'date' => date('Y-m-d', strtotime($passengerdeparturedatetime)),
            ['departuretime', '>=', strtotime($passengerdeparturedatetime)],
            ['departuretime', '<=', $timetolerance],
        ])->get();

        $matchedDrivers = [];

        foreach ($getdriverscoordinates as $value) {
            $car_tp = DriverCar::where(['user_id' => $value->user_id, 'isactive' => 1])->first();
            $long = $value->longitude;
            $lat = $value->latitude;
            $distanceBetweenPassengerAndDriver = $this->getDistance($request->latitude, $request->longitude, $lat, $long);
            $value->distance = ceil($distanceBetweenPassengerAndDriver);
            if(strtolower($car_tp->type) == strtolower($request->car_type)){
                if (ceil($distanceBetweenPassengerAndDriver) <= $distance) {
                    array_push($matchedDrivers, $value);
                }
            }
        }

        $request->session()->put(['results' => $matchedDrivers, 'timetolerance' => $request->timetolerance, 'distancetolerance' => $distance, 'dateandtime' => $convertedtime]);
        if (empty($matchedDrivers)) {
            return back()->with('error', 'No drivers found');
        } else {
            return back()->with('success', count($matchedDrivers) . ' drivers found');
        }
    }

    function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

    // private function addkilometertolocation($latitude, $longitude, $distance): array
    // {
    //     $newlocation = [];

    //     $earthRadius = 6371;
    //     $lat1 = deg2rad((float)$latitude);
    //     $lon1 = deg2rad((float)$longitude);
    //     $bearing = deg2rad(0);

    //     $lat2 = asin(sin($lat1) * cos($distance / $earthRadius) + cos($lat1) * sin($distance / $earthRadius) * cos($bearing));
    //     $lon2 = $lon1 + atan2(sin($bearing) * sin($distance / $earthRadius) * cos($lat1), cos($distance / $earthRadius) - sin($lat1) * sin($lat2));

    //     array_push($newlocation, round(rad2deg($lat2), 5), round(rad2deg($lon2), 5));
    //     return $newlocation;
    // }

    public function reset(Request $request)
    {
        $request->session()->forget('results');
        $request->session()->forget('timetolerance');
        $request->session()->forget('distancetolerance');
        return back();
    }


    public function getridedetails($id)
    {
        //get all the pickup points for the ride
        $rides = MultipleStop::where('routes_id', $id)->get();



        //check if user has joined any ride
        $joinedrides = DB::table('rides')
            ->where('route_id', $id)
            ->where('passenger', Auth::user()->id)
            ->get();

        if (empty($joinedrides[0])) {
            $status = false;
        } else {
            $status = true;
        }
        return view('ridedetails', ['id' => $id, 'rides' => $rides, 'joinedride' => $status]);
    }



    // This function maybe called in a loop to get all the drivers pickup latitude and longitude




    // similar example of how to use

    public function match($req, $res)
    {

        $passengerCoordinates = [];
        $driverCoordinates = [];
        $shortestDistanceToDriver = null;
        $driverIdWithShortestDistanceToPickupLocation = null;

        $obj = $this->riderequest;
        $obj->status = 1;

        $obj->passengerid = $_SESSION["user_id"];
        $getrequest = $obj->checkPassengerRideRequest();

        foreach ($getrequest as $key) {

            //passenger journey details
            $obj->id = $key[1];
            $coordinates = json_decode($key[4]);
            $pickuptime = $key[3];
            $longitude = $coordinates->longitude;
            $latitude = $coordinates->latitude;
        }

        $drivobj = $this->driverroutes;
        $drivobj->status = 0;

        $availabledrivers = $drivobj->checkRandomStatus();

        //loop through to get all the available in active drivers

        //decode the coordinates
        $passengerCoordinates[0] = $longitude;
        $passengerCoordinates[1] = $latitude;

        foreach ($availabledrivers as $key => $value) {
            //drivers journey details
            $newdrivobjs = json_decode($value[5]);

            // var_dump($newdrivobjs);
            // exit;

            //loop through the pickup location of the driver to get single array of coordinates
            foreach ($newdrivobjs as $key => $drivobj) {

                $driverlatitude = $drivobj[0];
                $driverlongitude = $drivobj[1];
                //$drivertime = $value[4];

                // var_dump($driverlongitude);
                // exit;

                $driverCoordinates[0] = $driverlongitude;
                $driverCoordinates[1] = $driverlatitude;

                $distanceToDriver  = $this->calculateDistanceBetweenCoordinates($passengerCoordinates, $driverCoordinates);

                // $isPickupTimeWithinDriverTimeLimit = MatchingAlgorithmController::isPickupTimeWithinDriverTimeLimit($pickuptime, $drivertime);

                //if ($isPickupTimeWithinDriverTimeLimit) {
                if (is_null($shortestDistanceToDriver)) {
                    $shortestDistanceToDriver = $distanceToDriver;
                } else if ($distanceToDriver < $shortestDistanceToDriver) {
                    $shortestDistanceToDriver = $distanceToDriver;
                }
                $driverIdWithShortestDistanceToPickupLocation = $value[2];
                //}
            }
        }


        //if (empty($driverIdWithShortestDistanceToPickupLocation) || is_null($driverIdWithShortestDistanceToPickupLocation)) {
        //   $_SESSION["error"] = "There are no drivers available";
        //   return $res->redirect("/userroutes");
        // } else {

        //   $obj->driverid = $driverIdWithShortestDistanceToPickupLocation;
        //   $update = $obj->updateRideRequest();
        //   if ($update) {

        //     $_SESSION["message"] = "You Have Been Matched";
        //save the driver id in session

        //   $_SESSION["driverid"] = $driverIdWithShortestDistanceToPickupLocation;

        //save ride id in session
        // $_SESSION["rideid"] = $obj->id;
        // return $res->redirect("/userroutes");
        //} else {
        //    $_SESSION["error"] = "unable To Update";
        //    return $res->redirect("/userroutes");
        //}
        //}
    }
}
