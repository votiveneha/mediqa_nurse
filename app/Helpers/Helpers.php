<?php
use Carbon\Carbon;

use App\Models\SpecialityModel;
use App\Models\PractitionerTypeModel;
use App\Models\CountryModel;
use App\Models\User;
use App\Models\StateModel;
use App\Models\LevelYearModel;
use App\Models\EvidenceModel;
use App\Models\EligibilityToWorkModel;
use App\Models\ProfessionModel;
use App\Models\WorkingChildrenCheckModel;
use App\Models\PoliceCheckModel;
use App\Models\DegreeModel;
use App\Models\EmergencyContactModel;
use App\Models\ProfessionalCer;
use App\Models\TrainingModel;
use App\Models\SkillModel;
use App\Models\VaccinationModel;

function specialty()
{
        $specialty_data =  SpecialityModel::where('parent', '0')->orderBy('nurse_level_order', 'asc')->get();
        return $specialty_data;
}
function specialty_name_by_id($specialty)
{
        $specialty_data =  SpecialityModel::where('id', $specialty)->orderBy('id', 'desc')->first();
        return $specialty_data->name;
}
function specialty_name_by_id_NEW($specialty)
{
        $specialty_data =  SpecialityModel::where('id', $specialty)->orderBy('id', 'desc')->first();
        return $specialty_data->name;
}
function sub_specialty($specialty_id)
{
        $specialty_data =  SpecialityModel::where('parent', $specialty_id)->orderBy('id', 'desc')->get();
        return $specialty_data;
}

function JobSpecialties()
{
        $JobSpecialties =  PractitionerTypeModel::where('status', '1')->where('parent','0')->orderBy('id', 'asc')->get();
        return $JobSpecialties;
}
function SubJobSpecialties()
{
        $SubJobSpecialties =  PractitionerTypeModel::where('status', '1')->where('parent','!=','0')->orderBy('id', 'desc')->get();
        return $SubJobSpecialties;
}
function practitioner_type()
{
        $practitioner_type_data =  PractitionerTypeModel::where('status', '1')->orderBy('id', 'desc')->get();
        return $practitioner_type_data;
}
function nurse_midwife_degree()
{
        $nurse_midwife_degree =  DegreeModel::where('status', '1')->orderBy('id', 'desc')->get();
        return $nurse_midwife_degree;
}
function nurse_midwife_degree_by_id($id)
{
        $nurse_midwife_degree =  DegreeModel::where('status', '1')->where('id', $id)->first();
        return $nurse_midwife_degree->name;
}
function practitioner_type_by_id($practitioner)
{
        $practitioner_type_data =  PractitionerTypeModel::where('id', $practitioner)->orderBy('id', 'desc')->first();
        return $practitioner_type_data->name;
}
function country_phone_code()
{
    $country_phone_code = CountryModel::where('status', '1')->select('phonecode','name')->groupBy('phonecode')->orderBy("phonecode", "asc")->get();
    return $country_phone_code;
}
function country_id($country_phone_code)
{
    $country = CountryModel::where('status', '1')->where('phonecode', $country_phone_code)->first();
    return $country->iso2;
}
function country_name_from_db()
{
        $country_data =  CountryModel::where('status', '1')->get();
        return $country_data;
}
function state_name_array($country_id)
{
        // $lastRecord = StateModel::where('country_id', $country_id)->get();
        $lastRecord = StateModel::where('country_code', $country_id)->get();
        return $lastRecord;
}
function state_name($state_id)
{
        $lastRecord = StateModel::where('id', $state_id)->first();
        return $lastRecord->name;
}
function state_list()
{
        $lastRecord = StateModel::all();
        
        return $lastRecord;
}
function country_name($country_id)
{

        $lastRecord = CountryModel::where('iso2', $country_id)->first();
        return $lastRecord->name;
}
function country_name_new($country_id)
{

        $lastRecord = CountryModel::where('id', $country_id)->first();
        return $lastRecord->name;
}
function year_level()
{
        $lastRecord = LevelYearModel::where('status','1')->get();
        return $lastRecord;
}
function evidence_list()
{
        $lastRecord = EvidenceModel::where('status','1')->get();
        return $lastRecord;
}
function profession_data()
{
        $lastRecord = ProfessionModel::where('user_id', Auth::guard('nurse_middle')->user()->id)->first();
        if($lastRecord){
            $lastRecord=$lastRecord;
        }else{
            $lastRecord='null';
        }
        return $lastRecord;
}
function emergency_contact_data()
{
        $lastRecord = EmergencyContactModel::where('user_id', Auth::guard('nurse_middle')->user()->id)->first();
        if($lastRecord){
            $lastRecord=$lastRecord;
        }else{
            $lastRecord='null';
        }
        return $lastRecord;
}
function clearances_data()
{
        $lastRecord = EligibilityToWorkModel::where('user_id', Auth::guard('nurse_middle')->user()->id)->first();
        if($lastRecord){
            $lastRecord=$lastRecord;
        }else{
            $lastRecord='null';
        }
        return $lastRecord;
}
function working_data()
{
        $lastRecord = WorkingChildrenCheckModel::where('user_id', Auth::guard('nurse_middle')->user()->id)->first();
        if($lastRecord){
            $lastRecord=$lastRecord;
        }else{
            $lastRecord='null';
        }
        return $lastRecord;
}
function police_check_data()
{
        $lastRecord = PoliceCheckModel::where('user_id', Auth::guard('nurse_middle')->user()->id)->first();
        if($lastRecord){
            $lastRecord=$lastRecord;
        }else{
            $lastRecord='null';
        }
        return $lastRecord;
}

