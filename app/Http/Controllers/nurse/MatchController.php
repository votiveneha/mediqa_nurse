<?php

namespace App\Http\Controllers\nurse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\MatchHelper;
use App\Models\User;
use App\Models\JobsModel;
use App\Models\SpecialityModel;
use Illuminate\Support\Facades\Auth;
use DB;

class MatchController extends Controller
{
    /**
     * Calculate match score for a given user and job
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job   $job
     * @return \Illuminate\Http\JsonResponse
     */

    public function match_percentage()
    {
        
        $user = Auth::guard("nurse_middle")->user();
        $jobs = JobsModel::get();
        

        $data['education_certification_percent'] = $this->matchEducationPercent($jobs,$user);
        $data['experience_certification_percent'] = $this->matchExperiencePercent($jobs,$user);
        

        //print_r($training_id_arr);

        


        //print_r($nurse_percent);die;
        return view('nurse.match_percentage')->with($data);
    }


    public function matchEducationPercent($jobs, $user)
    {
        // -------- COLLECT ALL JOB REQUIREMENTS -------- //
        $job_degree_arr        = [];
        $mandatorytraining_arr = [];
        $mandatoryeducation_arr = [];
        $award_recognitionarr   = [];

        foreach ($jobs as $job) {

            $job_degree_arr         = array_merge($job_degree_arr, (array) json_decode($job->degree, true));
            $mandatorytraining_arr  = array_merge($mandatorytraining_arr, (array) json_decode($job->mandatory_tarining, true));
            $mandatoryeducation_arr = array_merge($mandatoryeducation_arr, (array) json_decode($job->mandatory_education, true));
            $award_recognitionarr   = array_merge($award_recognitionarr, (array) json_decode($job->award_recognition, true));
        }

        // -------- USER DEGREE -------- //
        $user_degree = (array) json_decode($user->degree, true);
        $found_degree = empty(array_diff($user_degree, $job_degree_arr)) ? 1 : 0;

        // -------- USER TRAINING -------- //
        $training = DB::table("mandatory_training")->where("user_id", $user->id)->first();
        $training_data = json_decode($training->training_data, true);

        $training_id_arr = [];
        if(!empty($training_data)){
            foreach ($training_data as $parent => $childs) {
                $training_id_arr[] = $parent;
                $training_id_arr = array_merge($training_id_arr, array_keys($childs));
            }
        }

        $found_training = empty(array_diff($training_id_arr, $mandatorytraining_arr)) ? 1 : 0;

        // -------- USER EDUCATION -------- //
        $education_data = json_decode($training->education_data, true);
        $education_id_arr = [];
        
        if(!empty($$education_data)){
            foreach ($education_data as $parent => $childs) {
                $education_id_arr[] = $parent;
                $education_id_arr = array_merge($education_id_arr, array_keys($childs));
            }
        }

        $found_education = empty(array_diff($education_id_arr, $mandatoryeducation_arr)) ? 1 : 0;

        // -------- USER AWARDS -------- //
        $award = DB::table("professional_membership")->where("user_id", $user->id)->first();
        $award_user_arr = [];

        foreach ((array) json_decode($award->award_recognitions) as $group) {
            foreach ($group as $a) {
                $award_user_arr[] = $a;
            }
        }

        $found_award = empty(array_diff($award_user_arr, $award_recognitionarr)) ? 1 : 0;

        // -------- MATCH PERCENT -------- //
        $match = $found_degree + $found_training + $found_education + $found_award;
        return round(($match / 4) * 100);
    }

    public function matchExperiencePercent($jobs, $user)
    {
        $user_experience = $user->assistent_level;
        

        $emplyeement_positionsarr = [];
        $experience_level_arr = [];
        foreach ($jobs as $job) {
            $experience_level_arr[] = $job->experience_level;
            foreach (json_decode($job->emplyeement_positions) as $emplyeement_positions) {
                $emplyeement_positionsarr[] = $emplyeement_positions;
            }
        }

        

        $found_experience = 0;
        if(in_array($user_experience,$experience_level_arr)){
            $found_experience = 1;
        }

        $user_position_data = DB::table("user_experience")->where("user_id", $user->id)->get();
        $user_positionsarr = [];
        
        foreach ($user_position_data as $user_position) {
            
            foreach (json_decode($user_position->position_held) as $position_held) {

                foreach($position_held as $position){
                    $user_positionsarr[] = $position;
                }
                
            }
        }

        $found_position = empty(array_diff($user_positionsarr, $emplyeement_positionsarr)) ? 1 : 0;

        //print_r($user_positionsarr);

        // -------- MATCH PERCENT -------- //
        $match = $found_experience + $found_position;
        return round(($match / 2) * 100);
    }
    
    public function matchedJobs(){

        $user = Auth::guard("nurse_middle")->user();
        $jobs = JobsModel::get();
        $data['jobs'] = $jobs;
        $workData = $this->matchSingleWorkEnvironmentPercent($jobs,$user);
        
        $data['employeement_type_data'] = DB::table("employeement_type_preferences")->where("sub_prefer_id",0)->get();
        $data['shift_type_data'] = DB::table("work_shift_preferences")->where("shift_id",0)->where("sub_shift_id",NULL)->get();
        $data['employee_positions'] = DB::table("employee_positions")->where("subposition_id",0)->get();
        $data['benefits_preferences'] = DB::table("benefits_preferences")->where("subbenefit_id",0)->get();
        $data['work_environment_data'] = DB::table("work_enviornment_preferences")
            ->where("sub_env_id", 0)
            ->where("sub_envp_id", 0)
            ->get();
        $data['work_shift_data'] = DB::table("work_shift_preferences")
            ->where("shift_id", 0)
            ->where("sub_shift_id", NULL)
            ->get();    
        $data['type_of_nurse'] = DB::table("practitioner_type")
            ->where("parent", 0)
            ->get();        
        $data['speciality'] = DB::table("speciality")
            ->where("parent", 0)
            ->get();     
        $user_id = Auth::guard('nurse_middle')->user()->id;    
        $data['work_preferences_data'] = DB::table("work_preferences")
            ->where("user_id", $user_id)
            ->first();    
        $data['saved_searches_data'] = DB::table("saved_searches")
            ->where("user_id", $user_id)
            ->get(); 
        return view("nurse.matchedjobsnew")->with($data);
    }
    
    public function matchSingleWorkEnvironmentPercent($jobs,$user)
    {

    }


}