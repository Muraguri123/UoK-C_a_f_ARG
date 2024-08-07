<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    //
    public function myprojects()
    {
        if(!auth()->user()->haspermission('canaddoreditgrant')){
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Find the grant by ID or fail with a 404 error
        $isreadonlypage = true;
        $isadminmode = true; 
        // Return the view with the grant data
        return view('pages.projects.myprojects', compact( 'isreadonlypage', 'isadminmode'));
    }
    public function allprojects()
    {
        if(!auth()->user()->haspermission('canaddoreditgrant')){
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Add or Edit a Grant!");
        }
        // Find the grant by ID or fail with a 404 error
        $isreadonlypage = true;
        $isadminmode = true; 
        // Return the view with the grant data
        return view('pages.projects.allprojects', compact( 'isreadonlypage', 'isadminmode'));
    }
}
