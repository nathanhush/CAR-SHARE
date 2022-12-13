<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CarInterface;
use App\Models\DriverCar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DriverCarController extends Controller implements CarInterface
{
    public function unUseCar(Request $request, $id){


        $car = DriverCar::find($id);


        if(!empty($car)){
            $deactivate = DB::table('driver_cars')
                ->where('id', $id)
                ->update([
                    'isactive' => false
                ]);

            if($deactivate){

                return back()->with('success', 'Car Deactivated');
            }else{
                return back()->with('error', 'An Error Occured');
            }
        }
        return back()->with('error', 'Car Not Found');

    }

    public function useCar(Request $request, $id){
        $car = DriverCar::find($id);

        $carstatus = [];

        $allcars =  DriverCar::all();
        foreach($allcars as $cars){
            array_push($carstatus, $cars->isactive);
        }

        if(in_array(true,$carstatus)){
            return back()->with('error', 'A car is already active');
        }
        elseif(!empty($car)){
            $activate = DB::table('driver_cars')
                ->where('id', $id)
                ->update([
                    'isactive' => true
                ]);
            if($activate){

                return back()->with('success', 'Car Activated');
            }else{
                return back()->with('error', 'An Error Occured');
            }
        }else{
            return back()->with('error', 'Car Not Found');
        }


    }

    public function removeCar(Request $request, $id){
        $car = DriverCar::find($id);

        if(!empty($car) && $car->isactive == false){

            $delete = DB::table('driver_cars')
                ->where('id', $id)
                ->delete();

            if($delete){

                return back()->with('success', 'Car Deleted');
            }else{
                return back()->with('error', 'An Error Occured');
            }
        }
        return back()->with('error', 'This car is active');


    }

    public function addCar(Request $request){

        $validator = Validator::make($request->all(), [
            'car_name' => ['required'],
            'plate_number' => ['required','string','unique:driver_cars'],
            'available_seats' => ['required','integer', 'min:1', 'max:17'],
        ]);

        $usercar = DriverCar::create([
            'car_name' => ucfirst($request->car_name),
            'plate_number' => strtoupper($request->plate_number),
            'available_seats' => $request->available_seats,
            'isactive' => false,
            'user_id' => Auth::user()->id,
            'type' => $request->car_type
        ]);

        return back()->with('success', 'Car Added Successfully');


    }
}