function getUserDataById($id)
{
        $lastRecord = User::where('id',$id)->first();
        return $lastRecord;
}

function getLevelYearNameById($id)
{
        $lastRecord = LevelYearModel::where('id',$id)->first();
        return $lastRecord->name;
}
function getEvidenceTypeNameById($id)
{
        $lastRecord = EvidenceModel::where('id',$id)->first();
        return $lastRecord->name;
}

function practitioner_type_header()
{
        $practitioner_type_data =  PractitionerTypeModel::where('parent', '0')->orderBy('id', 'desc')->get();
        return $practitioner_type_data;
}
function nurse_Type_header()
{
        $practitioner_type_data =  SpecialityModel::where('parent', '0')->orderBy('id', 'desc')->get();
        return $practitioner_type_data;
}
function email_verified()
{
       
        if(Auth::guard('nurse_middle')->user()->user_stage=='0'){
                return false;
        }else{
                return true;
        }
}
function account_verified()
{
        if(Auth::guard('nurse_middle')->user()->user_stage=='1'){
                return false;
        }else{
                return true;
        }
}

function completeProfile()
{
        if(Auth::guard('nurse_middle')->user()->user_stage=='4'){
                return false;
        }else{
                return true;
        }
}

function approvedProfile()
{
        if(Auth::guard('nurse_middle')->user()->user_stage=='2'){
                return false;
        }else{
                return true;
        }
}

