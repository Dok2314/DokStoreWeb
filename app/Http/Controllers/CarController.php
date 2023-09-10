<?php

namespace App\Http\Controllers;

use App\Facades\DokStoreFacade;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function cars()
    {
        $cars = DokStoreFacade::getCars();

    }
}
