<?php

namespace App\Http\Controllers\nurse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\MatchHelper;
use App\Models\User;
use App\Models\JobsModel;
use App\Models\NurseNeededDocument;
use App\Models\NurseApplication;
use App\Models\InterviewsNurse;
use App\Models\SpecialityModel;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class MyCareerController extends Controller
{
    /**
     * Calculate match score for a given user and job
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job   $job
     * @return \Illuminate\Http\JsonResponse
     */

    public function nurseMyJobs()
    {
        return view('nurse.my_career.nurseMyJobs');
    }

    public function needed_document_delete($id = null){

        $nurseId = Auth::guard("nurse_middle")->user()->id;
        $document = NurseNeededDocument::where('id', $id)
            ->where('nurse_id', $nurseId)
            ->firstOrFail();

        $document->delete();

        return response()->json(['success' => true]);
    }
    public function action_needed_document(Request $request)
    {

        $file = $request->file('document_file');
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        // Store file inside public/uploads/needed_document
        $file->move(public_path('uploads/needed_document'), $filename);
        // Save in database (secure nurse_id)
        NurseNeededDocument::create([
            'nurse_id' => Auth::guard("nurse_middle")->user()->id, // DO NOT trust request nurse_id
            'name' => $request->document_name,
            'document_path' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully'
        ]);
  
    }
    
    public function action_needed_document_old(Request $request){
        // print_r($request->all());die;
        // $request->validate([
        //     'document_name' => 'required|string|max:255',
        //     'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        // ]);

        if ($request->hasFile('document_file')) {

            $file = $request->file('document_file');
            if ($file->isValid()) {
                $name = time() . '_' . rand(10000, 99999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/needed_document'), $name);
                // $newFiles[] = $name;
            }
      
            // Save in database
            NurseNeededDocument::create([
                'nurse_id' => $request->nurse_id,
                'name' => $request->document_name,
                'file_path' => $name,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully'
        ]);
    }
    public function interviews_nurse()
    {
        $nurseId = Auth::guard("nurse_middle")->user()->id;

        // echo $nurseId;die;
        // ACTIVE APPLICATIONS
        $upcoming_list = InterviewsNurse::with('job', 'health_care')
            ->where('nurse_id', $nurseId)
            ->whereNot('status', 5)
            ->where('scheduled_at', '>=', Carbon::now())
            ->orderBy('scheduled_at', 'asc')
            ->get();
        $upcoming_count = InterviewsNurse::where('nurse_id', $nurseId)
            ->whereNot('status', 5)
            ->where('scheduled_at', '>=', Carbon::now())
            ->count();
        //   echo "<pre>";  print_r($upcoming_list);die;
        // ARCHIVED APPLICATIONS
        $past_list = InterviewsNurse::with('job', 'health_care')
                    ->where('nurse_id', $nurseId)
                    ->whereNot('status', 5)
                    ->where('scheduled_at', '<', Carbon::now())
                    ->orderBy('scheduled_at', 'desc')
                    ->get();

        $interviews_list = InterviewsNurse::with('job', 'health_care')->where('nurse_id', $nurseId)->get();

        $action_count = InterviewsNurse::where('nurse_id', $nurseId)
            ->whereIn('status', [1, 2])
            ->where('scheduled_at', '>=', Carbon::now())
            ->count();

      

        $action_needed = InterviewsNurse::with('job', 'health_care')
            ->where('nurse_id', $nurseId)
            ->whereIn('status', [1, 2]) // scheduled + reschedule requested
            ->where('scheduled_at', '>=', Carbon::now()) // still upcoming
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $document_list = NurseNeededDocument::where('nurse_id', $nurseId)->get();


        return view(
            'nurse.my_career.interviews_nurse',
            compact('upcoming_list', 'past_list', 'action_needed', 'action_count','upcoming_count', 'document_list')
        );
    }

    public function action_interview(Request $request){
        $modal_no = $request->modal_no;

        $interviews = InterviewsNurse::with('job', 'health_care')->findOrFail($request->interview_id);

        // echo "<pre>"; print_r($interviews);die;
        return view('nurse.my_career.partial_interview_modal', compact('interviews','modal_no'));
    }

    public function interviews_events()
    {
        $nurseId = Auth::guard("nurse_middle")->user()->id;
        $statusMap = [
            1 => 'scheduled',
            2 => 'reschedule_requested',
            3 => 'confirmed',
            4 => 'completed',
            5 => 'no_show',
            6 => 'cancelled',
        ];

        // IMPORTANT: add ->get()
        $interviews = InterviewsNurse::with('job','health_care')->where('nurse_id', $nurseId)->whereNot('status', 5)->get();

        $events = $interviews->map(function ($item) use ($statusMap) {

            return [
                'id' => $item->id,
                'title' => $item->job->job_title,
                'start' => Carbon::parse($item->scheduled_at)->toIso8601String(),
                'backgroundColor' => $this->getColor($item->status),
                'borderColor' => $this->getColor($item->status),
                'extendedProps' => [
                    'status' => $statusMap[$item->status] ?? 'scheduled',
                    'meeting_link' => $item->meeting_link,
                    'location' => $item->location_address,
                    'notes' => $item->notes,
                    'duration' => $item->duration_minutes,
                ]
            ];
        });

        return response()->json($events);
    }

    private function getColor($status)
    {
        return match ($status) {
            1 => '#DB6E00', // scheduled
            2 => '#FFC107', // reschedule
            3 => '#00A81C', // confirmed
            4 => '#009BFA', // completed
            5 => '#6c757d', // no show
            6 => '#DC3545', // cancelled
            default => '#6c757d'
        };
    }
    public function action_application(Request $request)
    {
        $modal_no = $request->modal_no;

        $application = NurseApplication::with('job','health_care','interview')->findOrFail($request->application_id);

        return view('nurse.my_career.partial_application_modal', compact('application', 'modal_no'));
    }

    public function archivedTimeline(Request $request)
    {
        // print_r($request->all());die;
        $application = NurseApplication::with('job')
            ->findOrFail($request->application_id);

        $timeline = [];

        /* 1️⃣ Application Submitted */
        $timeline[] = [
            'title' => 'Application Submitted',
            'desc'  => 'Your profile has been submitted',
            'date'  => $application->created_at->format('d M Y'),
        ];

        /* 2️⃣ Under Review */
        if ($application->status >= 2) {
            $timeline[] = [
                'title' => 'Under Review',
                'desc'  => 'HR team reviewed your application',
                'date'  => $application->updated_at->format('d M Y'),
            ];
        }

        /* 3️⃣ Interview */
        if ($application->status >= 4) {
            $timeline[] = [
                'title' => 'Interview Stage',
                'desc'  => 'Interview process completed',
                'date'  => $application->updated_at->format('d M Y'),
            ];
        }

        /* 4️⃣ Final Status (Archived reason) */
        $finalStatus = match ($application->status) {

            8 => [ // Hired
                'label' => 'Hired',
                'desc'  => 'Congratulations! You have been hired.',
                'class' => 'success'
            ],

            9 => [ // Rejected (if you have)
                'label' => 'Rejected',
                'desc'  => 'Unfortunately, your application was rejected.',
                'class' => 'danger'
            ],

            11 => [ // Withdrawn (if you use 8)
                'label' => 'Withdrawn',
                'desc'  => 'You have withdrawn this application.',
                'class' => 'dark'
            ],

     

            default => [
                'label' => 'Closed',
                'desc'  => 'This application is now closed.',
                'class' => 'secondary'
            ]
        };

        $timeline[] = [
            'title' => $finalStatus['label'],
            'desc'  => $finalStatus['desc'],
            'date'  => $application->updated_at->format('d M Y'),
        ];

        return response()->json([
            'job_title' => $application->job_title ?? $application->job->title ?? '',

            'status' => $finalStatus,

            'timeline' => $timeline,

            'action' => null // archived has no action
        ]);
    }

    public function applicationTimeline(Request $request)
    {

        $application = NurseApplication::with('job')->findOrFail($request->application_id);

        $timeline = [];

        $timeline[] = [
            'title' => 'Application Submitted',
            'desc'  => 'Your profile has been submitted',
            'date'  => $application->created_at->format('d M Y')
        ];

        if ($application->status >= 2) {
            $timeline[] = [
                'title' => 'Under Review',
                'desc'  => 'HR team is reviewing your application',
                'date'  => now()->format('d M Y')
            ];
        }

        if ($application->status >= 4) {
            $timeline[] = [
                'title' => 'Interview Scheduled',
                'desc'  => 'Interview details shared',
                'date'  => now()->format('d M Y')
            ];
        }

        /* ---------- Action mapping ---------- */
        $action = match ($application->status) {
            1, 2, 3 => [
                'type' => 'withdraw',
                'label' => 'Withdraw Application',
                'class' => 'btn-dark'
            ],
            4 => [
                'type' => 'interview',
                'label' => 'View Interview Details',
                'class' => 'btn-outline-primary'
            ],
            6 => [
                'type' => 'offer_view',
                'label' => 'View Offer',
                'class' => 'btn-outline-info'
            ],
            7 => [
                'type' => 'offer_accept',
                'label' => 'Accept Offer',
                'class' => 'btn-success'
            ],
            default => null
        };

        $statusConfig = match ($application->status) {
            1 => [
                'label' => 'Submitted',
                'desc'  => 'Your application has been submitted successfully.',
                'class' => 'primary'
            ],
            2 => [
                'label' => 'Under Review',
                'desc'  => 'Your application is currently being reviewed.',
                'class' => 'warning'
            ],
            3 => [
                'label' => 'Shortlisted',
                'desc'  => 'You have been shortlisted for the next step.',
                'class' => 'info'
            ],
            4 => [
                'label' => 'Interview Scheduled',
                'desc'  => 'Interview details have been shared.',
                'class' => 'primary'
            ],
            6 => [
                'label' => 'Conditional Offer',
                'desc'  => 'A conditional offer has been issued.',
                'class' => 'info'
            ],
            7 => [
                'label' => 'Offer',
                'desc'  => 'Congratulations! You have received an offer.',
                'class' => 'success'
            ],
            default => [
                'label' => 'Closed',
                'desc'  => 'This application is no longer active.',
                'class' => 'secondary'
            ]
        };

        return response()->json([
            'job_title' => $application->job_title,
            // 'facility'  => $application->job->facility_name,
            'timeline'  => $timeline,
            'action'    => $action,
            'status'    => $statusConfig
        ]);
  
    }

    public function application()
    {
        $nurseId = Auth::guard("nurse_middle")->user()->id;

        // echo $nurseId;die;
        // ACTIVE APPLICATIONS
        $active_list = NurseApplication::with('job', 'health_care')->where('nurse_id', $nurseId)
            ->whereNotIn('status', [8, 9, 10, 11]) // hired, rejected, declined, withdrawn
            ->where('is_archived_by_nurse', 0)
            ->latest('applied_at')
            ->get();

        // ARCHIVED APPLICATIONS
        $archived_list = NurseApplication::with('job','health_care')->where('nurse_id', $nurseId)
            ->where(function ($q) {
                $q->whereIn('status', [8, 9, 10, 11])
                    ->orWhere('is_archived_by_nurse', 1);
            })
            ->latest('applied_at')
            ->get();


            // echo "<pre>";print_r($active_list);die;
        return view(
            'nurse.my_career.nurse_application',
            compact('active_list', 'archived_list')
        );
    }
    public function match_percentage()
    {
        
        $user = Auth::guard("nurse_middle")->user();
        $jobs = JobsModel::get();
        

        $data['education_certification_percent'] = $this->matchEducationPercent($jobs,$user);
        $data['experience_certification_percent'] = $this->matchExperiencePercent($jobs,$user);
        

        //print_r($training_id_arr);

        


        //print_r($nurse_percent);die;
        return view('nurse.my_career.match_percentage')->with($data);
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
        return view("nurse.my_career.matchedjobsnew")->with($data);
    }
    
    public function matchSingleWorkEnvironmentPercent($jobs,$user)
    {

    }


}