function update_user_stage($user_id,$tab_name)
{
        $user_data = User::where("id",$user_id)->first();   
        $reg_date = $user_data->created_at;

        $date = new DateTime($reg_date);

        

        // Format to get only the date
        $onlyDate = $date->format('Y-m-d');
        //print_r($user_data);
        if(!empty($user_data) && $user_data->user_stage == 1){
                DB::table("users")->where("id",$user_id)->update(["user_stage"=>"5"]); 

                // $to = "votivetester.vijendra@gmail.com";
                $to = "admin@mediqa.com";

                

                // $mailData = [

                //         'subject' => 'In-progress Nurse Profile',

                //         'email' => $to,


                //         'body' => '<p>Dear Mediqa Team,</p><p>A new Nurse/Midwife has started filling their profile on Mediqa.</p><br><p>User Details:  </p><p>- Name: '.$user_data->name." ".$user_data->lastname.'</p><p>- Email: ['.$user_data->email.']</p><p>- Registration Date: '.$onlyDate.'</p><p>- Profile Progress: '.$tab_name.'</p><br><p>This is an automated notification to inform you of new user activity.</p>',


                // ];

                
                // Mail::to($to)->send(new \App\Mail\DemoMail($mailData));

                $htmlBody = '
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <title>New User Activity Notification</title>
                        </head>
                        <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:30px 0;">
                                <tr>
                                <td align="center">
                                        <table width="100%" max-width="600" cellpadding="0" cellspacing="0"
                                        style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                                        <!-- Header -->
                                        <tr>
                                                <td style="background:#0d6efd; padding:20px; text-align:center;">
                                                <h1 style="margin:0; color:#ffffff; font-size:22px;">
                                                        Mediqa Notification
                                                </h1>
                                                </td>
                                        </tr>

                                        <!-- Body -->
                                        <tr>
                                                <td style="padding:30px; color:#333333;">
                                                <p style="margin:0 0 15px;">
                                                        Dear <strong>Mediqa Team</strong>,
                                                </p>

                                                <p style="margin:0 0 20px;">
                                                        A new <strong>Nurse / Midwife</strong> has started filling out their profile on Mediqa.
                                                </p>

                                                <p style="margin:0 0 10px;"><strong>User Details:</strong></p>

                                                <table width="100%" cellpadding="8" cellspacing="0" 
                                                        style="border-collapse:collapse; font-size:14px;">
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9; width:40%;">
                                                                Name
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($user_data->name . ' ' . $user_data->lastname) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Email
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($user_data->email) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Registration Date
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($onlyDate) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Profile Progress
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($tab_name) . '
                                                        </td>
                                                        </tr>
                                                </table>

                                                <p style="margin:25px 0 0; font-size:14px; color:#777;">
                                                        This is an automated notification to inform you of new user activity.
                                                </p>
                                                </td>
                                        </tr>

                                        <!-- Footer -->
                                        <tr>
                                                <td style="background:#f0f0f0; padding:15px; text-align:center; font-size:12px; color:#777;">
                                                © ' . '2024' . ' Mediqa. All rights reserved.
                                                </td>
                                        </tr>

                                        </table>
                                </td>
                                </tr>
                        </table>
                        </body>
                        </html>
                        ';

                \App\Helpers\ZeptoMailHelper::sendMail(
                        to: $to,
                        subject: "In-progress Nurse Profile",
                        htmlBody: $htmlBody
                );
        }   

        $tab_data = DB::table("updated_tab_name")->where("user_id",$user_id)->where("tab_name",$tab_name)->first();
        
        $tab_date = Carbon::now('Asia/Kolkata');
        if(empty($tab_data)){
                DB::table("updated_tab_name")->insert(["tab_name"=>$tab_name,"user_id"=>$user_id,"created_at"=>$tab_date]);      
        }

        $tab_count = DB::table("updated_tab_name")->where("user_id",$user_id)->get();
        
        if(count($tab_count) == 15 && empty($tab_data)){
                
                User::where("id",$user_id)->update(["user_stage"=>"4"]);

                $to = $user_data->email;

                
                // $mailData = [

                //         'subject' => 'Your Mediqa Profile is Complete',

                //         'email' => $to,


                //         'body' => '<p>Dear '.$user_data->name." ".$user_data->lastname.',</p><p>Congratulations! You have successfully completed your profile on Mediqa.</p><p>Your profile is now ready for review, and once approved, you will be able to:</p><p>- Apply for job opportunities.<br>- Connect with healthcare facilities and agencies.<br>- Receive interview requests and offers that match your skills and preferences.</p><p><strong>Next Steps:</strong></p><p>- Our team will review your profile for approval.<br>- You will receive an email once your profile has been approved.</p><p>If you have any questions, feel free to contact us at <a href="mailto:info@mediqa.com.au">info@mediqa.com.au</a></p><p>Thank you for being part of Mediqa. We look forward to helping you find the best nursing opportunities!</p>',


                // ];

                
                // Mail::to($to)->send(new \App\Mail\DemoMail($mailData));

                $htmlBodyUser = '
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <title>Profile Completed Successfully</title>
                        </head>
                        <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:30px 0;">
                                <tr>
                                <td align="center">
                                        <table width="100%" max-width="600" cellpadding="0" cellspacing="0"
                                        style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                                        <!-- Header -->
                                        <tr>
                                                <td style="background:#0d6efd; padding:20px; text-align:center;">
                                                <h1 style="margin:0; color:#ffffff; font-size:22px;">
                                                        Mediqa
                                                </h1>
                                                </td>
                                        </tr>

                                        <!-- Body -->
                                        <tr>
                                                <td style="padding:30px; color:#333333;">
                                                <p style="margin:0 0 15px;">
                                                        Dear <strong>' . e($user_data->name . ' ' . $user_data->lastname) . '</strong>,
                                                </p>

                                                <p style="margin:0 0 15px;">
                                                        <strong>Congratulations!</strong> You have successfully completed your profile on <strong>Mediqa</strong>.
                                                </p>

                                                <p style="margin:0 0 15px;">
                                                        Your profile is now ready for review. Once approved, you will be able to:
                                                </p>

                                                <ul style="margin:0 0 20px 20px; padding:0; font-size:14px;">
                                                        <li style="margin-bottom:8px;">Apply for job opportunities</li>
                                                        <li style="margin-bottom:8px;">Connect with healthcare facilities and agencies</li>
                                                        <li style="margin-bottom:8px;">Receive interview requests and offers that match your skills</li>
                                                </ul>

                                                <p style="margin:0 0 10px;"><strong>Next Steps:</strong></p>
                                                <p style="margin:0 0 20px; font-size:14px;">
                                                        • Our team will review your profile<br>
                                                        • You will receive an email once your profile has been approved
                                                </p>

                                                <p style="margin:0 0 15px; font-size:14px;">
                                                        If you have any questions, feel free to contact us at
                                                        <a href="mailto:info@mediqa.com.au" style="color:#0d6efd; text-decoration:none;">
                                                        info@mediqa.com.au
                                                        </a>
                                                </p>

                                                <p style="margin:20px 0 0;">
                                                        Thank you for being part of <strong>Mediqa</strong>!
                                                </p>
                                                </td>
                                        </tr>

                                        <!-- Footer -->
                                        <tr>
                                                <td style="background:#f0f0f0; padding:15px; text-align:center; font-size:12px; color:#777;">
                                                © ' . '2024' . ' Mediqa. All rights reserved.
                                                </td>
                                        </tr>

                                        </table>
                                </td>
                                </tr>
                        </table>
                        </body>
                        </html>
                        ';

                \App\Helpers\ZeptoMailHelper::sendMail(
                        to: $to,
                        subject: "Your Mediqa Profile is Complete",
                        htmlBody: $htmlBodyUser
                );

                // $to1 = "votivetester.vijendra@gmail.com";
                $to1 = "admin@mediqa.com";

                $last_tab_date = DB::table("updated_tab_name")->where("user_id",$user_id)->orderBy("tab_id","DESC")->get();

                $last_date = $last_tab_date[0]->created_at;

                $date1 = new DateTime($last_date);

                // Format to get only the date
                $onlyDate1 = $date1->format('Y-m-d');
                // $mailData = [

                //         'subject' => 'Completed Profile – Review Required for Approval',

                //         'email' => $to1,


                //         'body' => '<p>Dear Mediqa Team,</p><p>A user has successfully completed and saved all 11 tabs of their profile on Mediqa and is now awaiting review for approval.</p><br><p>User Details:</p><p>- Name: '.$user_data->name." ".$user_data->lastname.'<br>- Email: '.$user_data->email.'<br>- Registration Date: '.$onlyDate.'<br>- Completion Date: '.$onlyDate1.'</p><br><p>Please review the user\'s profile and approve it as necessary.</p>',


                // ];

                
                // Mail::to($to1)->send(new \App\Mail\DemoMail($mailData));

                $htmlBodyAdmin = '
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <title>Profile Completed – Approval Required</title>
                        </head>
                        <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:30px 0;">
                                <tr>
                                <td align="center">
                                        <table width="100%" max-width="600" cellpadding="0" cellspacing="0"
                                        style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">

                                        <!-- Header -->
                                        <tr>
                                                <td style="background:#0d6efd; padding:20px; text-align:center;">
                                                <h1 style="margin:0; color:#ffffff; font-size:22px;">
                                                        Mediqa – Admin Notification
                                                </h1>
                                                </td>
                                        </tr>

                                        <!-- Body -->
                                        <tr>
                                                <td style="padding:30px; color:#333333;">
                                                <p style="margin:0 0 15px;">
                                                        Dear <strong>Mediqa Team</strong>,
                                                </p>

                                                <p style="margin:0 0 20px;">
                                                        A user has successfully completed all profile sections and is now awaiting approval.
                                                </p>

                                                <p style="margin:0 0 10px;"><strong>User Details:</strong></p>

                                                <table width="100%" cellpadding="8" cellspacing="0"
                                                        style="border-collapse:collapse; font-size:14px;">
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9; width:40%;">
                                                                Name
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($user_data->name . ' ' . $user_data->lastname) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Email
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($user_data->email) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Registration Date
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($onlyDate) . '
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td style="border:1px solid #e0e0e0; background:#f9f9f9;">
                                                                Completion Date
                                                        </td>
                                                        <td style="border:1px solid #e0e0e0;">
                                                                ' . e($onlyDate1) . '
                                                        </td>
                                                        </tr>
                                                </table>

                                                <p style="margin:25px 0 0; font-size:14px;">
                                                        Please review the user’s profile and proceed with approval.
                                                </p>
                                                </td>
                                        </tr>

                                        <!-- Footer -->
                                        <tr>
                                                <td style="background:#f0f0f0; padding:15px; text-align:center; font-size:12px; color:#777;">
                                                © ' . '2024' . ' Mediqa. All rights reserved.
                                                </td>
                                        </tr>

                                        </table>
                                </td>
                                </tr>
                        </table>
                        </body>
                        </html>
                        ';

                \App\Helpers\ZeptoMailHelper::sendMail(
                        to: $to1,
                        subject: "Completed Profile – Review Required for Approval",
                        htmlBody: $htmlBodyAdmin
                );
        }
       
}

