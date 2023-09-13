<?php

namespace App\Http\Controllers\API\Patient;


use App\Http\Controllers\Controller;
use App\Models\Qoute;



class QouteController extends Controller
{
    //return random qoutes
    public function index()
    {
        $quotes = Qoute::inRandomOrder()->limit(10)->get();
        return response([
            'status' => true,
            'quotes'=>$quotes
        ]);
    }
}
