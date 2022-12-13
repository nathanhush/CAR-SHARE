<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleStop extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'latitude', 'longitude', 'name', 'routes_id'
    ];

    function route()
    {
        return $this->belongsTo(Route::class, 'routes_id');
    }
}
