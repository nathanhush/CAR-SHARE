<?php

namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface CarInterface {

    public function unUseCar(Request $request, $id);

    public function useCar(Request $request, $id);

    public function removeCar(Request $request, $id);

    public function addCar(Request $request);



}
