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

        foreach ($jobs as $job) {

            $job->display_name = $job->job_title;
        }

        $nurse_list = User::where(['role' => '1','type' => '1', 'user_stage' => '2'])->orderBy('id', 'desc')->paginate(2);
        // echo "<pre>"; print_r($nurse_list);die;
        return view('healthcare.find_nurse.job_find_nurse', compact('jobs','nurse_list'));
    }

    public function getNurseSorting(Request $request)
    {
        $query = DB::table('users')
            ->select('users.*')
        
            ->leftJoin('profession_data', 'profession_data.user_id', '=', 'users.id')
            ->leftJoin('speciality', 'speciality.id', '=', 'profession_data.specialties')
            ->leftJoin('practitioner_type', 'practitioner_type.id', '=', 'profession_data.nurse_data')
            ->groupBy('users.id');

        // Filter by nurse name (from users table)
        if ($request->nurse_registration) {
            $query->where('users.name', 'LIKE', '%' . $request->nurse_registration . '%');
        }

        // Filter by speciality OR practitioner type
        if ($request->role_speciality) {
            $query->where(function ($q) use ($request) {
                $q->where('speciality.name', 'LIKE', '%' . $request->role_speciality . '%')
                    ->orWhere('practitioner_type.name', 'LIKE', '%' . $request->role_speciality . '%');
            });
        }

        // Apply fixed conditions
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

    public function getNurseSorting_old(Request $request)
    {
        $query = DB::table('users')
            ->select('users.*') // explicitly select user fields
            ->leftJoin('profession_data', 'profession_data.user_id', '=', 'users.id')
            ->leftJoin('speciality', 'speciality.id', '=', 'profession_data.specialties')
            ->leftJoin('practitioner_type', 'practitioner_type.id', '=', 'profession_data.nurse_data');

        // Filter by nurse name (from users table)
        if ($request->nurse_registration) {
            $query->where('users.name', 'LIKE', '%' . $request->nurse_registration . '%');
        }

        // Filter by speciality name (optional)
        if ($request->role_speciality) {
            $query->where(function ($q) use ($request) {
                $q->where('speciality.name', 'LIKE', '%' . $request->role_speciality . '%')
                    ->orWhere('practitioner_type.name', 'LIKE', '%' . $request->role_speciality . '%');
            });
        }

        // Apply fixed conditions
        $query->where(['users.role' => '1','users.type' => '1','users.user_stage' => '2']);

        // Get results
        $nurse_list = $query->get();

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
