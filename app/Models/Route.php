<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class Route extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'departuretime', 'date', 'no_of_availabe_seats', 'longitude', 'latitude', 'user_id', 'starttime', 'endtime',  'car_id'
    ];

    function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    function stops(){
        return $this->hasMany(MultipleStop::class, 'routes_id');
    }


    function car()
    {
        return $this->belongsTo(DriverCar::class, 'car_id');
    }


    protected $table = 'routes';
}
