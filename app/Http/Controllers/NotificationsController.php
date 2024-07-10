<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    public function notificationshome(){
        return view('pages.notifications.home');
    }
}
