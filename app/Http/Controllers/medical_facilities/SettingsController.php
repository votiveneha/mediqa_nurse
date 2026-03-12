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
use App\Models\NurseApplication;

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
use Helpers;

class SettingsController extends Controller
{
    public function index()
    {
        $user_id = Auth::guard('healthcare_facilities')->user()->id;
        // $data['healtcare_data'] = DB::table("users")->where("id",$user_id)->first();
        // $data['recruiter_data'] = DB::table("users")->where("healthcare_id",$user_id)->get();
        $data['recruiter_data'] = DB::table('users')
    ->where(function($query) use ($user_id){
        $query->where('id', $user_id)
              ->orWhere('healthcare_id', $user_id);
    })
    ->get();
        return view('healthcare.settings.access_control')->with($data);
    }

    public function inviteUser(Request $request)
    {
        $email = $request->email;

        
        $token = Str::random(40);

        $password = Str::random(10);

        $user_data = User::where("email",$email)->first(); 

        $user_id = Auth::guard('healthcare_facilities')->user()->id;

        if(empty($user_data)){
            $user = new User;
            $user->healthcare_id = $user_id;
            $user->email = $email;
            $user->role = "5";
            $user->emailToken = $token; 
            $user->password = Hash::make($password); 
            $run = $user->save();
        }else{
            $run = 0;
        }

        if ($run) {
            $htmlBody = view('email.invite-user', [
                'role' => 'Recruiter',
                'password'=>$password,
                'invite_link' => url('/healthcare-facilities/login?token='.$token)
            ])->render();

            try {
                \App\Helpers\ZeptoMailHelper::sendMail(
                    $email,
                    "User Invitation",
                    $htmlBody
                );

                \Log::info("Invitation email sent", ['email' => $email]);
            } catch (\Throwable $ex) {
                \Log::error("Failed to send verification email", [
                    'user_id' => $email,
                    'error'   => $ex->getMessage()
                ]);
            }

            $json['status'] = 1;
            
            
        } else {
            $json['status'] = 0;
            $json['message'] = 'Email is already exists.!';
        }

        echo json_encode($json);
    }

    public function accept_invitation(Request $request)
    {
        
        $user_id = Auth::guard('healthcare_facilities')->user()->id;
        $user = User::find($user_id);
        $user->email_verify = 1;
        $user->emailVerified = '1';
        $user->user_stage = '1'; 
         
        $run = $user->save();

        if ($run) {
            $json['status'] = 1;
        }else {
            $json['status'] = 0;
        }

        echo json_encode($json);
    }

    public function deactivate_user(Request $request)
    {
        $user_id = $request->user_id;
        $status = $request->status;
        $user = User::find($user_id);
        $user->status = $status;
        
         
        $run = $user->save();

        if ($run) {
            $json['status'] = 1;
            $json['user_status'] = $status;
        }else {
            $json['status'] = 0;
        }

        echo json_encode($json);
    }

    public function delete_user(Request $request)
    {
        $user_id = $request->user_id;
        
        $user = User::find($user_id);
        
        
         
        $run = $user->delete();

        if ($run) {
            $json['status'] = 1;
            
        }else {
            $json['status'] = 0;
        }

        echo json_encode($json);
    }

}