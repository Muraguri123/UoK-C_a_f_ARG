<?php

namespace App\Http\Controllers\Proposals;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Department;
use App\Models\Expenditureitem;
use App\Models\Grant;
use App\Models\Proposal;
use App\Models\Publication;
use App\Models\ResearchDesignItem;
use App\Models\ResearchTheme;
use App\Models\Workplan;
use App\Models\User;
use App\Models\ProposalChanges;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ProposalsController extends Controller
{
    //
    public function getnewproposalpage()
    {
        if (!auth()->user()->hasselfpermission('canmakenewproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to Make a new Proposal!");
        }
        $isnewprop = true;
        $isreadonlypage = false;
        $isadminmode = false;
        $departments = Department::all();
        $themes = ResearchTheme::all();
        $user = auth()->user();
        $grants = Grant::where('status', 'Open')
            ->whereDoesntHave('proposals', function ($query) use ($user) {
                $query->where('useridfk', $user->userid);
            })
            ->get();
        return view('pages.proposals.proposalform', compact('isnewprop', 'isadminmode', 'isreadonlypage', 'departments', 'grants', 'themes'));
    }

    public function postnewproposal(Request $request)
    {
        if (!auth()->user()->hasselfpermission('canmakenewproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to Make a new Proposal!");
        }
        // Define validation rules
        $rules = [
            'grantnofk' => 'required|integer',
            'departmentfk' => 'required|string',
            'pfnofk' => 'required|integer',
            'themefk' => 'required|string',
            'highestqualification' => 'required|string',
            'officephone' => 'required|string',
            'cellphone' => 'required|string',
            'faxnumber' => 'required|string',
        ];

        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the grant has already been applied for
        if ($this->isgrantapplied($request->input('grantnofk'))) {
            // Proposal exists for the current user and grant number
            return redirect()->route('pages.proposals.viewnewproposal')
                ->with('success', 'Proposal exists for grant number');
        }
        $grant = Grant::findOrFail($request->input('grantnofk'));
        // Generate proposal code
        $currentYear = date('Y');
        $lastRecord = Proposal::orderBy('proposalid', 'desc')->first();
        $incrementNumber = $lastRecord ? $lastRecord->proposalid + 1 : 1;
        $generatedCode = 'UOK/ARG/' . $grant->finyear . '/' . $incrementNumber;

        // Create a new proposal instance
        $proposal = new Proposal();

        // Assign values from the request
        $proposal->proposalid = $incrementNumber;
        $proposal->proposalcode = $generatedCode;
        $proposal->grantnofk = $request->input('grantnofk');
        $proposal->departmentidfk = $request->input('departmentfk');
        $proposal->useridfk = Auth::user()->userid;
        $proposal->pfnofk = $request->input('pfnofk');
        $proposal->approvalstatus = 'Pending';
        $proposal->highqualification = $request->input('highestqualification');
        $proposal->officephone = $request->input('officephone');
        $proposal->cellphone = $request->input('cellphone');
        $proposal->faxnumber = $request->input('faxnumber');
        $proposal->themefk = $request->input('themefk');

        // Save the proposal
        $proposal->save();
        // Redirect to the edit proposal page with a success message
        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid, 'has_message' => 'Basic Details Successfully Saved!'])
            ->with('success', 'Basic Details Saved Successfully!!');
    }

    private function isgrantapplied($grantno)
    {
        try {
            // Get the current user's ID (assuming you have authenticated users)
            $userid = auth()->id();

            // Check if a proposal exists with the given $grantno and for the current user
            $proposalExists = Proposal::where('grantnofk', $grantno)
                ->where('useridfk', $userid)
                ->exists();

            return $proposalExists;
        } catch (Exception $e) {
            // Handle any exceptions, such as database errors
            return true; // Return true to indicate an error occurred (adjust as needed)
        }
    }

    public function getsingleproposalpage($id)
    {
        if (!auth()->user()->haspermission('canreadproposaldetails')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to read the requested Proposal!");
        }
        // Find the proposal by ID or fail with a 404 error
        $user = Auth::user();
        $prop = Proposal::findOrFail($id);
        $isreadonlypage = true;
        $isadminmode = true;
        $grants = Grant::all();
        $departments = Department::all();
        $themes = ResearchTheme::all();

        return view('pages.proposals.proposalform', compact('prop', 'isreadonlypage', 'isadminmode', 'departments', 'grants', 'themes'));
    }
    public function updatebasicdetails(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to edit this Proposal. Only the owner can Edit!");
        }
        $rules = [
            'grantnofk' => 'required|integer', // Example rules, adjust as needed
            'departmentfk' => 'required|string',
            'themefk' => 'required|string',
            'highestqualification' => 'required|string',
            'officephone' => 'required|string',
            'cellphone' => 'required|string',
            'faxnumber' => 'required|string',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Assign values from the request
        $proposal->departmentidfk = $request->input('departmentfk');
        $proposal->grantnofk = $request->input('grantnofk');
        $proposal->themefk = $request->input('themefk');
        $proposal->highqualification = $request->input('highestqualification'); // Example qualification
        $proposal->officephone = $request->input('officephone');
        $proposal->cellphone = $request->input('cellphone');
        $proposal->faxnumber = $request->input('faxnumber');
        // Save the proposal
        $proposal->save();

        // Optionally, return a response or redirect
        // return response()->json(['message' => 'Proposal created successfully'], 201);
        // return response()->json('success', 'Basic Details Saved Successfully!!');
        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid, 'has_message' => true])->with('success', 'Basic Details Saved Successfully!!');


    }

    public function updateresearchdetails(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to edit this Proposal. Only the owner can Edit!");
        }
        $rules = [
            'researchtitle' => 'required|string', // Example rules, adjust as needed
            'objectives' => 'required|string',
            'hypothesis' => 'required|string',
            'significance' => 'required|string',
            'ethicals' => 'required|string',
            'outputs' => 'required|string',
            'economicimpact' => 'required|string',
            'res_findings' => 'required|string',
            'terminationdate' => 'required|date',
            'commencingdate' => 'required|date',
        ];



        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Assign values from the request
        $proposal->researchtitle = $request->input('researchtitle');
        $proposal->objectives = $request->input('objectives');
        $proposal->hypothesis = $request->input('hypothesis');
        $proposal->significance = $request->input('significance'); // Example qualification
        $proposal->ethicals = $request->input('ethicals');
        $proposal->expoutput = $request->input('outputs');
        $proposal->socio_impact = $request->input('economicimpact');
        $proposal->res_findings = $request->input('res_findings');
        $proposal->commencingdate = $request->input('commencingdate');
        $proposal->terminationdate = $request->input('terminationdate');
        // Save the proposal
        $proposal->save();

        return redirect()->route('pages.proposals.editproposal', ['id' => $proposal->proposalid])->with('success', 'Research Details Saved Successfully!!');


    }
    public function submitproposal(Request $request, $id)
    {
        $proposal = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $proposal->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to Submit this Proposal. Only the owner can Submit!");
        }
        if ($proposal->submittedstatus) {
            return response(['message' => 'Application has already been submitted!', 'type' => 'warning']);
        }
        $cansubmit = $this->cansubmit($id);
        if (isset($cansubmit)) {
            $proposal->submittedstatus = true;
            $proposal->save();
            return response(['message' => 'Application Submitted Successfully!!', 'type' => 'success']);
        } else {
            return response(['message' => 'Application not ready for Submission. Has incomplete Details!', 'type' => 'warning']);
        }

    }
    public function approverejectproposal(Request $request, $id)
    {
        if (auth()->user()->haspermission('canapproveproposal') || auth()->user()->haspermission('canrejectproposal')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to  Approve/Reject this Proposal!");
        }
        $rules = [
            'comment' => 'required|string',
            'status' => 'required|string',
        ];
        // Validate incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => "Please provide a comment & status!", 'type' => "warning"], 400);
        }

        $proposal = Proposal::findOrFail($id);

        $proposal->approvalstatus = $request->input('status');
        $proposal->comment = $request->input('comment');
        $proposal->save();
        if ($request->input('status') == "Approved") {
            return response(['message' => 'Proposal Approved Successfully!!', 'type' => 'success']);
        } else if ($request->input('status') == "Rejected") {
            return response(['message' => 'Proposal Rejected Successfully!!', 'type' => 'success']);
        } else {
            return response(['message' => 'Unknown Action on Status!!', 'type' => 'danger']);
        }



    }
    public function cansubmit($id)
    {
        $response = $this->querysubmissionstatus($id);
        if ($response['basic'] == 2 && $response['design'] == 2 && $response['expenditure'] == 2 && $response['workplan'] == 2 && $response['researchinfo'] == 2) {
            return true;
        } else {
            return false;
        }

    }
    public function canapprove($id)
    {
        $user = User::findOrFail($id);

        if ($user->isadmin || $user->haspermission($id)) {
            return true;
        } else {
            return false;
        }

    }
    public function viewallproposals()
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to view all Proposals!");
        }
        $allproposals = Proposal::all();
        return view('pages.proposals.allproposals', compact('allproposals'));
    }
    public function viewmyapplications()
    {
        if (!auth()->user()->haspermission('canviewmyapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to view My Proposals!");
        }
        $userid = auth()->user()->userid;
        $allproposals = Proposal::where('useridfk', $userid);
        return view('pages.proposals.myapplications', compact('allproposals'));
    }

    public function geteditsingleproposalpage(Request $req, $id)
    {
        $prop = Proposal::findOrFail($id);
        if (!auth()->user()->userid == $prop->useridfk) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to Edit the requested Proposal!");
        }
        $isreadonlypage = false;
        // $isadminmode = true;
        $grants = Grant::all();
        $departments = Department::all();
        $themes = ResearchTheme::all();
        $hasmessage = ($req->input('has_message', 0) == 1) ? true : false;
        // Return the view with the proposal data
        return view('pages.proposals.proposalform', compact('prop', 'isreadonlypage', 'departments', 'grants', 'themes', 'hasmessage'));

    }

    public function fetchmyapplications()
    {
        if (!auth()->user()->haspermission('canviewmyapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to view My Proposals!");
        }
        $userid = auth()->user()->userid;
        $myapplications = Proposal::where('useridfk', $userid)->with('department', 'grantitem', 'themeitem', 'applicant')->get();
        return response()->json($myapplications);
    }

    public function fetchallproposals()
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to view all Proposals!");
        }
        $data = Proposal::with('department', 'grantitem', 'themeitem', 'applicant')->get();
        return response()->json($data); // Return  data as JSON
    }

    public function fetchsearchproposals(Request $request)
    {
        if (!auth()->user()->haspermission('canviewallapplications')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to view all Proposals!");
        }
        $searchTerm = $request->input('search');
        $data = Proposal::with('department', 'grantitem', 'themeitem', 'applicant')
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

    public function fetchcollaborators($id)
    {
        $data = Collaborator::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function fetchpublications($id)
    {
        $data = Publication::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }

    public function fetchexpenditures($id)
    {
        $data = Expenditureitem::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
    public function fetchworkplanitems($id)
    {
        $data = Workplan::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
    public function fetchresearchdesign($id)
    {
        $data = ResearchDesignItem::where('proposalidfk', $id)
            ->get();
        return response()->json($data); // Return filtered data as JSON
    }
    public function querysubmissionstatus($id)
    {
        //0 -not required
        //1 -not completed
        //2 -completed
        $prop = Proposal::findOrFail($id);
        $researchinfo = 1;
        if ($prop->researchtitle && $prop->objectives && $prop->commencingdate && $prop->terminationdate && $prop->hypothesis && $prop->significance && $prop->ethicals && $prop->expoutput && $prop->socio_impact && $prop->res_findings) {
            $researchinfo = 2;
        }
        $basic = ($prop) ? 2 : 1;
        $design = (ResearchDesignItem::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $finanncials = (Expenditureitem::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $workplan = (Workplan::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        $collaborators = (Collaborator::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        ;
        $publications = (Publication::where('proposalidfk', $id)->count() > 0) ? 2 : 1;
        ;
        $appstatus = array('basic' => $basic, 'researchinfo' => $researchinfo, 'design' => $design, 'workplan' => $workplan, 'collaborators' => $collaborators, 'publications' => $publications, 'expenditure' => $finanncials);
        return $appstatus;
    }

    public function fetchsubmissionstatus($id)
    {
        $data = $this->querysubmissionstatus($id);
        $cansubmit = $this->cansubmit($id);
        return response(['data' => $data, 'cansubmitstatus' => $cansubmit]);
    }

    public function fetchproposalchanges($id)
    {
        $data = ProposalChanges::where('proposalidfk', $id)->with('suggestedby')->get();
        return response()->json($data);
    }
}

