<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    function switchModes()
    {
        $user = auth()->user();
        /// $new_role = ($user->role_id == 1) ? change to (2) driver : change to (1) user;
        $new_role = ($user->role_id == 1) ? 2 : 1;
        User::where('id', $user->id)->update([
            'role_id' => $new_role
        ]);
        $redirect_url = ($user->role_id == 1) ? '/driver-dashboard' : 'dashboard';
        return redirect($redirect_url)->with('success', 'Modes switched sucesfully');
    }


    function updateUserInformation(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
        ]);
        if ($validatedData->fails()) {
            return back()->with('errors', $validatedData->errors()->all());
        }

        User::where('id', auth()->user()->id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone
        ]);


        return back()->with('success', 'Profile update was sucessfull');
    }



}
