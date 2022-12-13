<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RatingReviewController extends Controller
{
    public function rateride(Request $request){

        $check = DB::table('ratings')
                ->where('userid', Auth::user()->id)
                ->where('driverroutesid', $request->id)
                ->get();
        if(!empty($check[0])){
            return back()->with('error', 'You have rated this ride already');

        }else{
            Rating::create([
                    'rating' => $request->rate,
                    'driverroutesid' => $request->id,
                    'userid' => Auth::user()->id
                ]);
            return back()->with('success', 'You have rated this ride');
        }
    }




    public function reviewride(Request $request)
    {
        $check = DB::table('reviews')
        ->where('userid', Auth::user()->id)
            ->where('driverroutesid', $request->id)
            ->get();
        if (!empty($check[0])) {
            return back()->with('error', 'You cannot review twice');
        } else {
            Review::create([
                'review' => $request->review,
                'driverroutesid' => $request->id,
                'userid' => Auth::user()->id
            ]);
            return back()->with('success', 'You have added a review for this ride');
        }
    }
}
