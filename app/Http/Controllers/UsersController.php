<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Faker\Core\Number;
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
        if (!auth()->user()->haspermission('canviewallusers')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to View Users!");
        }
        $allusers = User::all();
        return view('pages.users.manage', compact('allusers'));
    }

    public function updateuserpermissions(Request $request, $id)
    {
        if (!auth()->user()->haspermission('canchangeuserroleorrights')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Change User Role or Right!");
        }
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
                if ($user->role != 2) {
                    foreach ($newPermissions as $permissioncode) {
                        $permission = Permission::where('pid', $permissioncode)->firstOrFail();
                        if ($permission->targetrole != 2) {
                            $user->permissions()->attach($permission->pid, ['id' => (string) Str::uuid()]);
                        }
                    }
                } else {
                    $applicantpermissions = Permission::where('targetrole', $user->role)->get();
                    foreach ($applicantpermissions as $permission) {
                        $user->permissions()->attach($permission->pid, ['id' => (string) Str::uuid()]);
                    }
                }
            });
            return response()->json(['message' => 'Permissions updated successfully.', 'type' => "success"]);
        } else {
            return response()->json(['message' => 'Administrator has exclusively all rights!', 'type' => 'warning']);
        }

    }
    public function updaterole(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('canChangeUserRoleOrRights')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not authorized to change user role or rights!");
        }

        // Find the user by ID or fail with a 404 error
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return response()->json(['message' => 'Super Administrator has exclusively all rights!', 'type' => 'warning']);
        }

        DB::transaction(function () use ($user, $request) {
            // Detach all permissions
            $user->permissions()->detach();
            DB::table('userpermissions')->where('useridfk', $user->id)->delete();

            // Update role and admin status
            if ($request->has('isadmin') && $request->input('isadmin') == 'on') {
                $user->isadmin = true;
                $user->role = 1;
            } elseif ($request->has('userrole')) {
                $user->role = (int) $request->input('userrole');
                $user->isadmin = false;
            } else {
                $user->isadmin = false;
            }

            // Update active status
            $user->isactive = $request->has('userisactive') && $request->input('userisactive') == 'on';

            // Update permissions based on the role
            if ($user->role == 2) {
                $applicantPermissions = Permission::where('targetrole', $user->role)->get();
            } else {
                $applicantPermissions = $this->getNonApplicantDefaultRights();
            }

            foreach ($applicantPermissions as $permission) {
                $user->permissions()->attach($permission->pid, ['id' => (string) Str::uuid()]);
            }

            $user->saveOrFail();
        });

        return response()->json(['message' => 'Role updated successfully!', 'type' => 'success']);
    }

    public function getnonapplicantdefaultrights()
    {
        $names = ['canviewallapplications', 'canviewreports', 'canviewadmindashboard', 'canreadproposaldetails', 'canviewofficeuse', 'canproposechanges'];
        $permissions = [];
        $permissions = Permission::whereIn('shortname', $names)->get();
        return $permissions;
    }
    public function updatebasicdetails(Request $request, $id)
    {

        if (Auth::user()->userid != $id && !Auth::user()->haspermission('canedituserprofile')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Edit this User!");
        }
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
            return response()->json(['message' => $validator->errors(), 'type' => 'danger'], 400);
        }

        // Find the user by ID or fail with a 404 error
        $user = User::findOrFail($id);
        $user->name = $request->input('fullname');
        $user->email = $request->input('email');
        $user->pfno = $request->input('pfno');
        $user->phonenumber = $request->input('phonenumber');
        $user->save();
        return response()->json(['message' => 'User Updated Successfully!', 'type' => 'success']);



    }

    public function viewsingleuser($id)
    {
        if (!auth()->user()->haspermission('canedituserprofile')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Edit this User!");
        }
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
        if (!auth()->user()->haspermission('canedituserprofile')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to Edit this User!");
        }
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
        if (!auth()->user()->haspermission('canviewallusers')) {
            return response()->json([]);
        } else {
            $data = User::all();
            return response()->json($data); // Return  data as JSON
        }
    }

    public function fetchsearchusers(Request $request)
    {
        if (!auth()->user()->haspermission('canviewallusers')) {
            return response()->json([]);
        } else {
            $searchTerm = $request->input('search');
            $data = User::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->orWhere('pfno', 'like', '%' . $searchTerm . '%')
                ->orWhere('isactive', 'like', '%' . $searchTerm . '%')
                ->get();
            return response()->json($data); // Return filtered data as JSON
        }

    }


}
