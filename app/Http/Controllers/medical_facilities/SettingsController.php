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
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Barryvdh\DomPDF\Facade\Pdf;


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

    public function billing(Request $request)
    {
        $user = Auth::guard('healthcare_facilities')->user();
        $data['plan_data'] = DB::table("plan_management")->where("status","true")->get();

        $data['invoices'] = DB::table('invoices')
        ->where('user_id', $user->id)
        ->orderBy('id', 'desc')
        ->get();
        
        return view('healthcare.settings.billing')->with($data);
    }

    public function payment_page(Request $request)
    {
        
        $product_id = $request->product_id;
        $data['plan_data'] = DB::table("plan_management")->where("product_id",$product_id)->first();
        return view('healthcare.settings.payment')->with($data);
    }

    public function process(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        //echo $request->product_id;die;
        $payment_data = DB::table("plan_management")->where("product_id",$request->product_id)->first();
        
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($payment_data->monthly_price*100), // ₹500 (in paise)
                'currency' => 'USD',
                'payment_method' => $request->payment_method_id,
                
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // ✅ FIX
                ],
            ]);

            if ($paymentIntent->status == 'succeeded') {

                $payment_id = DB::table("payments")->insertGetId([
                    'payment_intent_id' => $paymentIntent->id,
                    'product_id' => $request->product_id,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'status' => $paymentIntent->status,
                    'payment_method' => $paymentIntent->payment_method,
                ]);

                $user = Auth::guard('healthcare_facilities')->user();
                // ✅ Create Invoice
                DB::table("invoices")->insert([
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'user_id' => $user->id,
                    'payment_id' => $payment_id,
                    'product_id' => $request->product_id,
                    'plan_name' => $payment_data->plan_name, // ✅ ADD THIS
                    'amount' => $paymentIntent->amount,
                    'tax' => 0,
                    'total_amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'billing_name' => $user->name,
                    'billing_email' => $user->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // try {
            //     \App\Helpers\ZeptoMailHelper::sendMail(
            //         $r->email,
            //         "Payment Confirmation - Mediqa",
            //         $htmlBody
            //     );

            //     \Log::info("Payment Confirmation email sent", ['user_id' => $r->id]);
            // } catch (\Throwable $ex) {
            //     \Log::error("Failed to send Payment Confirmation email", [
            //         'user_id' => $r->id,
            //         'error'   => $ex->getMessage()
            //     ]);
            // }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'data' => $paymentIntent
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function invoices()
    {
        $user = Auth::guard('healthcare_facilities')->user();

        $invoices = DB::table('invoices')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('healthcare.settings.index', compact('invoices'));
    }

    public function downloadInvoice($id)
    {
        $invoice = DB::table('invoices')->where('id', $id)->first();

        if (!$invoice) {
            abort(404);
        }

        $pdf = Pdf::loadView('healthcare.settings.invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

}