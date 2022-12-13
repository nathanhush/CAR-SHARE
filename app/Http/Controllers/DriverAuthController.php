<?php

namespace App\Http\Controllers;

use App\Models\DriverCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;


class DriverAuthController extends Controller
{
    public function create()
    {
        $role = DB::table('roles')
                    ->where('id',2)
                    ->value('id');

        return view('driver.register', ['role' => $role]);
    }

    public function driverstore(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users'],
            'role_id' => ['required'],
            'car_name' => ['required'],
            'plate_number' => ['required','string','unique:driver_cars'],
            'available_seats' => ['required','integer', 'min:1', 'max:17'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'firstname' => ucfirst($request->firstname),
            'lastname' => ucfirst($request->lastname),
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        if($user){

            $usercar = DriverCar::create([
                'car_name' => ucfirst($request->car_name),
                'plate_number' => strtoupper($request->plate_number),
                'available_seats' => $request->available_seats,
                'type' => strtolower($request->type),
                'isactive' => true,
                'user_id' => $user->id
            ]);

            if($usercar){
                event(new Registered($user));
                $request->session()->flash('success', 'Registration Successful, Verify Email');
                return redirect('/login');
            }
            return back()->with('error', 'Car Registration Failed');

        }
        return back()->with('error', 'Driver Registration Failed');

    }
}
