<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class RegisterController extends Controller
{
    //
    public function showRegistrationForm()
    {
        return view('pages.auth.register');
    }

    public function register(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:255',
            'pfno' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create a new user instance
        $user = new User();
        $user->name = $validatedData['fullname'];
        $user->email = $validatedData['email'];
        $user->pfno =$validatedData['pfno'];
        $user->phonenumber =$validatedData['phonenumber'];
        $user->password = Hash::make($validatedData['password']);
        $user->role=2; 
        $user->isadmin=0;
        $user->isactive=0;
        // $user->isactive=1;
        $user->save();

        // Redirect to login page with success message
        return redirect()->route('pages.login')->with('success', 'Registration successful. Please login.');
    }

    public function resetuserpassworddirectly(Request $request,$id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([ 
            'newpass' => 'required|string|min:8',
        ]);

        // Create a new user instance
        $user = User::findOrFail($id); 
        $user->password = Hash::make($validatedData['newpass']); 
        $user->save();

        // Redirect to login page with success message
        return response(['message'=> 'Password Changed successfully!','type'=>'success']);
    }
}
