<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MultipleStop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\AssignOp\Mul;

class DashboardController extends Controller
{
    public function index(){
        $drivercars = DB::table('driver_cars')->where('user_id', Auth::user()->id)->get();
        return view('driver.dashboard', ['cars' => $drivercars]);
    }




    public function submitmultiplepickuplocation(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'routes_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $ck = MultipleStop::where(['routes_id' => $request->routes_id, 'longitude' => $request->longitude, 'latitude' => $request->latitude])->count();
        if($ck > 0) {
            return response([
                'message' => 'Stop already Exist'
            ], 406);
        }

        MultipleStop::create([
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'routes_id' => $request->routes_id,
        ]);
        return response([
            'message' => 'Location added sucessfuly'
        ]);
    }
}
