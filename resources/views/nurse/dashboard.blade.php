@php
  use Carbon\Carbon;
@endphp
@extends('nurse.layouts.layout')
@section('content')

    <style>
        /* =============================
                STATUS BANNER
          ============================= */
        .status-bg {
            background:#fff4da;
            border-radius: 10px;
            padding: 25px;
            border: 1px solid #ffe0b2;
        }

        .status-bg h5 {
            font-weight: 600;
        }

        .btn-primary-custom {
            background: #000;
            color: #fff;
            border: none;
            padding: 14px 22px;
            border-radius: 6px;
            border: 1px solid #000;
            transition: all ease-in-out .3s;
        }

        .btn-primary-custom:hover {
            background: transparent;
            color: #000;

        }

           /* HEADLINE */
                   
        .dashboard-title {
            font-weight: 700;
        }

        .sub-text {
            color: #6c757d;
        }
            /* JOB CARD */
                  
        .job-card {
            background: #fff;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid #000;
        }

        /* Header */
        .job-title {
            font-weight: 700;
            font-size: 18px;
        }

        .heart {
            font-size: 18px;
            color: #bbb;
            cursor: pointer;
        }

        .heart:hover {
            color: #e74c3c;
        }

        /* Location & type */
        .job-meta {
            font-size: 12px;
            color: #6c757d;
        }

        .job-meta i {
            margin-right: 4px;
            font-size: 11px;
        }

        /* Salary */
        .salary {
            color: #0a7c86;
        }

        .nurse-salary {
            font-size: 16px;
            font-weight: 700;
        }

        /* Badge */
        .badge-new {
            background: #d4f5ea;
            color: #198754;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Details list */
        .job-details {
            font-size: 12px;
            margin-top: 8px;
            color: #495057;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        /* Footer */
        .job-footer {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            enter;
        }

        .match {
            font-size: 13px;
            color: #6c757d;
        }

        .nurse-apply-btn {
            background: #2c7a7b;
            border: 1px solid #2c7a7b;
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 14px;
            display: inline;
            transition: all ease-in-out .3s
        }

        .nurse-apply-btn:hover {
            background: #fff;
            border: 1px solid #256b6c;
            color: #256b6c !important;
        }

        .section-title {
            font-weight: 600;
        }

        .view-all {
            font-size: 14px;
            font-weight: 500;
        }

        /* ========== */


        /* 20/2  */
        /* ======= filyter chips ======== */

        /* Horizontal scroll container */
        .chip-container {
            display: flex;
            overflow-x: auto;
            padding-bottom: 8px;
            gap: 10px;
        }

        /* Hide scrollbar (optional) */
        .chip-container::-webkit-scrollbar {
            display: none;
        }

        /* Chip Style */
        .filter-chip {
            border: 1px solid #e0e0e0;
            background: #fff;
            padding: 5px 10px;
            border-radius: 30px;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            transition: .2s;
            color: #444;
            font-size: 12px;
        }

        .filter-chip i {
            margin-right: 2px;
            font-size: 10px;
        }

        /* Hover */
        .filter-chip:hover {
            background: #f2f6ff;
        }

        /* ACTIVE CHIP */
        .filter-chip.active {
            background: #2c7a7b;
            color: #fff;
            border-color: #2c7a7b;
            box-shadow: 0 2px 6px rgba(43, 109, 246, 0.25);
        }

        /* Urgent visual hint */
        .filter-chip.urgent {
            border-color: #ffb3b3;
        }

        /* .filter-chip[data-mode="top"]{
        margin-right:20px;
    } */
    </style>

    <main class="main">
        <section class="section-box mt-30">
            <div class="container">
                <div class="row flex-row-reverse">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 float-right">
                        <div class="chip-container">
                            <button class="filter-chip active" data-mode="top">
                                <i class="fas fa-star"></i> Top Matches
                            </button>

                            <button class="filter-chip">
                                <i class="fas fa-bolt"></i> Instant Connect
                            </button>

                            <button class="filter-chip">
                                <i class="far fa-clock"></i> Last Minute
                            </button>

                            <button class="filter-chip">
                                <i class="fas fa-play"></i> Immediate Start
                            </button>

                            <button class="filter-chip">
                                <i class="fas fa-exclamation-circle"></i> Urgent Hire
                            </button>

                            <button class="filter-chip">
                                <i class="fas fa-certificate"></i> New
                            </button>
                        </div>

                        <!-- =============================
                              STATUS BANNER
                          ============================= -->
                        <div class="status-bg mb-4 text-center mt-4">
                            <h5>Complete your credentials to view job details and unlock job applications
                            </h5>
                            <p class="mb-2">We need your credentials to unlock matching jobs.</p>

                            <strong>Status: Not Started</strong><br><br>

                            <button class="btn btn-primary-custom">
                                Continue Registration
                            </button>
                        </div>

                          <!-- =============================
                                  HEADLINE
                              ============================= -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div>
                                <h3 class="dashboard-title">Your Job Matches</h3>
                                <div class="sub-text">Based on your preferences and availability</div>
                            </div>

                            <a href="{{route('nurse.find_jobs')}}" class="view-all">
                                Go to Find Jobs →
                            </a>
                        </div>
                            <!-- =============================
                                RECOMMENDED JOBS
                            ============================= -->
                        {{-- <div class="d-flex justify-content-between mb-3">
                            <h5 class="section-title">Recommended Jobs</h5>
                            <a href="#" class="view-all">View all →</a>
                        </div> --}}

                        <div class="row ">

                            @foreach ($jobs_list as $jobs)
                                
                     
                            <!-- JOB CARD -->
                            <div class="col-lg-4 col-md-6 d-flex">
                                <div class="job-card w-100 mb-4">
                                    <!-- Header -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="job-title mb-0">{{$jobs->job_title}}</div>
                                        <i class="far fa-heart heart"></i>
                                    </div>

                                    <!-- Location & Type -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">

                                        <div class="d-flex gap-4">
                                            <span class="job-meta mr-3">
                                                <i class="fas fa-map-marker-alt"></i> {{$jobs->state_name}},{{$jobs->country_name}}
                                            </span>

                                            <span class="job-meta">
                                                <i class="far fa-circle"></i> {{$jobs->employment_type}}
                                            </span>
                                        </div>

                                        <span class="badge badge-new">New</span>
                                    </div>

                                    <!-- Salary -->
                                    {{-- <div class="nurse-salary mt-2">
                                        <span class="salary"> $ </span> <span> 55/hr </span>
                                    </div> --}}

                                    <div class="nurse-salary mt-2">
                                    
                                        @php
                                    
                                        if($jobs->main_emp_type == 1){
                                        $min = $jobs->per_salary_min;
                                        $max = $jobs->per_salary_max;
                                        $per = $jobs->salary_permanent;
                                        }
                                        elseif($jobs->main_emp_type == 2){
                                        $min = $jobs->fixed_term_salary_min;
                                        $max = $jobs->fixed_term_salary_max;
                                        $per = $jobs->salary_range_fix_term;
                                        }
                                        elseif($jobs->main_emp_type == 3){
                                        $min = $jobs->temporary_salary_min;
                                        $max = $jobs->temporary_salary_max;
                                        $per = $jobs->salary_range_temporary;
                                        }
                                    
                                        @endphp
                                    
                                        @if(!empty($min))
                                        <span class="salary">$</span>
                                        <span>{{ $min }} - {{ $max }}/{{$per}}</span>
                                        @endif
                                    
                                    </div>
                                    @php
                                    $startDate = Carbon::parse($jobs->created_at);
                                        switch ($jobs->expiry_date) {
                                        case 1:
                                        $endDate = $startDate->copy()->addDays(7);
                                        break;
                                        case 2:
                                        $endDate = $startDate->copy()->addDays(14);
                                        break;
                                        case 3:
                                        $endDate = $startDate->copy()->addDays(30);
                                        break;
                                        case 4:
                                        $endDate = $startDate->copy()->addDays(60);
                                        break;
                                        case 5:
                                        $endDate = Carbon::parse($jobs->custom_expiry_date);
                                        break;
                                        default:
                                        $endDate = null;
                                        }
                                        $start = $startDate->format('d M Y');
                                        $end = $endDate ? $endDate->format('d M Y') : '';
                                        $date_range = $start . ' – ' . $end;
                                    @endphp
                                    <!-- Shift Dates -->
                                    <div class="job-meta mt-1">
                                        {{$date_range}}
                                    </div>

                                    <!-- Bullet Details -->
                                    <div class="job-details">
                                        <div>
                                            <p>Single patient COVID care</p>
                                            <p>PPE provided</p>
                                        </div>
                                        <div class="match">
                                            87% match
                                        </div>

                                    </div>

                                    <!-- Footer -->
                                    <div class="job-footer">

                                        {{-- <button class="btn nurse-apply-btn text-white">
                                            Apply Now
                                        </button> --}}
                                        @php
                                            $apply_job_data = DB::table("nurse_applications")->where("nurse_id",$user_id)->where("job_id",$jobs->id)->first();
                                        @endphp
                                        <button class="btn text-white nurse-apply-btn {{ $jobs->id }} 
                                            @if(!empty($apply_job_data)) applied @endif" @if(empty($apply_job_data))
                                            onclick="applyNow('{{ $user_id }}','{{ $jobs->id }}')" @else disabled @endif>
                                            @if(!empty($apply_job_data))
                                            Applied
                                            @else
                                            Apply Now
                                            @endif
                                        </button>
                                        <div>
                                            <a href="{{ route('nurse.job_details',['job_id'=>$jobs->id]) }}" class="d-flex gap-2">
                                            <span><i class="fa fa-bookmark-o" aria-hidden="true"></i></span>
                                            Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- ============== -->


                    </div>
                    {{-- <div class="col-lg-3 col-md-12 col-sm-12 col-12"></div> --}}

                </div>
            </div>
        </section>
    </main>

    {{-- City Selection Modal --}}
    @if (Auth::guard('nurse_middle')->user()->active_country == null)
        <div class="modal fade" id="registrationCountryModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <!-- Success Icon -->
                    <div class="modal-header border-0 justify-content-center">
                        <div class="rounded-circle bg-success-subtle p-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>
                    </div>
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold w-100"> Please choose your registration country </h5>
                    </div>
                    <!-- Title -->
                    <!-- Dropdown -->
                    <div class="modal-body">
                        <p class="mb-3">
                            This sets your search country and loads the right jurisdictions and checks. You can add more
                            countries later and switch anytime.
                        </p>
                        <select class="form-select" id="registration_country">
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->iso2 }}" @if ($country->iso2 === 'AU') selected @endif
                                    data-id="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="country_id" id="country_id">
                        <span class="text-danger d-block mt-2" id="countryError"></span>
                    </div>

                    <!-- Button -->
                    <div class="modal-footer border-0">
                        <button class="btn btn-dark w-100 fw-bold" id="saveCountry">
                            Continue
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif


