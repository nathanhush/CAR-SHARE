<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCar extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'car_name', 'plate_number', 'available_seats', 'isactive', 'user_id', 'photos', 'type'
    ];

    function owner(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
