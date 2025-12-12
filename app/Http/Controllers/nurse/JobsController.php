<?php

namespace App\Http\Controllers\nurse;
use App\Http\Requests\AddnewsletterRequest;

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
use Helpers;
use Mail;
use Validator;
use DB;
use URL;
use Session;
use File;
use App\Services\Admins\SpecialityServices;
use Illuminate\Support\Facades\Storage;
use App\Models\SavedSearches;
use App\Models\JobsModel;

class JobsController extends Controller{
    
    public function index()
    {
        $this->ensureDefaultSearch();
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
        $today = now();    

        foreach ($data['saved_searches_data'] as $search) {
            $filters = json_decode($search->filters, true);

            $query = JobsModel::query();

            // Example: Apply filters dynamically
            if (!empty($filters['sector'])) {
                $query->where('sector', $filters['sector']);
            }

            // Employment type (multiple IDs)
            if (!empty($filters['employment_type'])) {
                $query->whereIn('emplyeement_type', $filters['employment_type']);
            }

            // Work shift (multiple IDs)
            if (!empty($filters['work_shift'])) {
                $query->whereIn('shift_type', $filters['work_shift']);
            }

            // Work environment
            if (!empty($filters['work_environment'])) {
                $query->whereIn('work_environment', $filters['work_environment']);
            }

            // Employee positions
            if (!empty($filters['employee_positions'])) {
                $query->whereIn('emplyeement_positions', $filters['employee_positions']);
            }
            // if (!empty($filters['nurse_type'])) {
            //     $query->whereIn('nurse_type', (array) $filters['nurse_type']);
            // }
            // if (!empty($filters['speciality'])) {
            //     $query->whereIn('speciality', (array) $filters['speciality']);
            // }

            // Only jobs added today
            $query->whereDate('created_at', $today);

            // Count matching jobs
            $data['query_count'] = $query->count();
        }
    
            
        $data['location_status'] = "";    
        if(!empty($data['work_preferences_data']) && $data['work_preferences_data']->location_status == "International relocation"){    
            $international_location = json_decode($data['work_preferences_data']->countries);
            $country_name_arr = [];
            if(!empty($international_location)){
                foreach($international_location as $inter_loc){
                    if($inter_loc != "Other"){
                        $countdata = DB::table("countries")->where("id",$inter_loc)->first();
                        $country_name_arr[] = $countdata->name; 
                    }
                }
            }    
            $other_countries = [];
            if (in_array("Other", $international_location)) {
                $other_location = json_decode($data['work_preferences_data']->other_countries);
                foreach($other_location as $other_loc){
                    $countdata = DB::table("countries")->where("id",$other_loc)->first();
                    $other_countries[] = ucwords(strtolower($countdata->name)); 
                    
                }
                
            }
            
            $data['location_status'] = "international_location";
            $country_merge = array_merge($country_name_arr, $other_countries);
        }

        if(!empty($data['work_preferences_data']) && $data['work_preferences_data']->location_status == "Current Location area (not willing to relocate)"){
            $address = $data['work_preferences_data']->prefered_location_current;
            $parts = explode(",", $address);
            $country_merge = trim(end($parts));
            $data['location_status'] = "current_location";
        }

        

        if(!empty($data['work_preferences_data']) && $data['work_preferences_data']->location_status == "Multiple locations area (relocation within your country)"){
            $address = json_decode($data['work_preferences_data']->prefered_location);
            $country_merge = [];
            if(!empty($address)){
                foreach($address as $add){
                    
                    $parts = explode(",", $add->location);
                    $country_merge[] = trim(end($parts));
                }
            }
            
            $data['location_status'] = "multiple_location";
        }
        $country_merge = '';
        if(!empty($data['work_preferences_data']) && $data['work_preferences_data']->location_status == NULL){
            $country_merge = '';
            $data['location_status'] = "";
        }
        
        $data['country_name'] = $country_merge;
        $data['user_data'] = DB::table("users")->where("id",$user_id)->first();
                   
        $data['jobs'] = DB::table("job_boxes")->get();                
        return view('nurse.find_jobs')->with($data);
    }

