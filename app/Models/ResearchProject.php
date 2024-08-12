<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ResearchProject extends Model
{
    use HasFactory;
    protected $table = 'researchprojects';



    // Use UUIDs instead of auto-incrementing IDs
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'researchid';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'researchid',
        'researchnumber',
        'proposalidfk',
        'projectstatus',
        'ispaused',
    ];

    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'proposalid', 'proposalidfk');
    }
    public function applicant()
    {
        return $this->hasOneThrough(User::class, Proposal::class, 'proposalid', 'userid', null, 'useridfk');
    }
}