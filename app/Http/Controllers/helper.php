<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



if(!function_exists("getuserdetails")){

    function getuserdetails($id){
        return User::find($id);
    }

}



if (!function_exists("getridedateandtime")) {

    function getridedateandtime($id)
    {

        $data = DB::table('driverroutes')
            ->where('id', $id)
            ->select('departuretime')
            ->first();

        return $data->departuretime;
    }
}


if (!function_exists("getridestatus")) {

    function getridestatus($id)
    {
        $data = DB::table('rides')
            ->where('route_id', $id)
            ->select('status')
            ->get();

        if(empty($data[0])){
            return false;
        }elseif($data[0]->status == "pending"){
            return false;
        }elseif ($data[0]->status == "ended") {
            return true;
        }elseif ($data[0]->status == "active") {
           return true;
        }else{
            return false;
        }
    }
}

if (!function_exists("getridestringstatus")) {
function getridestringstatus($id)
{

    $data = DB::table('rides')
        ->where('route_id', $id)
        ->select('status')
        ->get();

        if(empty($data->first())){
            return false;
        }

    if ($data[0]->status == "pending") {
        return "pending";
    }elseif($data[0]->status == "active"){
        return "active";
    }elseif($data[0]->status == "ended"){
        return "ended";
    }
}
}

if (!function_exists("getridedate")) {

    function getridedate($id)
    {


        $data = DB::table('driverroutes')
        ->where('id', $id)
        ->get('departuredate');

        return $data[0]->departuredate;
    }
}



if (!function_exists("getdriverroutesid")) {

    function getdriverroutesid($id)
    {


        $data = DB::table('multiple_stops')
        ->where('id', $id)
            ->get('routes_id');

        return $data[0]->routes_id;
    }
}


if (!function_exists("passengers")) {

    function passengers($id)
    {


        $data = DB::table('rides')
        ->where('route_id', $id)
            ->get('passenger');

            if(empty($data->first())){
                return [];
            }
        return $data[0]->passenger;

    }
}


if (!function_exists("changestatustostring")) {

    function changestatustostring($status)
    {

        if ($status == 0) {
            return "Setting Ride";
        }
        if($status == 1){
            return "Pending";
        }
        if($status == 2){
            return "Active";
        }
        if($status == 3){
            return "Ended";
        }
    }
}



if (!function_exists("getpassengerrating")) {

    function getpassengerrating($id)
    {

        $rating = DB::table('ratings')
            ->where('userid', Auth::user()->id)
            ->where('driverroutesid', $id)
            ->get();
        if (empty($rating->first())) {
            return [];
        }

        return $rating[0]->rating;
    }
}


if (!function_exists("getdriverrating")) {

    function getdriverrating($id)
    {

        $rating = DB::table('ratings')
            ->where('driverroutesid', $id)
            ->get();
        if (empty($rating->first())) {
            return [];
        }

        return $rating;
    }
}


if (!function_exists("getdriverreviews")) {

    function getdriverreviews($id)
    {

        $reviews = DB::table('reviews')
        ->where('driverroutesid', $id)
            ->get();
        if (empty($reviews->first())) {
            return [];
        }

        return $reviews;
    }
}

if (!function_exists("getpassengerreview")) {

    function getpassengerreview($id)
    {

        $review = DB::table('reviews')
        ->where('userid', Auth::user()->id)
            ->where('driverroutesid', $id)
            ->get();

        if (empty($review->first())) {
            return [];
        }
        return $review[0]->review;
    }
}


if (!function_exists("changeratingtostring")) {

    function changeratingtostring($status)
    {

        if ($status == 1) {
            echo "<h4 style='color:red'>POOR</h4>";
        }
        if ($status == 2) {
            echo "<h4 style='color:orangered'>FAIR</h4>";
        }
        if ($status == 3) {
            echo "<h4 style='color:blue'>GOOD</h4>";
        }
        if ($status == 4) {
            echo "<h4 style='color:greenyellow'>VERY GOOD</h4>";
        }
        if ($status == 5) {
            echo "<h4 style='color:green'>EXCELLENT</h4>";
        }
    }
}