    public function capitalizeFirstTwo($string) {
        $result = '';
        $words = explode(' ', strtolower($string)); // split words, normalize to lowercase
        foreach ($words as $word) {
            $part1 = strtoupper(substr($word, 0, 2)); // first 2 letters uppercase
            $part2 = substr($word, 2);               // rest normal
            $result .= $part1 . $part2 . ' ';
        }
        return trim($result);
    }

    public function getWorkFlexiblityData(Request $request)
    {
        $table_name = $request->table_name;
        $column_name = $request->column_name;
        $main_column_id = $request->main_column_id;
        $column_type = $request->column_type;

        $employeement_type_data = DB::table($table_name)->where($column_name,0)->get()->map(function ($item) {
            return (array) $item;
        })->toArray();
        //print_r($employeement_type_data);die;
        $nested = [];
        foreach($employeement_type_data as $employeement_type){
            $subemp_type = DB::table($table_name)->where($column_name,$employeement_type[$main_column_id])->get();
            $nested[] = [
                'id' => $employeement_type[$main_column_id],
                'name' => $employeement_type[$column_type], // Adjust field names as per your DB
                'sub_types' => $subemp_type->map(function($item) use ($main_column_id, $column_type) {
                    $items = (array)$item;
                    return [
                        'id' => $items[$main_column_id],
                        'name' => $items[$column_type] // Adjust as needed
                    ];
                })
            ];
        }

        $user_id = Auth::guard('nurse_middle')->user()->id;    
        $work_preferences_data = DB::table("work_preferences")
            ->where("user_id", $user_id)
            ->first();    

        $response = [
            'filters'     => $nested,
            'preferences' => $work_preferences_data,
        ];

           

        // Convert to JSON (optional)
        $json = json_encode($response, JSON_PRETTY_PRINT);

        return $json;

    }

    public function getWorkEnvironmentData(Request $request)
    {
        $employeement_type_data = DB::table("work_enviornment_preferences")
            ->where("sub_env_id", 0)
            ->where("sub_envp_id", 0)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        $nested = [];

        foreach ($employeement_type_data as $employeement_type) {
            $subemp_type = DB::table("work_enviornment_preferences")
                ->where("sub_env_id", $employeement_type['prefer_id'])
                ->where("sub_envp_id", 0)
                ->get();

            $nested[] = [
                'id' => $employeement_type['prefer_id'],
                'name' => $employeement_type['env_name'],
                'sub_types' => $subemp_type->map(function ($item) {
                    $items = (array) $item;

                    // Third level: subsub_types
                    $subsub_type = DB::table("work_enviornment_preferences")
                        ->where("sub_envp_id", $items['prefer_id']) // 3rd level based on sub_envp_id
                        ->get()
                        ->map(function ($third) {
                            $third_item = (array) $third;
                            return [
                                'id' => $third_item['prefer_id'],
                                'name' => $third_item['env_name']
                            ];
                        });

                    return [
                        'id' => $items['prefer_id'],
                        'name' => $items['env_name'],
                        'subsub_types' => $subsub_type
                    ];
                })
            ];
        }

        $json = json_encode($nested, JSON_PRETTY_PRINT);

        // Return as JSON
        return $json;
    }

    public function getJobsSorting(Request $request){
        $sort_name = $request->sort_name;

        if($sort_name == "most_recent"){
            $data['jobs'] = DB::table("job_boxes")->orderBy('id','desc')->get();
        }

        if($sort_name == "urgent_hire"){
            $data['jobs'] = DB::table("job_boxes")->orderBy('urgent_hire','desc')->get();
            //print_r($data['jobs']);die;
        }

        if($sort_name == "application_deadline"){
            $data['jobs'] = DB::table("job_boxes")->orderBy('application_submission_date','asc')->get();
            //print_r($data['jobs']);die;
        }
        
        return view("nurse.job_filter_data")->with($data);
    }

    public function getNurseData(Request $request)
    {
        $nurse_id = $request->nurse_id;
        $nurse_data = DB::table("practitioner_type")->where("parent",$nurse_id)->get();
        
        $ndatas = array();
        foreach($nurse_data as $ndata){
            
            $nsubarr = array();
            $get_nurse_count = DB::table("practitioner_type")->where("parent",$ndata->id)->get();

            foreach($get_nurse_count as $get_nurse){
                $nsubarr[] = array("id"=>$get_nurse->id,"name"=>$get_nurse->name,"parent"=>$get_nurse->parent);
            }
            
            $ndatas[] = array("id"=>$ndata->id,"name"=>$ndata->name,"parent"=>$ndata->parent,"get_nurse_count"=>count($get_nurse_count),"get_nurse"=>$nsubarr);
        }
        return json_encode($ndatas);
    }

