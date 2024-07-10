<?php

namespace App\Http\Controllers\Proposals;


use App\Http\Controllers\Controller;
use App\Models\ProposalChanges;
use App\Models\Workplan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProposalChangesController extends Controller
{
    //
    public function postproposalchanges(Request $request)
    {
        // Validate incoming request data if needed
        // Define validation rules
        $rules = [
            'issue' => 'required|string', // Example rules, adjust as needed
            'suggestion' => 'required|string',  
            'proposalidfk' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors()], 400);
            return response(['message' => 'Fill all the required Fields!','type'=>'danger'], 400);

        }
 
        // Assuming you're retrieving grantno, departmentid, and userid from the request
        $proposal = new ProposalChanges(); // Ensure the model name matches your actual model class name
        // Assign values from the request
        $proposal->triggerissue = $request->input('issue');
        $proposal->suggestedchange = $request->input('suggestion');
        $proposal->suggestedbyfk = Auth::user()->userid;  
        $proposal->status = 'Pending';  
        $proposal->proposalidfk =$request->input('proposalidfk');
        // Save the proposal
        $proposal->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        return response(['message'=> 'Proposal Change Saved Successfully!!','type'=>'success']);


    }

    
    public function fetchall()
    {
        $data = Workplan::all();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearch(Request $request)
    {
        $searchTerm = $request->input('search');
        $data = Workplan::with('department', 'grantitem', 'themeitem', 'applicant')
            ->where('approvalstatus', 'like', '%' . $searchTerm . '%')
            ->orWhere('highqualification', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('themeitem', function ($query) use ($searchTerm) {
                $query->where('themename', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('applicant', function ($query1) use ($searchTerm) {
                $query1->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('department', function ($query) use ($searchTerm) {
                $query->where('shortname', 'like', '%' . $searchTerm . '%');
            })
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function geteditsingleexpenditurepage($id)
    {
        // Find the proposal by ID or fail with a 404 error
        $prop = Workplan::findOrFail($id);
        $isreadonlypage = false;
        $isadminmode = true; 
        // Return the view with the proposal data
        return view('pages.proposals.proposalform', compact('prop', 'isreadonlypage', 'isadminmode', 'departments', 'grants', 'themes'));
    }
}
