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
}
