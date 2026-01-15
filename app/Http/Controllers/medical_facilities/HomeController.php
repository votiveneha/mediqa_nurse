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

class HomeController extends Controller
{
 protected $authServices;
 protected $specialityRepository;
      public function __construct(AuthServices $authServices, SpecialityRepository $specialityRepository){
        $this->authServices = $authServices;
        $this->specialityRepository = $specialityRepository;
    }
    public function index($message = '')
    {
         if (!Auth::guard('nurse_middle')->check()) {
            $title = "Login";
            return view('nurse.home', compact( 'message'));
        } else {
            

            return redirect()->route('nurse.dashboard');
        }
        
    }
    public function index_main($message = '')
    {
         if (!Auth::guard('nurse_middle')->check()) {
            $title = "Login";
            $practitioner_data = SpecialityModel::where("status",'1')->get();
            //print_r($practitioner_data);die;
            $speciality_data = PractitionerTypeModel::where("status",'1')->get();
            $work_preferences_data = WorkPreferModel::get();
           return view('nurse.medical-facilities', compact( 'message','practitioner_data','speciality_data','work_preferences_data'));
        } else {
            return redirect()->route('nurse.dashboard');
        }
        
    }
    public function registraion($message = '')
    {
         if (!Auth::guard('nurse_middle')->check()) {
            $title = "Login";
            $practitioner_data = SpecialityModel::where("status",'1')->get();
            //print_r($practitioner_data);die;
            $speciality_data = PractitionerTypeModel::where("status",'1')->get();
            $work_preferences_data = WorkPreferModel::get();
           return view('healthcare.medical-facilities-registraion', compact( 'message','practitioner_data','speciality_data','work_preferences_data'));
        } else {
            return redirect()->route('nurse.dashboard');
        }
        
    }

    public function healthcareRegistration(Request $request)
    {
        
        $hospital_name = $request->hospital_name;
        $emailaddress = $request->emailaddress;
        $mobile_no = $request->mobile_no;
        $post_code = $request->post_code;
        $address = $request->address;
        $password = $request->password;
        
        $user_data = User::where("email",$emailaddress)->first(); 

        if(empty($user_data)){
            $user = new User();
            $user->name = $hospital_name;
            $user->email = $emailaddress;
            $user->role = "healthcare-facilities";
            $user->password = Hash::make($password);
            $run = $user->save();
            Auth::guard('healthcare_facilities')->login($user);
            Auth::login($user);
        }else{
            $run = 0;
        }

        if ($run) {
            Session::put('user_id', $user->id);
            $json['status'] = 1;
            $json['message'] = 'Congratulations! Your registration was successful. Please check your email; we have sent you a verification email to your registered address!';
        }else{
            $json['status'] = 0;
            $json['message'] = 'Email is already registered.!';
        }
        echo json_encode($json);
    }

    public function login($message = '')
    {
        $practitioner_data = SpecialityModel::where("parent",0)->get();
        $speciality_data = PractitionerTypeModel::where("parent",0)->get();
        $work_preferences_data = WorkPreferModel::where("sub_env_id",0)->where("sub_envp_id",0)->get();
        $title = "Login";
        //$prefix = $request->segment(2);die;
        return view('nurse.login', compact('title', 'message','practitioner_data','speciality_data','work_preferences_data'));
    }

    public function userloginAction(Request $request)
    {
        
        if (Auth::guard('healthcare_facilities')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'healthcare-facilities'])) {
            if (isset($request->remember_me) && !empty($request->remember_me)) {
                setcookie("email", $request->email, time() + 3600);
                setcookie("password", $request->password, time() + 3600);
            } else {
                setcookie("email", "");
                setcookie("password", "");
            }
            return redirect('/healthcare-facilities/my-profile')->with('success', 'You are Logged in sucessfully.');
        } else {
            return back()->with('error', 'Invalid login details.');
        }
    }

     public function logout(Request $request)
    {
        Auth::guard('healthcare_facilities')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('healthcare-facilities');
    }

    public function emailVerificationPending()
    {

        if (Auth::guard('healthcare_facilities')->user()) {

            if (Auth::guard('healthcare_facilities')->user()->emailVerified == 1 &&  Auth::guard('healthcare_facilities')->user()->user_stage == 1 && Auth::guard('healthcare_facilities')->user()->type == 1) {

                return redirect()->route('medical-facilities.profile-under-reviewed');
            } elseif (Auth::guard('healthcare_facilities')->user()->emailVerified == 1 &&  Auth::guard('healthcare_facilities')->user()->type == 0) {
                return redirect()->route('medical-facilities.dashboard');
            } else {
                $title = "";
                $message = "";

                return view('auth.email-verification-pending', compact('title', 'message'));
            }
        } else if (Session::get('user_id')) {
            $user_id = Session::get('user_id');

            $title = 'sa';
            $message = "";
            $r = User::where("id", $user_id)->first();
            Auth::guard('healthcare_facilities')->attempt(['email' => $r->email, 'password' => $r->ps]);
            // return redirect('/nurse/my-profile?page=my_profile');
            return redirect('/nurse/dashboard');
            return view('auth.email-verification-pending', compact('title', 'message'));
        } else {
            $title = "s";
            return redirect()->route('healthcare-facilities.login');
        }
    }

    public function manage_profile(){
        return view('healthcare.profile');
    }

    public function profileUnderReviewed()
    {
        // die();

        if (Auth::guard('healthcare_facilities')->user()) {
            if (Auth::guard('healthcare_facilities')->user()->user_stage == 2) {

                return redirect()->route('nurse.dashboard');
            } else {
                $title = "";
                $message = "";
                return view('auth.profile-under-reviewed', compact('title', 'message'));
            }
        } else {

            return redirect()->route('medical-facilities.login');
        }
    }
    
}