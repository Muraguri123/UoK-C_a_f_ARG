<?php

namespace App\Models;

use Auth;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ResearchTheme;



class Proposal extends Model
{
    use HasFactory;
    protected $table = 'proposals';

    // Use UUIDs instead of auto-incrementing IDs
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'proposalid';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposalid',
        'grantnofk',
        'useridfk',
        'pfnofk',
        'themefk',
        'highqualification',
        'departmentidfk',
        'approvalstatus',
        'faxnumber',
        'cellphone',
        'officephone',
        'submittedstatus'
    ];



    //functions
    public function applicant()
    {
        return $this->belongsTo(User::class, 'useridfk', 'userid');
    }
    //functions
    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentidfk', 'depid');
    }
    //functions
    public function grantitem()
    {
        return $this->belongsTo(Grant::class, 'grantnofk', 'grantid');
    }
    public function themeitem()
    {
        return $this->belongsTo(ResearchTheme::class, 'themefk', 'themeid');
    }
    public function researchProject()
    {
        return $this->belongsTo(ResearchProject::class, 'research_project_id', 'id');
    }
    public function proposalchanges()
    {
        return $this->belongsTo(ResearchTheme::class, 'themefk', 'themeid');
    }

    function getCurrentFinancialYear()
    {
        $currentDate = new DateTime();
        $currentYear = $currentDate->format('Y');
        $currentMonth = (int) $currentDate->format('m'); // Convert month to integer

        // Financial year starts in July
        $financialYearStartMonth = 7; // July is 7 (1-based index)

        if ($currentMonth >= $financialYearStartMonth) {
            // From July to December, the financial year starts this year
            return "{$currentYear}/" . ($currentYear + 1);
        } else {
            // From January to June, the financial year started last year
            return ($currentYear - 1) . "/{$currentYear}";
        }
    }

    public function isEditable()
    {
        try {
            if ($this->approvalstatus == 'Rejected' || $this->approvalstatus == 'Approved' || !$this->caneditstatus) {
                return false;
            }
            if ($this->approvalstatus == 'Pending') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }

    }
    public function hasPendingUpdates()
    {
        try {
            $changes = $this->proposalchanges()->get();
            if ($changes->where('status', 'Pending')) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }

    }
    public function isApprovable()
    {
        try {
            $user = Auth::user();
            if (($user->userid == $this->useridfk) && $this->caneditstatus && $this->approvalstatus == 'Pending') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }

    }
}
