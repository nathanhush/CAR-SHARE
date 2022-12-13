<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ExternalAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(){

            $user = Socialite::driver('google')->stateless()->user();

            $this->registerOrLoginUser($user);

            return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback(){
        $user = Socialite::driver('facebook')->stateless()->user();

        $this->registerOrLoginUser($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function registerOrLoginUser($data){
            $user = User::where('email', '=', $data->email)->first();
            if(!$user) {

                $user = User::create([
                    'firstname' => $data->name,
                    'email' => $data->email,
                    'role_id' => 1,
                    'provider_id' => $data->id,
                    'avatar' => $data->avatar
                ]);


            }

            Auth::login($user);
    }
}
