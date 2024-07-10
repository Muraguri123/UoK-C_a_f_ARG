<?php

namespace App\Http\Controllers\Proposals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //
    public function home(){
        return view('pages.reports.home');
    }
}
