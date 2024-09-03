<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusinessMailingController extends Controller
{
    //
    public function mailinghome(){
        return view('pages.mailingmodule.home');
    }
}
