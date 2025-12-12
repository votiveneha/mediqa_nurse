@foreach($jobs as $job)
<div class="job-card">
    <?php

    $nurse_type = json_decode($job->nurse_type);
    $nurse_arr = array();  
    if(!empty($nurse_type)){
        foreach($nurse_type as $nt){
            $nurse_type = DB::table("practitioner_type")->where("id",$nt)->first();
            $nurse_arr[] = $nurse_type->name;
        }
    }

    $nurse_arr_string = implode(",",$nurse_arr);


    $emplyeement_positions = json_decode($job->emplyeement_positions);
    
    $emp_pos_arr = array();  
    if(!empty($emplyeement_positions)){
        foreach($emplyeement_positions as $emppos){
            $emp_position = DB::table("employee_positions")->where("position_id",$emppos)->first();
            $emp_pos_arr[] = $emp_position->position_name;
        }
    }

    $emp_pos_arr_string = implode(",",$emp_pos_arr);

    $emplyeement_type = json_decode($job->emplyeement_type);
    
    $emplyeement_type_arr = array();  
    if(!empty($emplyeement_type)){
        foreach($emplyeement_type as $emptype){
        
            $emp_type = DB::table("employeement_type_preferences")->where("emp_prefer_id",$emplyeement_type)->first();
            
            $emplyeement_type_arr[] = $emp_type->emp_type;
        }
    }

    $emplyeement_type_arr_string = implode(",",$emplyeement_type_arr);

    $shift_type = json_decode($job->shift_type);
    
    $shift_type_arr = array();  
    if(!empty($shift_type)){
        foreach($shift_type as $shifttype){
        
            $shiftty = DB::table("work_shift_preferences")->where("work_shift_id",$shifttype)->first();
            
            $shift_type_arr[] = $shiftty->shift_name;
        }
    }

    $shift_type_arr_string = implode(",",$shift_type_arr);

    $work_environment = json_decode($job->work_environment);
    
    $work_environment_arr = array();  
    if(!empty($work_environment)){
        foreach($work_environment as $work_env){
        
            $workenv = DB::table("work_enviornment_preferences")->where("prefer_id",$work_env)->first();
            
            $work_environment_arr[] = $workenv->env_name;
        }
    }

    $work_environment_arr_string = implode(",",$work_environment_arr);

    $benefits = json_decode($job->benefits);
    
    $benefits_arr = array();  
    if(!empty($benefits)){
        foreach($benefits as $benefit){
        
            $benefit_data = DB::table("benefits_preferences")->where("benefits_id",$benefit)->first();
            
            $benefits_arr[] = $benefit_data->benefits_name;
        }
    }

    $benefits_arr_string = implode(",",$benefits_arr);

    $specialityies = json_decode($job->typeofspeciality);
    
    $speciality_arr = array();  
    if(!empty($specialityies)){
        foreach($specialityies as $special){
        
            $speciality_data = DB::table("speciality")->where("id",$special)->first();
            
            $speciality_arr[] = $speciality_data->name;
        }
    }

    $speciality_arr_string = implode(",",$speciality_arr);
    
    
    ?>
    <!-- Top Row: Company Logo & Position -->
    <div class="job-card-header">
        <div class="job-company">
        <div class="job-logo">🏥</div>
        <div class="job-details">
            <strong>{{ $nurse_arr_string }}</strong>
            <div class="location">{{ $job->location_name }}</div>
        </div>
        </div>
        @if($job->urgent_hire == 1)
        <div class="urgent-tag">Urgent Hiring</div>
        @endif
    </div>

    <!-- Job Role / Hospital Name -->
    <div class="job-role">{{ $job->agency_name }}</div>

    <!-- Main Job Info -->
    <div class="job-meta">
        <span><strong>Position:</strong> {{ $emp_pos_arr_string }}</span>
        <span><strong>Salary:</strong> ${{ $job->salary }}/hr</span>
    </div>

    <!-- Expanded Job Details -->
    <div class="job-info-details">
        <div><strong>Sector:</strong> {{ $job->sector }}</div>
        <div><strong>Employment Type:</strong> {{ $emplyeement_type_arr_string }}</div>
        <div><strong>Shift Type:</strong> {{ $shift_type_arr_string }}</div>
        <div><strong>Work Environment:</strong> {{ $work_environment_arr_string }}</div>
        <div><strong>Benefits:</strong> {{ $benefits_arr_string }}</div>
        
        <div><strong>Specialty:</strong> {{ $speciality_arr_string }}</div>
        <div><strong>Experience Required:</strong>
            {{ $job->experience_level }}{{ $job->experience_level == 1 ? 'st' : ($job->experience_level == 2 ? 'nd' : ($job->experience_level == 3 ? 'rd' : 'th')) }} Year
        </div>
        <div class="last-date">
            Last Date:
            <?php
                echo $formattedDate = date("d M Y", strtotime($job->application_submission_date));
            ?>
            
        </div>
    </div>

    <!-- Footer: Match & Apply -->
    <div class="job-footer">
        <?php
            $user_id = Auth::guard("nurse_middle")->user()->id;
        ?>
        <div class="match-score">85% Match</div>
        <button class="apply-btn apply-btn-{{ $job->id }} @if(!empty($apply_job_data)) applied @endif" onclick="applyNow('{{ $user_id }}','{{ $job->id }}')">
        @if(!empty($apply_job_data))
        Applied
        @else
        Apply Now
        @endif
        </button>
        
    </div>
</div>
@endforeach