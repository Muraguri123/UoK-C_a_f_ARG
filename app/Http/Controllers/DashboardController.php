<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ResearchTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function home()
    {
        if(!auth()->user()->haspermission('canviewadmindashboard')){
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to View Admin Dashboard!");
        }
        $themes = ResearchTheme::all();
        // Count proposals grouped by theme
        $themeCounts = Proposal::join('researchthemes', 'proposals.themefk', '=', 'researchthemes.themeid')
            ->select('researchthemes.themename as themename', DB::raw('count(proposals.proposalid) as total'))
            ->groupBy('themename')
            ->get()
            ->toArray();

        // Prepare the data array
        $data = [
            ['Research Theme', 'Applications Count'], // Header row
        ];
 
        foreach ($themes as $theme) {
            $count = 0;
            foreach ($themeCounts as $tc) {
                if ($tc['themename'] === $theme->themename) {
                    $count = $tc['total'];
                    break;
                }
            }
            $data[] = [
                $theme->themename,
                $count,
            ];
        }
        // Get  proposals count
        $allProposalscount = Proposal::count();
        $approvedProposalsCount = Proposal::where('approvalstatus', 'approved')->count();
        $rejectedProposalsCount = Proposal::where('approvalstatus', 'rejected')->count();
        $pendingProposalsCount = Proposal::where('approvalstatus', 'pending')->count();
        $requirechangeProposalsCount = Proposal::where('approvalstatus', 'requirechange')->count();
        $dashboardmetrics = [
            'allProposalscount' => $allProposalscount,
            'approvedProposalsCount' => $approvedProposalsCount,
            'pendingProposalsCount' => $pendingProposalsCount,
            'requirechangeProposalsCount' => $requirechangeProposalsCount,
            'rejectedProposalsCount' => $rejectedProposalsCount,
            'themeCounts' => $data
        ];

        return view('pages.dashboard', $dashboardmetrics);
    }

    public function dashboard()
    {
        if(!auth()->user()->haspermission('canviewadmindashboard')){
            return redirect()->route('pages.unauthorized')->with('unauthorizationmessage', "This User is not Authorized to View Admin Dashboard!");
        }
        $themes = ResearchTheme::all();
        // Count proposals grouped by theme
        $themeCounts = Proposal::join('researchthemes', 'proposals.themefk', '=', 'researchthemes.themeid')
            ->select('researchthemes.themename as themename', DB::raw('count(proposals.proposalid) as total'))
            ->groupBy('themename')
            ->get()
            ->toArray();

        // Prepare the data array
        $data = [
            ['Research Theme', 'Applications Count'], // Header row
        ];
 
        foreach ($themes as $theme) {
            $count = 0;
            foreach ($themeCounts as $tc) {
                if ($tc['themename'] === $theme->themename) {
                    $count = $tc['total'];
                    break;
                }
            }
            $data[] = [
                $theme->themename,
                $count,
            ];
        }
        // Get  proposals count
        $allProposalscount = Proposal::count();
        $approvedProposalsCount = Proposal::where('approvalstatus', 'approved')->count();
        $rejectedProposalsCount = Proposal::where('approvalstatus', 'rejected')->count();
        $pendingProposalsCount = Proposal::where('approvalstatus', 'pending')->count();
        $requirechangeProposalsCount = Proposal::where('approvalstatus', 'requirechange')->count();
        $dashboardmetrics = [
            'allProposalscount' => $allProposalscount,
            'approvedProposalsCount' => $approvedProposalsCount,
            'pendingProposalsCount' => $pendingProposalsCount,
            'requirechangeProposalsCount' => $requirechangeProposalsCount,
            'rejectedProposalsCount' => $rejectedProposalsCount,
            'themeCounts' => $data
        ];

        return view('pages.dashboard', $dashboardmetrics);
    }

    public function unauthorized(){
        $message=session('unauthorizationmessage');
    
        return view('pages.unauthorized', ['message'=>$message]);

    }

}