@endsection
@section('js')
    <script>
        $(document).ready(function() {
            if (window.location.pathname.includes("/nurse/dashboard")) {
                $('#registrationCountryModal').modal('show');
            }

        });

        $('#registration_country').on('change', function() {
            let selectedId = $(this).find(':selected').data('id');
            $('#country_id').val(selectedId);
        });

        $('#saveCountry').on('click', function() {

            const country = $('#registration_country').val();
            const country_code = $('#country_id').val();

            if (!country) {
                $('#countryError').text('Please select a country');
                return;
            }


            $.ajax({
                url: "{{ route('nurse.saveRegistrationCountry') }}",
                type: "POST",
                data: {
                    country_id: country,
                    country_code: country_code,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    $('#registrationCountryModal').modal('hide');

                    // Unlock UI
                    $('.profession-tab').removeClass('disabled');

                    // Redirect cleanly
                    // window.location.href = "{{ route('nurse.my-profile') }}?page=my_profile";
                    window.location.href = "{{ route('nurse.dashboard') }}";
                }
            });
        });
    </script>

    {{-- 20/2  --}}

    <script>
        const chips = document.querySelectorAll('.filter-chip');

        chips.forEach(chip => {
            chip.addEventListener('click', function() {

                const isTop = this.dataset.mode === "top";

                // deactivate Top Matches if other clicked
                if (!isTop) {
                    document.querySelector('[data-mode="top"]')
                        .classList.remove('active');
                }

                this.classList.toggle('active');

                // if none active → activate Top Matches
                const activeChips =
                    document.querySelectorAll('.filter-chip.active');

                if (activeChips.length === 0) {
                    document.querySelector('[data-mode="top"]')
                        .classList.add('active');
                }
            });
        });
    </script>
    <script>
     function applyNow(user_id, job_id) {
        $.ajax({
            type: "POST",
            url: "{{ url('/nurse/applyJobs') }}",
            data: {
                user_id: user_id,
                job_id: job_id,
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            success: function (response) {

                if (response.status == true) {

                    let btn = $('.apply-btn-' + job_id);

                    btn.text('Applied');
                    btn.addClass('applied');
                    btn.prop('disabled', true);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });

                } else {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: response.message,
                        confirmButtonColor: '#d33'
                    });

                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong!',
                });
            }
        });
    }
    </script>
@endsection
