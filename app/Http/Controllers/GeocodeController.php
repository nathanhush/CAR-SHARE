<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Geocoder;


class GeocodeController extends Controller
{
    public function fetch(Request $request)
    {
        $key = env('GEOCODEAPIKEY');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $key
        ])->get('https://app.geocodeapi.io/api/v1/search?', [
            'text' => $request->value,
            'apikey' => $key,
        ]);

        $data = json_decode($response);



        if (empty($data->features)) {
            echo "No Location Found";
        } else {
            echo $data->features[0]->properties->label;
        }
    }



    public function fetchcoordinates(Request $request)
    {
        $key = env('GEOCODEAPIKEY');



        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $key
        ])->get('https://app.geocodeapi.io/api/v1/search?', [
            'text' => $request->value,
            'apikey' => $key,
        ]);

        $data = json_decode($response);


        if(empty($data)){
            echo "No Location Found";
        }else{
            echo json_encode($data->features[0]->geometry->coordinates);
        }

    }

    public function fetchall(Request $request){
        $key = env('GEOCODEAPIKEY');



        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $key
        ])->get('https://app.geocodeapi.io/api/v1/search?', [
            'text' => $request->value,
            'apikey' => $key,
        ]);

        $data = json_decode($response);


        if(empty($data)){
            echo "No Location Found";
        }else{

            $alldata = [ 'long' => $data->features[0]->geometry->coordinates[0], 'lat' => $data->features[0]->geometry->coordinates[1], 'address' => $data->features[0]->properties->label  ];
            return json_encode($alldata);
        }

    }
}
