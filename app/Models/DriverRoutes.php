<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRoutes extends Model
{
    use HasFactory;

    protected $table = 'driverroutes';

    protected $fillable = [
        'departuredate', 'departuretime', 'seats_available', 'longitude', 'latitude', 'status', 'userid'
    ];
}
