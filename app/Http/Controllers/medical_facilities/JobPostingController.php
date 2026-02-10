<?php

namespace App\Http\Controllers\medical_facilities;

use App\Models\CountryModel;
use App\Models\User;
use App\Models\ProfessionModel;
use App\Models\EligibilityToWorkModel;
use App\Models\WorkingChildrenCheckModel;
use App\Models\PoliceCheckModel;
use App\Models\PractitionerTypeModel;

use App\Models\WorkPreferModel;
use App\Models\JobsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Log;
use App\Services\User\AuthServices;
use App\Http\Requests\UserUpdateProfile;
use App\Http\Requests\UserChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Str;
use Mail;
use Validator;
use DB;
use URL;
use Session;

use App\Models\SpecialityModel;

use App\Repository\Eloquent\SpecialityRepository;

class JobPostingController extends Controller
{
    public function job_posting()
    {
        return view('healthcare.job_posting');
    }

    public function change_password()
    {
        return view('healthcare.change_password');
    }

    public function getWorkplaceData(Request $request)
    {
        
        $place_id = $request->place_id;
        
        $data['work_data'] = DB::table("work_enviornment_preferences")->where("sub_env_id",$place_id)->where("sub_envp_id",0)->orderBy('env_name', 'ASC')->get();
        $environment_name = DB::table("work_enviornment_preferences")->where("prefer_id",$place_id)->first();
        //print_r(json_encode($data));
        $data['env_name'] = $environment_name->env_name;
        $data['prefer_id'] = $place_id;
        return json_encode($data);
    }

    public function getSubWorkplaceData(Request $request)
    {
        
        $place_id = $request->place_id;
        $subplace_id = $request->subplace_id;
        $data['work_data'] = DB::table("work_enviornment_preferences")->where("sub_env_id",$place_id)->where("sub_envp_id",$subplace_id)->orderBy('env_name', 'ASC')->get();
        $environment_name = DB::table("work_enviornment_preferences")->where("prefer_id",$subplace_id)->first();
        //print_r($data);die;
        $data['env_name'] = $environment_name->env_name;
        $data['prefer_id'] = $place_id;
        $data['subplace_id'] = $subplace_id;
        return json_encode($data);
    }

    public function updateBasicJobs(Request $request)
    {
        $user_id = $request->user_id;

        $user_data = User::where("id",$user_id)->first();

        $year = Carbon::now()->year;

        $number = random_int(10000, 99999);

        do {
        // Generate a random 5-digit number (10000 to 99999)
            $code = random_int(10000, 99999); 
            
            // Check if the code already exists in your database table
            $codeExists = JobsModel::where('job_box_id', $code)->exists(); // Replace 'product_code' with your column name
            
        } while ($codeExists); // Loop if it exists


        $job_id = "MQ-".$user_data->country."-".$year."-".$code;

        $sector_preferences = $request->sector_preferences;
        $job_title = $request->job_title;
        $position_open = $request->position_open;
        $type_of_nurse = $request->type_of_nurse;
        $subnursetype = $request->subnursetype;
        $subnursedata = DB::table("practitioner_type")->where("id",$subnursetype)->first();
        $sub_nurse_name = json_encode((array)$subnursedata->name);

        //$subnursetype = json_encode($request->subnursetype);
        $subwork = json_encode($request->input('subwork'));
        $subpwork = json_encode($request->input('subworkthlevel'));
        $main_speciality = $request->main_speciality;
        $subspeciality = $request->subspeciality['primary'];

        // start from first key
        $key = array_key_first($subspeciality);

        while (isset($subspeciality[$key])) {
            $next = $subspeciality[$key][0];

            // if next key does not exist, current key is the last key
            if (!isset($subspeciality[$next])) {
                $lastKeyArray = $subspeciality[$key];
                break;
            }

            $key = $next;
        }

        //print_r($lastKeyArray);

        //print_r($subspeciality['primary']);die;
        $speciality_experience = $request->speciality_experience;
        $willing_upskill = isset($request->willing_upskill)?$request->willing_upskill:0;
        $speciality = json_encode($request->subspeciality['secondary']);

        $job_post = new JobsModel;
        $job_post->job_box_id = $job_id;
        $job_post->sector = $sector_preferences;
        $job_post->healthcare_id = $user_id;
        //$job_post->nurse_type = $type_of_nurse;
        $job_post->nurse_type = $sub_nurse_name;
        $job_post->typeofspeciality = json_encode($lastKeyArray);
        //$job_post->sub_speciality = $subspeciality;
        $job_post->experience_level = $speciality_experience;
        $job_post->secondary_speciality = $speciality;
        $job_post->willing_to_upskill = $willing_upskill;
        $job_post->work_environment = $subpwork;
        $job_post->job_title = $job_title;
        $job_post->position_open = $position_open;
        $run = $job_post->save();

        $jobId = $job_post->id;
        Session::put('jobId', $jobId);

        if ($run) {
            $json['status'] = 1;
            
            
        } else {
            $json['status'] = 0;
        }

        echo json_encode($json);
    }

