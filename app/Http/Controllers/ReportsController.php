<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //
    public function home(){
        if(!auth()->user()->haspermission('canviewreports')){
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to View Reports!");
        }
        return view('pages.reports.home');
    }
}