    public function getSpecialityData(Request $request)
    {
        $specality_id = $request->speciality_id;
        $specality_data = DB::table("speciality")->where("parent",$specality_id)->get();
        
        $specdatas = array();
        foreach($specality_data as $specdata){
            
            $specsubarr = array();
            $get_spec_count = DB::table("speciality")->where("parent",$specdata->id)->get();

            foreach($get_spec_count as $get_spec){
                $specsubarr[] = array("id"=>$get_spec->id,"name"=>$get_spec->name,"parent"=>$get_spec->parent);
            }
            
            $specdatas[] = array("id"=>$specdata->id,"name"=>$specdata->name,"parent"=>$specdata->parent,"get_spec_count"=>count($get_spec_count),"get_spec"=>$specsubarr);
        }
        return json_encode($specdatas);
    }

    public function getFilterData(Request $request){
        $filter_name = $request->filter_name;
        $searchValues = $request->selectedValues;
        $selectedValues = $request->selectedValues;
        //print_r($selectedValues);die;

        
        if($filter_name == "Employment Type"){
            $data['jobs'] = DB::table("job_boxes")->where(function($query) use ($selectedValues) {
                foreach ($selectedValues as $id) {
                    $query->orWhereJsonContains('emplyeement_type', (string) $id);
                }
            })->get();
            
        }    

        if($filter_name == "Position"){
            $data['jobs'] = DB::table("job_boxes")->where(function($query) use ($selectedValues) {
                foreach ($selectedValues as $id) {
                    $query->orWhereJsonContains('emplyeement_positions', (string) $id);
                }
            })->get();
        }    

        if($filter_name == "Benefits"){
            $data['jobs'] = DB::table("job_boxes")->where(function($query) use ($selectedValues) {
                foreach ($selectedValues as $id) {
                    $query->orWhereJsonContains('benefits', (string) $id);
                }
            })->get();
        }    
        //print_r($jobs);
        
        if($filter_name == "sector"){
            $data['jobs'] = DB::table("job_boxes")->whereIn("sector",$selectedValues)->get();
        }    
        //print_r($data);die;
        return view("nurse.job_filter_data")->with($data);

    }

    public function getExperienceData(Request $request){
        $experience = $request->experience;

        $data['jobs'] = DB::table("job_boxes")->where("experience_level",$experience)->get();

        return view("nurse.job_filter_data")->with($data);
    }

    public function getFilterNurseData(Request $request){
        $nurse_data = $request->nurse_data;

        $data['jobs'] = DB::table('job_boxes')
        ->where(function($query) use ($nurse_data) {
            foreach ($nurse_data as $value) {
                $query->orWhere('nurse_type', 'LIKE', '%"'.$value.'"%');
            }
        })
        ->get();

        return view("nurse.job_filter_data")->with($data);
    }

    public function getFilterSpecialityData(Request $request){
        $speciality_data = $request->speciality_data;

        $data['jobs'] = DB::table('job_boxes')
        ->where(function($query) use ($speciality_data) {
            foreach ($speciality_data as $value) {
                $query->orWhere('typeofspeciality', 'LIKE', '%"'.$value.'"%');
            }
        })
        ->get();

        return view("nurse.job_filter_data")->with($data);
    }

    public function updateSectorData(Request $request){
        $sector_data = $request->sector_data;

        $user_id = Auth::guard("nurse_middle")->user()->id;

        $updateWorkPreferencesFlexiblity = DB::table("work_preferences")->where("user_id",$user_id)->update(['sector_preferences'=>$sector_data]);
    }

    public function applyJobs(Request $request){
        $user_id = $request->user_id;
        $job_id = $request->job_id; 

        $applyJobs = DB::table("job_apply")->insert(["user_id"=>$user_id,"job_id"=>$job_id]);

        if($applyJobs == 1){
            return $applyJobs;
        }
    }

