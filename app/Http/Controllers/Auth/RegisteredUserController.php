<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $role = DB::table('roles')->where('id',1)->first();
        return view('auth.register', ['role' => $role]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {


        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users'],
            'role_id' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        $request->session()->flash('success', 'Registration Successful');

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