    public function getEmpData(Request $request)
    {
        $sub_prefer_id = $request->sub_prefer_id;
        $employeement_type_name = DB::table("employeement_type_preferences")->where("emp_prefer_id",$sub_prefer_id)->first();
        
        
        $data['employeement_type_preferences'] = DB::table("employeement_type_preferences")->where("sub_prefer_id",$sub_prefer_id)->get();
        
        
        //print_r($employeement_type_preferences);die;
        $data['employeement_type_name'] = $employeement_type_name->emp_type;
        $data['employeement_type_id'] = $employeement_type_name->emp_prefer_id;
        return json_encode($data);
    }
    public function contract_pay()
    {
        $data['employeement_type_preferences'] = DB::table("employeement_type_preferences")->where("sub_prefer_id","0")->get();
        return view('healthcare.contract-pay')->with($data);
    }

    public function updateContractPay(Request $request)
    {
        $emp_type = json_encode((array)$request->subemptype);
        //print_r($emp_type);

        //for permanent
        $per_hours_week = (!empty($request->per_hours_week))?$request->per_hours_week:'';
        $per_salary_format = (!empty($request->per_salary_format))?$request->per_salary_format:'';
        $per_salary_min = (!empty($request->per_salary_min))?$request->per_salary_min:'';
        $per_salary_max = (!empty($request->per_salary_max))?$request->per_salary_max:'';

        //for fixed-term
        $contract_length_value = (!empty($request->contract_length_value))?$request->contract_length_value:'';
        $contract_length_unit = (!empty($request->contract_length_unit))?$request->contract_length_unit:'';
        $fixed_term_hours_week = (!empty($request->per_hours_week))?$request->fixed_term_hours_week:'';
        $fixed_term_salary_format = (!empty($request->fixed_term_salary_format))?$request->fixed_term_salary_format:'';
        $fixed_term_salary_min = (!empty($request->fixed_term_salary_min))?$request->fixed_term_salary_min:'';
        $fixed_term_salary_max = (!empty($request->per_salary_max))?$request->fixed_term_salary_max:'';

        //for tmporary
        $temporary_hours_week = (!empty($request->temporary_hours_week))?$request->temporary_hours_week:'';
        $temporary_salary_format = (!empty($request->temporary_salary_format))?$request->temporary_salary_format:'';
        $temporary_salary_min = (!empty($request->temporary_salary_min))?$request->temporary_salary_min:'';
        $temporary_salary_max = (!empty($request->temporary_salary_min))?$request->temporary_salary_max:'';
        //echo Session::has('job_id');
        $job_id = Session::get('jobId');

        $job_post = JobsModel::find($job_id);
        
        $job_post->emplyeement_type = $emp_type;

        $job_post->hours_per_week_permanent = $per_hours_week;
        $job_post->salary_permanent = $per_salary_format;
        $job_post->per_salary_min = $per_salary_min;
        $job_post->per_salary_max = $per_salary_max;

        $job_post->contract_length_fixed_term = $contract_length_value;
        $job_post->contract_length_unit_fixed_term = $contract_length_unit;
        $job_post->hours_per_week_fixed_term = $fixed_term_hours_week;
        $job_post->salary_range_fix_term = $fixed_term_salary_format;
        $job_post->fixed_term_salary_min = $fixed_term_salary_min;
        $job_post->fixed_term_salary_max = $fixed_term_salary_max;

        $job_post->shift_date_time_temporary = $temporary_hours_week;
        $job_post->salary_range_temporary = $temporary_salary_format;
        $job_post->temporary_salary_min = $temporary_salary_min;
        $job_post->temporary_salary_max = $temporary_salary_max;
        
        $run = $job_post->save();

        if ($run) {
            $json['status'] = 1;
            
            
        } else {
            $json['status'] = 0;
        }

        echo json_encode($json);
    }
}