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
        $speciality = json_encode($request->subspeciality);

        $job_post = new JobsModel;
        
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
}