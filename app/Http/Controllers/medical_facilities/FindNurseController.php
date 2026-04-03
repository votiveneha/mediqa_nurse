<?php

namespace App\Http\Controllers\medical_facilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\User\AuthServices;
use App\Http\Requests\UserUpdateProfile;
use App\Http\Requests\UserChangePasswordRequest;
use App\Models\JobsModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Str;
use Mail;
use Validator;
use DB;
use URL;
use Session;
use Helpers;
use App\Repository\Eloquent\SpecialityRepository;

class FindNurseController extends Controller
{
    public function index()
    {
        $user = Auth::guard('healthcare_facilities')->user();

        $jobs = JobsModel::where('healthcare_id', $user->id)->get();

        // foreach ($jobs as $job) {

        //     $job->display_name = $job->job_title;
        // }

        $nurse_list = User::where(['role' => '1','type' => '1', 'user_stage' => '2'])->orderBy('id', 'desc')->paginate(2);
        // echo "<pre>"; print_r($nurse_list);die;
        return view('healthcare.find_nurse.job_find_nurse', compact('jobs','nurse_list'));
    }

    public function getNurseSorting(Request $request)
    {
        $query = DB::table('users')
            ->select(
                'users.*',DB::raw('MAX(profession_data.assistent_level) as experience_level') 
            )      
            ->leftJoin('work_preferences', 'work_preferences.user_id', '=', 'users.id')
            ->leftJoin('user_licenses_details', 'user_licenses_details.user_id', '=', 'users.id')
            ->leftJoin('profession_data', 'profession_data.user_id', '=', 'users.id')
            ->leftJoin('speciality', 'speciality.id', '=', 'profession_data.specialties')
            ->leftJoin('practitioner_type', 'practitioner_type.id', '=', 'profession_data.nurse_data')
            ->groupBy('users.id');

        // Filter by nurse name (from users table)
        if ($request->nurse_registration) {
            $query->where(function ($q) use ($request) {
                $q->where('users.name', 'LIKE', '%' . $request->nurse_registration . '%')
                    ->orWhere('users.lastname', 'LIKE', '%' . $request->nurse_registration . '%')
                    ->orWhere(DB::raw("CONCAT(users.name, ' ', users.lastname)"), 'LIKE', '%' . $request->nurse_registration . '%')
                    ->orWhere('user_licenses_details.aphra_registration_no', 'LIKE', '%' . $request->nurse_registration . '%');
            });
        }


        // Filter by speciality OR practitioner type
        if ($request->role_speciality) {
            $query->where(function ($q) use ($request) {
                $q->where('speciality.name', 'LIKE', '%' . $request->role_speciality . '%')
                    ->orWhere('practitioner_type.name', 'LIKE', '%' . $request->role_speciality . '%');
            });
        }

        if ($request->available_to_start) {
            $query->where('users.start_job_dropdown',$request->available_to_start);
        }

        //sorting
        if ($request->sort_by == "highest_experience") {
            $query->orderBy('experience_level', 'DESC'); 
        }
        // elseif ($request->sort_by == "top_matches") {
        //     $query->orderBy('users.start_job_dropdown', 'ASC');
        // } elseif ($request->sort_by == "available_soonest") {
        //     $query->orderBy('users.created_at', 'DESC'); 
        // }
        // Apply fixed conditions

        //Search tab chip
        if ($request->search_id) {

            $saved = DB::table('job_boxes')
                ->where('job_box_id', $request->search_id)
                ->first();

            // print_r($saved); die;
            if ($saved) {

                //Sector
                if (!empty($saved->sector)) {
                    $query->where(function ($q) use ($saved) {
                        // Case 1: Public
                        if ($saved->sector == 1) {
                            $q->where('work_preferences.sector_preferences', 'LIKE', '%Public & Government%');
                        }
                        // Case 2: Private
                        elseif ($saved->sector == 2) {
                            $q->where('work_preferences.sector_preferences', 'LIKE', '%Private%');
                        }
                        // Case 3: Both
                        elseif ($saved->sector == 3) {
                            $q->where('work_preferences.sector_preferences', 'LIKE', '%Public Government & Private%');
                        }
                    });
                }

                // Nurse Type
                if (!empty($saved->nurse_type_id)) {
                    $nurseTypeIds = json_decode($saved->nurse_type_id, true);
                    $query->where('profession_data.nurse_data', $nurseTypeIds);
                }

                 //speciality
                if (!empty($saved->typeofspeciality)) {
                    $nurseSpecialties = json_decode($saved->typeofspeciality, true);
                    $query->where('profession_data.specialties', $nurseSpecialties);
                }

                // Emplyeement Type
                if (!empty($saved->emplyeement_type)) {

                    $employTypeIds = json_decode($saved->emplyeement_type, true);

                    $query->where(function ($q) use ($employTypeIds) {
                        foreach ($employTypeIds as $id) {
                            $q->orWhereRaw(
                                "JSON_SEARCH(work_preferences.emptype_preferences, 'one', ?) IS NOT NULL",
                                [(string)$id]
                            );
                        }
                    });
                }

                if (!empty($saved->shift_type)) {
                    $employTypeIds = json_decode($saved->shift_type, true);
                    $query->where(function ($q) use ($employTypeIds) {
                        foreach ($employTypeIds as $id) {
                            $q->orWhereRaw(
                                "JSON_SEARCH(work_preferences.work_shift_preferences, 'one', ?) IS NOT NULL",
                                [(string)$id]
                            );
                        }
                    });
                }

                // // Work Environment
                if (!empty($saved->filter_work_environment)) {

                    $nurseTypeIds = json_decode($saved->filter_work_environment, true);

                    $query->where(function ($q) use ($nurseTypeIds) {
                        foreach ($nurseTypeIds as $id) {
                            $q->orWhereJsonContains('work_environment', $id);
                        }
                    });
                }

                // //Benefit
                // if (!empty($saved->benefits)) {

                //     $benefitIds = json_decode($saved->benefits, true);

                //     $query->where(function ($q) use ($benefitIds) {
                //         foreach ($benefitIds as $id) {
                //             $q->orWhereRaw("FIND_IN_SET(?, work_preferences.benefits_preferences)", [$id]);
                //         }
                //     });
                // }
            }
       

        }
        $query->where(['users.role' => '1','users.type' => '1','users.user_stage' => '2']);

        // Get results
        $nurse_list = $query->get();

        // print_r($nurse_list);die;
        // Paginate manually
        $nurse_list = new \Illuminate\Pagination\LengthAwarePaginator(
            $nurse_list->forPage($request->page ?? 1, 2),
            $nurse_list->count(),
            2,
            $request->page ?? 1
        );

        if ($nurse_list->count() == 0) {
            return response()->json([
                'status' => false,
                'html' => ''
            ]);
        }

        return response()->json([
            'status' => true,
            'html' => view('healthcare.find_nurse.partial_find_nurse', compact('nurse_list'))->render()
        ]);
    }


}
