<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobsModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'job_boxes';
    protected $guarded =[];

    protected $casts = [
        'shift_type' => 'array',
    ];

    public function getShiftNamesAttribute()
    {
        if (!$this->shift_type) {
            return [];
        }

        return WorkshiftModel::whereIn('work_shift_id', $this->shift_type)
            ->pluck('shift_name')
            ->toArray();
    }

    public function getEmploymentTypeAttribute()
    {

        if (!$this->emplyeement_type) {
            return null;
        }

        return EmpTypeModel::where('emp_prefer_id', $this->emplyeement_type)
            ->value('emp_type');
    } 

    public function getHealthCareNameAttribute()
    {
        if (!$this->healthcare_id) {
            return null;
        }

        return User::where('id', $this->healthcare_id)
            ->value('name');
    }


}
