<?php

namespace App\Http\Controllers\admin;
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
use App\Models\User;
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

class HealthcareController extends Controller
{
    public function index()
    {
        $data['healthcare_list'] = User::where("role",2)->where('email_verify','1')->where('user_stage','1')->orderBy('created_at','desc')->get();
        return view("admin.healthcare.healthcare_list")->with($data);
    }
}