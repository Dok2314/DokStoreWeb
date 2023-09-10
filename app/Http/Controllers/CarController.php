<?php

namespace App\Http\Controllers;

use App\Facades\DokStoreFacade;
use Illuminate\Http\Request;

class CarController extends Controller
{
    protected int $limit = 6;

    public function cars()
    {
        $cars = array_slice(DokStoreFacade::getCars(), 0, $this->limit);
        return view('welcome', compact('cars'));
    }
}