    public function addSavedSearches(Request $request){
        
        $user_id = Auth::guard('nurse_middle')->user()->id;
        $search_id = $request->search_id;
        $filter_location = $request->edit_filter_location;
        $filter_shift = $request->edit_filter_shift;
        $filter_preview = $request->edit_filter_preview;
        $minSalary1 = $request->minSalary1;
        $maxSalary1 = $request->maxSalary1;
        $year_experience = $request->year_experience;
        $edit_alert_cap = $request->edit_alert_cap;
        $edit_quiet_start = $request->edit_quiet_start;
        $edit_quiet_end = $request->edit_quiet_end;
        $edit_search_notes = $request->edit_search_notes;
        $suggestion_search_name = $request->suggestion_search_name;
        $search_type = $request->search_type;
        $alert_frequency = $request->alert_frequency;
        $delivery_method = $request->delivery_method;
        $filters = $request->filters;
        $date = date("Y-m-d H:i:s");

        $totalSearches = SavedSearches::where('user_id', $user_id)->count();


        if($search_id){
            $oldSearch = SavedSearches::find($search_id);
            //$oldSearch->name = $search_name;
            $oldSearch->alert = $alert_frequency;
            $oldSearch->delivery = $delivery_method;
            $oldSearch->filters = $filters;
            $oldSearch->salary_min = $minSalary1;
            $oldSearch->salary_max = $maxSalary1;
            $oldSearch->experience = $year_experience;
            // $oldSearch->location = $filter_location;
            // $oldSearch->shift = $filter_shift;
            // $oldSearch->preview_count = $filter_preview;
            // $oldSearch->daily_cap = $edit_alert_cap;
            $oldSearch->quite_hours_start = $edit_quiet_start;
            $oldSearch->quite_hours_end = $edit_quiet_end;
            $oldSearch->notes = $edit_search_notes;
            $oldSearch->updated_at = $date;
            $run = $oldSearch->save();
            $lastInsertedId = $oldSearch->searches_id;
        }else{
            $saved_searches = new SavedSearches();
            $saved_searches->user_id = $user_id;
            //$saved_searches->name = $search_name;
            $saved_searches->type = $search_type;
            $saved_searches->alert = $alert_frequency;
            $saved_searches->delivery = $delivery_method;
            $saved_searches->filters = $suggestion_search_name;
            $saved_searches->created_at = $date;
            $run = $saved_searches->save();

            $lastInsertedId = $saved_searches->id;
        }
        

        if ($run) {
            $json['status'] = 1;
            $json['id'] = $lastInsertedId;
        } else {
            $json['status'] = 0;
            
        }

        echo json_encode($json);
    }

    public function deleteSearchJobsData(Request $request){
        $searches_id = $request->searches_id;
        $delete_search_data = DB::table("saved_searches")->where("searches_id",$searches_id)->delete();

        
        echo $delete_search_data;
    }

    public function duplicateSearch(Request $request)
    {
        
        $oldSearch = SavedSearches::where('searches_id', $request->searches_id)->first();
        $user_id = Auth::guard('nurse_middle')->user()->id;

        if (!$oldSearch) {
            return response()->json(['success' => false, 'message' => 'Original search not found']);
        }

        $duplicate = new SavedSearches();
        $duplicate->user_id = $user_id;
        $duplicate->name = $request->name;
        $duplicate->filters = $request->filter_json;
        $duplicate->alert = $request->alert;
        $duplicate->delivery = $request->delivery;
        $duplicate->created_at = now();
        $duplicate->save();

        return response()->json([
            'success' => true,
            'new_id' => $duplicate->id
        ]);
    }

    public function getEditSearchData(Request $request)
    {
        
        $SearchData = SavedSearches::where('searches_id', $request->id)->first();

        $search_data = json_encode($SearchData);

        //print_r($search_data);

        return $search_data;
    }

    public function deleteMultipleSearches(Request $request)
    {
        if (!$request->has('ids')) {
            return response()->json(['status' => 'error', 'message' => 'No IDs provided']);
        }

        SavedSearches::whereIn('searches_id', $request->ids)->delete();

        return response()->json(['status' => 'success']);
    }

