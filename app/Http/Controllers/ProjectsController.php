<?php

namespace App\Http\Controllers;

use App\Models\ResearchProject;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    //
    public function myprojects()
    {
        if (!auth()->user()->hasPermission('canviewmyprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view your Projects!");
        }

        // Return the view with the necessary data
        return view('pages.projects.myprojects');
    }
    public function fetchmyactiveprojects()
    {
        if (!auth()->user()->hasPermission('canviewmyprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view your Projects!");
        }

        $userid = auth()->user()->userid;

        // Fetch projects where the related proposals' useridfk matches the current user
        $myprojects = ResearchProject::with('proposal')
            ->whereHas('proposal', function ($query) use ($userid) {
                $query->where('useridfk', $userid);
            })
            ->where('projectstatus', 'Active')
            ->with('proposal')
            ->with('applicant')
            ->get();
        // Return the view with the necessary data
        return response()->json($myprojects);
    }
    public function fetchmyallprojects()
    {
        if (!auth()->user()->hasPermission('canviewmyprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view your Projects!");
        }

        $userid = auth()->user()->userid;

        // Fetch projects where the related proposals' useridfk matches the current user
        $myprojects = ResearchProject::with('proposal')
            ->whereHas('proposal', function ($query) use ($userid) {
                $query->where('useridfk', $userid);
            })
            ->with('proposal')
            ->with('applicant')
            ->get();
        // Return the view with the necessary data
        return response()->json($myprojects);
    }
    public function viewmyproject($id)
    {
        if (!auth()->user()->hasPermission('canreadmyproject')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view this Project!");
        }

        // Fetch projects where the related proposals' useridfk matches the current user
        $project = ResearchProject::with(['proposal.applicant'])->findOrFail($id);
            //  ;
        // Return the view with the necessary data
        return view('pages.projects.viewproject', compact('project'));
    }

    public function allprojects()
    {
        if (!auth()->user()->hasPermission('canviewallprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Projects!");
        }
        // Return the view with the necessary data
        return view('pages.projects.allprojects');
    }

    public function fetchallactiveprojects()
    {
        if (!auth()->user()->hasPermission('canviewallprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Projects!");
        }

        $allprojects = ResearchProject::where('projectstatus', 'Active')
            ->with('proposal')
            ->with('applicant')
            ->get();
        return response()->json($allprojects);
    }

    public function fetchallprojects()
    {
        if (!auth()->user()->hasPermission('canviewallprojects')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view all Projects!");
        }

        // Fetch projects where the related proposals' useridfk matches the current user
        $allprojects = ResearchProject::with('proposal')
            ->with('applicant')
            ->get();
        return response()->json($allprojects);
    }

    public function fetchsearchallprojects(Request $request)
    {
        if (!auth()->user()->hasPermission('canviewallprojects')) {
            return response()->json([]);
        }

        $searchTerm = $request->input('search');

        // Fetch projects where the applicant's name or project status matches the search term
        $allprojects = ResearchProject::with(['proposal', 'applicant'])
            ->whereHas('applicant', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhere('projectstatus', 'like', '%' . $searchTerm . '%')
            ->get();

        return response()->json($allprojects);
    }

    public function viewanyproject($id)
    {
        if (!auth()->user()->hasPermission('canreadanyproject')) {
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "You are not Authorized to view this Project!");
        }

        // Fetch projects where the related proposals' useridfk matches the current user
        $project = ResearchProject::with(['proposal.applicant'])->findOrFail($id);
            //  ;
        // Return the view with the necessary data
        return view('pages.projects.viewproject', compact('project'));
    }

}
