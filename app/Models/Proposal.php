<?php

namespace App\Models;

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
        'officephone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
}