    public function deleteSingleSearch(Request $request)
    {
        SavedSearches::where('searches_id', $request->id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function ensureDefaultSearch()
    {
        $user_id = Auth::guard('nurse_middle')->user()->id;

        $exists = SavedSearches::where('user_id', $user_id)
            ->where('type', 'dynamic')
            ->where('name', 'My Preferences')
            ->first();

        if (!$exists) {
            SavedSearches::create([
                'user_id' => $user_id,
                'name' => 'My Preferences',
                'type' => 'dynamic',
                'alert' => 'Off'
            ]);
        }
    }

   public function getEmpDataSearch(Request $request)
    {
        $id = $request->sub_prefer_id;
        $filterType = $request->filter_type;

        $response = $this->getFilterDataRecursive($filterType, $id);

        return response()->json($response);
    }

    private function getFilterDataRecursive($filterType, $parentId)
    {
        switch ($filterType) {
            case 'employment_type':
                $main = DB::table('employeement_type_preferences')
                            ->where('emp_prefer_id', $parentId)
                            ->first();

                $subs = DB::table('employeement_type_preferences')
                            ->where('sub_prefer_id', $parentId)
                            ->get();

                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->emp_prefer_id,
                        'name' => $s->emp_type,
                        'subtypes' => $this->getFilterDataRecursive('employment_type', $s->emp_prefer_id)['subtypes'],
                    ];
                }

                return [
                    'main' => [
                        'id' => $main->emp_prefer_id,
                        'name' => $main->emp_type,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];

            case 'work_shift':
                $main = DB::table('work_shift_preferences')
                            ->where('work_shift_id', $parentId)
                            ->first();

                $subs = DB::table('work_shift_preferences')
                        ->where(function ($q) use ($parentId) {
                            // Level 2: direct sub-shifts (no sub_shift_id)
                            $q->where(function ($q2) use ($parentId) {
                                $q2->where('shift_id', $parentId)
                                ->whereNull('sub_shift_id');
                            })
                            // Level 3: sub-shifts under another sub-shift
                            ->orWhere(function ($q3) use ($parentId) {
                                $q3->where('sub_shift_id', $parentId);
                            });
                        })
                        ->get();



                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->work_shift_id,
                        'name' => $s->shift_name,
                        'subtypes' => $this->getFilterDataRecursive('work_shift', $s->work_shift_id)['subtypes'],
                    ];
                }

                return [
                    'main' => [
                        'id' => $main->work_shift_id ?? null,
                        'name' => $main->shift_name ?? null,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];
            case 'work_environment':
                // Fetch the main parent record
                $main = DB::table('work_enviornment_preferences')
                    ->where('prefer_id', $parentId)
                    ->first();

                $subs = DB::table('work_enviornment_preferences')
                        ->where(function ($q) use ($parentId) {
                            // Level 2: direct sub-environments (sub_env_id = parentId AND sub_envp_id = 0 or null)
                            $q->where(function ($q2) use ($parentId) {
                                $q2->where('sub_env_id', $parentId)
                                ->where(function ($q3) {
                                    $q3->whereNull('sub_envp_id')
                                        ->orWhere('sub_envp_id', 0);
                                });
                            })
                            // Level 3: deeper sub-environments (sub_envp_id = parentId)
                            ->orWhere(function ($q4) use ($parentId) {
                                $q4->where('sub_envp_id', $parentId);
                            });
                        })
                        ->get();


                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->prefer_id,
                        'name' => $s->env_name,
                        'subtypes' => $this->getFilterDataRecursive('work_environment', $s->prefer_id)['subtypes'] ?? [],
                    ];
                }

