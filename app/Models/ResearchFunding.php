<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchFunding extends Model
{
    use HasFactory;
    protected $table = 'researchfundings';
    protected $primaryKey = 'id';

    // Define the fillable attributes
    protected $fillable = [
        'createdby',
        'researchidfk',
        'amount'
    ];
 
}
