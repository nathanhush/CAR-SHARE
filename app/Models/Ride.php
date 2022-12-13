<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $guarded= [];

    protected $fillable = [
        'driver', 'passenger', 'driver', 'route_id', 'starttime', 'endtime', 'status'
    ];
}
