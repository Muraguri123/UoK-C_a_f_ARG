<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // For generating UUIDs

class UsersController extends Controller
{
    //
    public function viewallusers()
    {
        $allusers = User::all();
        return view('pages.users.manage', compact('allusers'));
    }

    public function updateuserpermissions(Request $request, $id)
    {
        // Find the user by ID or fail with a 404 error
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,pid'
        ]);
        $newPermissions = $request->input('permissions');
        $user = User::findOrFail($id);
        if (!$user->issuperadmin()) {
            DB::transaction(function () use ($user, $id, $newPermissions) {
                $user->permissions()->detach();
                DB::table('userpermissions')->where('useridfk', $id)->delete();
                // Add new permissions for the user
                foreach ($newPermissions as $permissioncode) {
                    $permission = Permission::where('pid', $permissioncode)->firstOrFail();
                    $user->permissions()->attach($permission->pid, ['id' => (string) Str::uuid()]);
                }
            });
            return response()->json(['message' => 'Permissions updated successfully.', 'type' => "success"]);
        } else {
            return response()->json(['message' => 'Administrator has exclusively all rights!', 'type', $user]);
        }

    }

    public function updaterole(Request $request, $id)
    {
        // Find the user by ID or fail with a 404 error
        $user = User::findOrFail($id);
        if (!$user->issuperadmin()) {

            if ($request->exists('isadmin') && $request->input('isadmin') == 'on') {
                $user->isadmin = true;
                $user->role = 1;

            } else if ($request->exists('userrole')) {
                $user->role = $request->input('userrole');
                $user->isadmin = false;
            } else {
                $user->isadmin = false;
            }
            if ($request->exists('userisactive') && $request->input('userisactive') == 'on') {
                $user->isactive = true;
            } else {
                $user->isactive = false;
            }
            $user->save();
            return response()->json(['message' => 'Role Updated Successfully!', 'type', 'success']);

        } else {
            return response()->json(['message' => 'Super Administrator has exclusively all rights!', 'type', 'warning']);
        }

    }

    public function updatebasicdetails(Request $request, $id)
    {
           // Define validation rules
           $rules = [
            'fullname' => 'required|string',
            'email' => 'required|string',
            'phonenumber' => 'required|string', 
            'pfno' => 'required|string', 
        ];

        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(),'type'=>'danger'], 400);
        }

        // Find the user by ID or fail with a 404 error
        $user = User::findOrFail($id);
        if ($user->issuperadmin() || (auth()->user()->userid==$user->userid)) {
            $user->name =$request->input('fullname');
            $user->email =$request->input('email');
            $user->pfno =$request->input('pfno');
            $user->phonenumber =$request->input('phonenumber');
                $user->save();
            return response()->json(['message' => 'User Updated Successfully!', 'type'=> 'success']);

        } else {
            return response()->json(['message' => 'You dont have the rights to update this User!', 'type'=>'danger']);
        }

    }

    public function viewsingleuser($id)
    {
        // Find the user by ID or fail with a 404 error
        $user = User::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true;
        $permissions = Permission::all();
        // Return the view with the proposal data
        return view('pages.users.viewuser', compact('user', 'isreadonlypage', 'isadminmode', 'permissions'));
    }
    public function geteditsingleuserpage($id)
    {
        // Find the proposal by ID or fail with a 404 error
        $prop = User::findOrFail($id);
        $isreadonlypage = false;
        $isadminmode = true;
        $grants = User::all();
        // Return the view with the proposal data
        return view('pages.users.proposalform', compact('prop', 'isreadonlypage', 'isadminmode', 'departments', 'grants', 'themes'));
    }

    public function fetchallusers()
    {
        $data = User::all();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearchusers(Request $request)
    {
        $searchTerm = $request->input('search');
        $data = User::all()->where('name', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->orWhere('pfno', 'like', '%' . $searchTerm . '%')
            ->orWhere('isactive', 'like', '%' . $searchTerm . '%')
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
}
