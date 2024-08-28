<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupervisionController extends Controller
{
    //
    public function home() 
    {
        return view('pages.supervision.home');
    }
}
