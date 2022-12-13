<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Passenger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->role_id == 1){
            //i am a passenger continue to request
            return $next($request);
        }else {
            return redirect('/driver-dashboard')->with('error', 'You can not access page, switch to user mode to use page');
        }
    }
}