                // Final structured response
                return [
                    'main' => [
                        'id' => $main->prefer_id ?? null,
                        'name' => $main->env_name ?? null,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];

            case 'employee_positions':
                $main = DB::table('employee_positions')
                            ->where('position_id', $parentId)
                            ->first();

                $subs = DB::table('employee_positions')
                            ->where('subposition_id', $parentId)
                            ->get();

                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->position_id,
                        'name' => $s->position_name,
                        'subtypes' => $this->getFilterDataRecursive('employee_positions', $s->position_id)['subtypes'],
                    ];
                }

                return [
                    'main' => [
                        'id' => $main->position_id,
                        'name' => $main->position_name,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];    

            case 'benefits_preferences':
                $main = DB::table('benefits_preferences')
                            ->where('benefits_id', $parentId)
                            ->first();

                $subs = DB::table('benefits_preferences')
                            ->where('subbenefit_id', $parentId)
                            ->get();

                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->benefits_id,
                        'name' => $s->benefits_name,
                        'subtypes' => $this->getFilterDataRecursive('benefits_preferences', $s->benefits_id)['subtypes'],
                    ];
                }

                return [
                    'main' => [
                        'id' => $main->benefits_id,
                        'name' => $main->benefits_name,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];        

            case 'nurse_type':
                // Fetch the main parent record
                $main = DB::table('practitioner_type')
                    ->where('id', $parentId)
                    ->first();

                $subs = DB::table('practitioner_type')
                                ->where('parent', $parentId)
                                ->get();


                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'subtypes' => $this->getFilterDataRecursive('work_environment', $s->id)['subtypes'] ?? [],
                    ];
                }

                // Final structured response
                return [
                    'main' => [
                        'id' => $main->id ?? null,
                        'name' => $main->name ?? null,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];    

            case 'speciality':
                // Fetch the main parent record
                $main = DB::table('speciality')
                    ->where('id', $parentId)
                    ->first();

                $subs = DB::table('speciality')
                                ->where('parent', $parentId)
                                ->get();


                $subtypes = [];
                foreach ($subs as $s) {
                    $subtypes[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'subtypes' => $this->getFilterDataRecursive('speciality', $s->id)['subtypes'] ?? [],
                    ];
                }

                // Final structured response
                return [
                    'main' => [
                        'id' => $main->id ?? null,
                        'name' => $main->name ?? null,
                    ],
                    'subtypes' => $subtypes,
                    'has_subtypes' => count($subtypes) > 0,
                ];        


            default:
                return [
                    'main' => null,
                    'subtypes' => [],
                    'has_subtypes' => false,
                ];
        }
    }

    public function getSavedSearch(Request $request)
    {
        $searches_id = $request->id;
        $search = SavedSearches::find($searches_id);

        $filters = json_decode($search->filters, true); // decode JSON to array

        return response()->json([
            'search' => $search,
            'filters' => $filters,
        ]);
    }


    public function removeFilter(Request $request, $id)
    {
        $savedSearch = SavedSearches::findOrFail($id);
        $filters = json_decode($savedSearch->filters, true) ?? [];

        $value = $request->input('value');

        // Remove the value from the array
        $filters = array_values(array_filter($filters, function($v) use ($value) {
            return $v !== $value;
        }));

        // Update the record
        $savedSearch->filters = json_encode($filters, JSON_UNESCAPED_UNICODE);
        $savedSearch->save();

        return response()->json([
            'status' => 'success',
            'filters' => $filters
        ]);
    }


    public function updateAlert(Request $request)
    {
        $searches_id = $request->search_id;
        $search = SavedSearches::find($searches_id);
        $search->alert = $request->frequency_value;
        $run = $search->save();
    }


    public function run($id)
    {
        // Step 1: Get saved search by ID
        $savedSearch = SavedSearches::find($id);

        if (!$savedSearch) {
            return response()->json(['error' => 'Saved search not found.'], 404);
        }

        // Step 2: Decode filters (JSON string → PHP array)
        $filters = json_decode($savedSearch->filters, true);

        if (empty($filters) || !is_array($filters)) {
            return response()->json(['message' => 'No filters available for this search.'], 200);
        }

        // Step 3: Build the query dynamically
        $query = DB::table('job_boxes');

        // Sector (single value)
        if (!empty($filters['sector'])) {
            $query->where('sector', $filters['sector']);
        }

        // Employment type (multiple IDs)
        if (!empty($filters['employment_type'])) {
            $query->whereIn('emplyeement_type', $filters['employment_type']);
        }

        // Work shift (multiple IDs)
        if (!empty($filters['work_shift'])) {
            $query->whereIn('shift_type', $filters['work_shift']);
        }

        // Work environment
        if (!empty($filters['work_environment'])) {
            $query->whereIn('work_environment', $filters['work_environment']);
        }

        // Employee positions
        if (!empty($filters['employee_positions'])) {
            $query->whereIn('emplyeement_positions', $filters['employee_positions']);
        }

        // Nurse type
        // if (!empty($filters['nurse_type'])) {
        //     $query->whereIn('nurse_type_id', $filters['nurse_type']);
        // }

        // // Speciality
        // if (!empty($filters['speciality'])) {
        //     $query->whereIn('speciality_id', $filters['speciality']);
        // }

        // Salary range
        if (!empty($filters['salary_range'])) {
            $min = $filters['salary_range']['min'] ?? null;
            $max = $filters['salary_range']['max'] ?? null;

            if ($min && $max) {
                $query->whereBetween('salary', [$min, $max]);
            }
        }

        // Years of experience
        if (!empty($filters['years_of_experience'])) {
            $query->where('experience_level', '>=', $filters['years_of_experience']);
        }

        // Step 4: Get results
        $results = $query->get();

        // Step 5: Update "last_run_at" for tracking
        $savedSearch->update(['last_run_at' => now()]);

        // Step 6: Return response
        return response()->json([
            'message' => 'Search executed successfully.',
            'filters' => $filters,
            'results' => $results
        ]);
    }

    public function get_tags()
    {
        $employeement_type = DB::table("employeement_type_preferences")->where("sub_prefer_id","!=","0")->get();
        $shift_type = DB::table("work_shift_preferences")->where("shift_id","!=","0")->get();
        $work_environment = DB::table("work_enviornment_preferences")->where("sub_env_id","!=","0")->get();
        $employee_positions = DB::table("employee_positions")->where("subposition_id","!=","0")->get();
        $benefits = DB::table("benefits_preferences")->where("subbenefit_id","!=","0")->get();
        $practitioner_type = DB::table("practitioner_type")->where("parent","!=","0")->get();
        $speciality = DB::table("speciality")->where("parent","!=","0")->get();

        $allTags = [];
        //print_r($employeement_type);die;
        foreach($employeement_type as $emp_type){
            $allTags[] = $emp_type->emp_type;
        }

        foreach($shift_type as $stype){
            $allTags[] = $stype->shift_name;
        }

        foreach($work_environment as $wenvironment){
            $allTags[] = $wenvironment->env_name;
        }

        foreach($employee_positions as $employee_pos){
            $allTags[] = $employee_pos->position_name;
        }

        foreach($benefits as $bene){
            $allTags[] = $bene->benefits_name;
        }

        foreach($practitioner_type as $practype){
            $allTags[] = $practype->name;
        }

        foreach($speciality as $spec){
            $allTags[] = $spec->name;
        }

        $allTags = array_merge($allTags, ['Public & Government', 'Private', 'Public Government & Private']);

        return json_encode($allTags);
    }

    public function get_filters_data()
    {
        $user_id = Auth::guard('nurse_middle')->user()->id;
        $work_prefer_data = DB::table("work_preferences")->where("user_id",$user_id)->first();

        $sector_data = $work_prefer_data->sector_preferences;
        $emptype_preferences = json_decode($work_prefer_data->emptype_preferences);
        $worktype_preferences = json_decode($work_prefer_data->work_environment_preferences);

        $saved_filters = [];
        if(!empty($emptype_preferences)){
            foreach($emptype_preferences as $emptype_prefer){
                foreach($emptype_prefer as $emptype_prefer1){
                    $employeement_type = DB::table("employeement_type_preferences")->where("emp_prefer_id",$emptype_prefer1)->first();
                    $saved_filters[] = $employeement_type->emp_type;
                }
            }
        }

        if(!empty($worktype_preferences)){
            foreach($worktype_preferences as $workpreferences){
                foreach($workpreferences as $index=>$workpreferences1){
                    $workprefertype = DB::table("work_enviornment_preferences")->where("prefer_id",$index)->first();
                    $saved_filters[] = $workprefertype->env_name;
                    foreach($workpreferences1 as $workpreferences2){
                        $workprefertype1 = DB::table("work_enviornment_preferences")->where("prefer_id",$workpreferences2)->first();
                        $saved_filters[] = $workprefertype1->env_name;
                    }
                }
            }
        }

        $saved_filters = array_merge($saved_filters, [$sector_data]);

        //print_r($saved_filters);

        return json_encode($saved_filters);
    }

    public function getEmptypeData(Request $request)
    {
        $emp_ids = json_decode($request->id_arr);

        $emp_name_arr = [];    
        foreach($emp_ids as $emp_id){
            $emp_prefer_data = DB::table("employeement_type_preferences")->where("emp_prefer_id",$emp_id)->first();
            $emp_name_arr[] = $emp_prefer_data->emp_type;
        }

        return json_encode($emp_name_arr);
        
    }



    

}