function getUserNameById($id)
{
        $lastRecord = User::where('id',$id)->first();
        if($lastRecord){
                return $lastRecord->name . ' ' . $lastRecord->lastname;
        }
        
}
function professional_certificate_by_id($id)
{
        $certificate =  ProfessionalCer::where('id', $id)->first();
        return $certificate->name;
}
function training_name_by_id($specialty)
{
        $training_data =  TrainingModel::where('id', $specialty)->first();
        return $training_data->name;
}
function skill_name_by_id($id)
{
        $skill_data =  SkillModel::where('id', $id)->first();
        return $skill_data->name;
}
function vaccination_name_by_id($id)
{
        $vaccination_data =  VaccinationModel::where('id', $id)->first();
        return $vaccination_data->name;
}
function getParentSpecialityId(array $tree, int|string $childId): ?int
{
    $childId = (string) $childId;

    foreach ($tree as $key => $values) {

        // Only process hierarchy keys
        if (!str_starts_with($key, 'type_')) {
            continue;
        }

        // Value must be an array of children
        if (!is_array($values)) {
            continue;
        }

        // Skip speciality_status or associative arrays
        if (array_keys($values) !== range(0, count($values) - 1)) {
            continue;
        }

        if (in_array($childId, $values, true)) {
            return (int) str_replace('type_', '', $key);
        }
    }

    return null;
}