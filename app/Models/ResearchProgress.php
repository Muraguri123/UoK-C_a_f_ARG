<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchProgress extends Model
{
    use HasFactory;
    protected $table = 'researchprogress';
    protected $primaryKey = 'id';

    // Define the fillable attributes
    protected $fillable = [
        'reportedbyfk',
        'researchidfk',
        'report'
    ];
 
}
