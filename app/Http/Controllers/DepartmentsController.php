<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Grant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    //
    public function postnewdepartment(Request $request)
    {
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [
            'shortname' => 'required|string', // Example rules, adjust as needed
            'description' => 'required|string',  
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!','type'=>'danger'], 400);

        }

        // Assuming you're retrieving grantno, departmentid, and userid from the request
        $grant = new Department(); // Ensure the model name matches your actual model class name
        // Assign values from the request
        $grant->shortname = $request->input('shortname');
        $grant->description = $request->input('description'); 
        $grant->save();

        // Optionally, return a response or redirect 
        return response(['message'=> 'Department Saved Successfully!!','type'=>'success']);


    }

    public function updatedepartment(Request $request, $id)
    {
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [
            'description' => 'required|string', // Example rules, adjust as needed
            'shortname' => 'required|string', // Adjust data types as per your schema 
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!','type'=>'danger'], 400);

        }

        // Assuming you're retrieving grantno, departmentid, and userid from the request
        $dep = Department::findOrFail($id); // Ensure the model name matches your actual model class name
        // Assign values from the request
        $dep->shortname = $request->input('shortname');
        $dep->description = $request->input('description'); 
        $dep->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message'=> 'Department Updated Successfully!!','type'=>'success']);


    }
    public function viewalldepartments()
    {
        $alldepartments = Department::all();
        return view('pages.departments.home', compact('alldepartments'));
    }
    public function getviewdepartmentpage($id)
    {
        // Find the department by ID or fail with a 404 error
        $department = Department::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true;  
        return view('pages.departments.departmentform', compact('department', 'isreadonlypage', 'isadminmode'));
    }
    public function geteditdepartmentpage($id)
    {
        // Find the grant by ID or fail with a 404 error
        $grant = Grant::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true; 
        // Return the view with the grant data
        return view('pages.proposals.proposalform', compact('isreadonlypage', 'isadminmode', 'grant'));
    }

    public function fetchalldepartments()
    {
        $data = Department::all();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearchdepartments(Request $request)
    {
        $searchTerm = $request->input('search');
        $data = Department::all()->where('shortname', 'like', '%' . $searchTerm . '%') 
            ->orWhere('description', 'like', '%' . $searchTerm . '%')   
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
}
