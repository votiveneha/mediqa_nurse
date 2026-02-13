<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewsNurse extends Model
{
    use HasFactory;
    protected $table = 'interview_nurses';
    protected $guarded = [];
}
