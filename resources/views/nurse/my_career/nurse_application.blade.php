@extends('nurse.layouts.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ url('/public') }}/nurse/assets/css/jquery.ui.datepicker.monthyearpicker.css">
<link rel='stylesheet'
  href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<style type="text/css">
  .hide_profile_image {
    display: none !important;
  }
  span.select2.select2-container {
    padding: 5px !important;
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple {
    background-color: white !important;
    border: 1px solid #0000 !important;
    border-radius: 4px !important;
    cursor: text !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #000 !important;
    border: 1px solid #000 !important;
    border-radius: 4px !important;
    cursor: default !important;
    color: #fff !important;
    float: left;
    padding: 0;
    padding-right: 0.75rem;
    margin-top: calc(0.375rem - 2px);
    margin-right: 0.375rem;
    padding-bottom: 2px;
    white-space: normal;
    line-height: 20px;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    font-size: 20px !important;
    float: left;
    padding-right: 3px;
    padding-left: 3px;
    margin-right: 1px;
    margin-left: 3px;
    font-weight: 700;
    line-height: 20px;
  }
  .registration_progress {
    font-weight: 900;
    background-color: black;
    color: #fff;
  }
  form#multi-step-form-nurseProfileForm ul.select2-selection__rendered {
    box-shadow: none;
    max-height: inherit;
    border: none;
    position: relative;
  }
  .category {
    margin-bottom: 1.5rem;
  }
  .label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.4rem;
    font-weight: 600;
  }
  .progress-bar-bg {
    background: #e4e8ee;
    height: 16px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
  }
  .progress-bar-fill {
    background: #000;
    height: 100%;
    border-radius: 10px;
    color: white;
    font-size: 12px;
    padding-left: 6px;
    display: flex;
    align-items: center;
  }
  /*07/02 */
  .status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
  }
  .under-review {
    background: #facc15;
    color: #856404;
  }
  .offer {
    background: #22c55e;
    color: #155724;
  }
  .shortlisted {
    background: #ef4444;
    color: #ffffff;
  }
  /* Right side modal */
  .modal.right .modal-dialog {
    position: fixed;
    right: 0;
    margin: 0;
    width: 420px;
    height: 100%;
    transform: translate3d(0%, 0, 0);
  }
  .modal.right .modal-content {
    height: 100%;
    border-radius: 0;
    border: none;
  }
  .timeline {
    position: relative;
    padding-left: 30px;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 8px;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
  }
  .timeline-item {
    position: relative;
    margin-bottom: 20px;
  }
  .timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 8px;
    width: 10px;
    height: 10px;
    background: #facc15;
    border-radius: 50%;
  }
  .pl-370 {
    padding-left: 370px !important;
  }
  .application .nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #000000;
    border-top: 0;
    border-left: 0;
    border-right: 0;
    border-radius: 0;
  }
  .application .nav-tabs .nav-link:focus,
  .nav-tabs .nav-link:hover {
    border-color: transparent;
    isolation: isolate;
  }
  .filter-btn {
    /*border: 1px solid #e5e7eb;*/
    font-size: 14px;
    padding: 6px 12px;
    background: #fff;
  }
  .filter-btn:hover {
    background: #f8f9fa;
  }
  .search-input {
    padding-left: 32px;
    height: 38px;
    font-size: 14px;
  }
  .search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
  }
  .filter-border {
    border: 1px solid #dee2e6;
    border-radius: 10px;
  }
  .filter-border .btn-light {
    border-color: transparent;
    padding-left: 10px;
  }
  .filter-border input {
    border: 0;
    padding-left: 32px;
    height: auto !important;
    padding: 11px 35px;
  }
  .filter-item:not(:last-child) {
    border-right: 1px solid #e5e7eb;
    padding: 7px 15px;
  }
  .filter-item i {
    margin-right: 8px !important;
  }
  .whitespace-nowrap {
    white-space: nowrap;
    padding: 7px 15px;
  }
  .pt-7 {
    padding: 7px 0;
  }
  .pl-20 {
    padding-left: 20px;
  }
  .application-table th {
    color: #6b7280;
  }
  .application-table .table-bordered,
  .table-bordered td {
    border: 1px solid #dee2e6 !important;
    padding: 15px;
  }
  .application-table .table-bordered>:not(caption)>*>* {
    border-width: inherit;
  }
  .application-table thead {
    border-bottom: transparent;
  }
  .table-nurse-head {
    font-size: 16px;
    color: #000000;
  }
  .application-table small {
    font-size: 12px !important;
  }
  .timeline-item small {
    font-size: 12px;
    color: #6b7280;
  }
  .progress-content {
    background: #60a5fa14;
    width: auto;
    padding: 20px;
    border-radius: 20px;
  }
  .close {
    border: 0;
    background: transparent;
  }
  .pending-offer-head {
    color: #60a5fa;
  }
  .pending-des {
    position: relative;
    padding-left: 15px;
    max-width: 160px;
    font-size: 12px;
  }
  .pending-des::before {
    content: '';
    position: absolute;
    left: 0;
    top: 10px;
    background: #6b7280;
    width: 8px;
    height: 8px;
    border-radius: 100%;
  }
  .status-badge.submitted {color: white; background:#6b7280; }
  .status-badge.under_review { color: white;background:#facc15; }
  .status-badge.shortlisted { color: white;background:#f59e0b; }
  .status-badge.interview_scheduled {color: white; background:#3b82f6; }
  .status-badge.interview_completed { color: white;background:#60a5fa ; } 
  .status-badge.conditional_offer {color: white; background:#3b82f6 ; }
  .status-badge.offer {color: white; background:#22c55e; }
  .status-badge.hired {color: white; background:#8b5cf6; }
  .status-badge.withdrawn {color:white; background:#374151; }
  .status-badge.rejected,
  .status-badge.declined { color: white;background:#ef4444; }

  /*10/02 */

  .offer-modal {
  border-radius: 12px;
}

.accept-box {
  background: linear-gradient(135deg, #eef8ff, #f9fcff);
}

.status-box {
  background: #f5f7f9;
}

.badge-success {
  background-color: #22b573;
}

.modal-footer .btn-success {
  background-color: #22b573;
  border: none;
}


/*view interview details */
.process-modal {
  border-radius: 12px;
}

.interview-status {
  background: #fff3cd;
  border: none;
  border-radius: 8px;
  color: #664d03;
}

.process {
  position: relative;
  padding-left: 30px;
}

.process::before {
  content: "";
  position: absolute;
  left: 6px;
  top: 6px;
  bottom: 0;
  width: 2px;
  background: #e9ecef;
}

.process-item {
  position: relative;
  display: flex;
  margin-bottom: 20px;
}

.process-item.last {
  margin-bottom: 0;
}

.process-item .dot {
  position: absolute;
  left: -30px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #ced4da;
  margin-top: 6px;
}

.process-item.active .dot {
  background: #007bff;
}

.process-item.orange .dot {
  background: #f0ad4e;
}

.process-item .content {
  padding-left: 10px;
}
.withdraw-text{
  font-size: 12px;
}

/*view details */
.offer-review-modal {
  border-radius: 12px;
}

/* Left card */
.left-card {
  background: #f8f9fa;
  /*border-radius: 10px;*/
}

/* Status alert */
.status-alert {
  background: #fff4da;
  border-radius: 8px;
  padding: 14px 16px;
  color: #6b4e00;
}

/* Document cards */
.doc-card {
  background: #f4f9fd;
  border-radius: 10px;
  padding: 14px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
#offerReviewModal small{
  font-size: 12px;
}


 

  
  }
</style>
@endsection
@section('content')
<main class="main">
  <section class="section-box mt-0">
    <div class="row m-0 profile-wrapper">
      <div class="col-lg-3 col-md-4 col-sm-12 p-0 left_menu">
        @include('nurse.layouts.career_sidebar')
      </div>
      <main class="main">
        <section class="section-box mt-0">
          <div class="row m-0 profile-wrapper">
            {{-- LEFT SIDEBAR --}}
            <div class="col-lg-3 col-md-4 col-sm-12 p-0 left_menu">
              @include('nurse.layouts.career_sidebar')
            </div>
            {{-- RIGHT CONTENT --}}
            <div class="container mt-5 pl-370 application">
              <!-- Header Row -->
              <div class="d-flex align-items-center mb-3 ">
                <h4 class="mb-0">Applications</h4>
                <!-- Tabs -->
                <ul class="nav nav-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#active">
                      Active
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#archived">
                      Archived <span class="badge badge-light ml-1 under-review">12</span>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- Tab Content (FULL WIDTH) -->
              <div class="tab-content">
                <!-- Active Tab -->
                <div class="tab-pane fade show active" id="active">
                  <!-- Filters row (optional) -->
                  <div class="d-flex w-100 align-items-center mb-3 gap-4">
                    <!-- Left Filters -->
                    <div class="d-flex align-items-center filter-border w-100">
                      <!-- Status Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <span class=""><i class="fas fa-search"></span></i> Status
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Under Review</a>
                          <a class="dropdown-item" href="#">Offer</a>
                          <a class="dropdown-item" href="#">Shortlisted</a>
                          <a class="dropdown-item" href="#">Rejected</a>
                        </div>
                      </div>
                      <!-- Date Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <i class="far fa-calendar-alt mr-1"></i> Last 30 Days
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Last 7 Days</a>
                          <a class="dropdown-item" href="#">Last 30 Days</a>
                          <a class="dropdown-item" href="#">Last 6 Months</a>
                        </div>
                      </div>
                      <!-- Search -->
                      <div class="position-relative mr-2 filter-item">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control search-input" placeholder="Search..." />
                      </div>
                    </div>
                    <!-- Clear Filters -->
                    <div class="filter-border pt-7">
                      <button
                        class="btn btn-light filter-btn d-flex align-items-center w-100 whitespace-nowrap filter-btn gap-4">
                        <span class="d-flex gap-2"> <i class="fas fa-sliders-h mr-1"></i> Clear
                          Filters</span>
                        <span class="text-muted ml-1">4 results</span>
                      </button>
                    </div>
                  </div>
                  <!-- Table -->
                  <div class="application-table">
                    <table class="table  bg-white">
                      <thead>
                        <tr>
                          <th>Job Title</th>
                          <th>Facility</th>
                          <th>Type</th>
                          <th>Status</th>
                          <th>Date Applied </th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody class="table-bordered" >
                        @foreach ($active_list as $list )
                        <tr>
                          <td>
                            <p class="table-nurse-head"> {{$list->job_title}}</p>
                          </td>
                          <td>
                            <p class="table-nurse-head"> St.John Hospital</p>
                          </td>
                          <td>
                            <p class="table-nurse-head"> St.John Hospital</p>
                          </td>
                          <td>
                              {{-- <span class="status-badge {{ $list->status }}">
                                  {{ ucwords(str_replace('_', ' ', $list->status)) }}
                              </span> --}}
                            {{-- <span class="status-badge {{ $list->status_key }}">
                                {{ $list->status_label }}
                            </span> --}}
                          <span
                              class="status-badge {{ $list->status_key }} open-status-modal"
                              data-id="{{ $list->id }}"
                              data-toggle="modal"
                              data-target="#underReviewModal"
                          >
                              {{ $list->status_label }}
                          </span>
                          </td>
                        <td>
                            <p>{{ \Carbon\Carbon::parse($list->applied_at)->format('j M Y') }}</p>
                        </td>
                        <td>
                            @switch($list->status)

                                @case(1) {{-- submitted --}}
                                @case(2) {{-- under_review --}}
                                @case(3) {{-- shortlisted --}}
                                    <button class="btn btn-outline-danger status-badge" data-toggle="modal" data-target="#withdrawModal">
                                        Withdraw
                                    </button>
                                    @break

                                @case(4) {{-- interview_scheduled --}}
                                    <button class="btn btn-outline-primary status-badge" data-toggle="modal" 
                                    data-target="#interviewProcessModal">
                                        View Interview Details
                                    </button>
                                    @break

                                @case(6) {{-- conditional_offer --}}
                                    <button class="btn btn-outline-info status-badge" data-toggle="modal" data-target="#offerReviewModal">
                                        View Offer
                                    </button>
                                    @break

                                @case(7) {{-- offer --}}
                                    <button class="btn btn-outline-success status-badge" data-toggle="modal" data-target="#acceptOfferModal">
                                        Accept Offer
                                    </button>
                                    @break

                                @default
                                    <span class="text-muted">—</span>
                            @endswitch
                        </td>
                        </tr>
                        <!-- modal offer  -->
                        <div class="modal right fade" data-id={{$list->id}} id="underReviewModal" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  Registered Nurse <br>
                                  <small class="text-muted">St. John Hospital</small>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">
                                <div class="alert alert-warning">
                                  <strong>Under Review</strong><br>
                                  Your application is currently being reviewed.
                                </div>
                                <!-- <p class="mb-2"><strong>Progress (100%)</strong></p> -->
                                <div class="timeline">
                                  <div class="timeline-item">
                                    <span><strong>Progress </strong><small> (100%)</small></span><br>
                                    <small>Lorem ipsum dolor sit amet</small>
                                    <p>5 Nov 2025</p>
                                  </div>
                                  <div class="timeline-item">
                                    <small>A SmuRevined</small><br>
                                    <div class="progress-content">
                                      <small>test</small>
                                      <p>5 Nov 2025</p>
                                    </div>
                                  </div>
                                  <div class="timeline-item">
                                    <strong class="pending-offer-head">A Offer</strong><br>
                                    <div class="d-flex">
                                      <p class="pending-des">Lorem ipsum dolor sit amet,Lorem ipsum dolor
                                        sit amet,</p>
                                      <p class="text-dark">18 jan 2025</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button class="btn btn-dark btn-block w-100">
                                  Withdraw Application
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>



                        <!-- Accept offer modal  -->
                        <div class="modal fade" id="acceptOfferModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content offer-modal">

                    <!-- Header -->
                    <div class="modal-header border-0 flex w-100 justify-content-between flex-column">
                      <div class="d-flex justify-content-between w-100">
                        <h5 class="mb-1 font-weight-bold">Midwife Offer</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      
                      </div> 
                      <div class="d-flex justify-content-between w-100">
                          <small class="text-muted">
                          Royal Women's Hospital, Full-Time
                        </small>
                      <small class="text-muted ml-auto mr-3">3 Nov 2025</small>
                      
                      </div>
                    </div>

                    <hr class="my-0">

                    <!-- Body -->
                    <div class="modal-body">
                      <div class="row">

                        <!-- Left Content -->
                        <div class="col-md-8">
                          <div class="d-flex gap-2 mb-3">
                          <!-- <span class="py-2 offer-process"></span> -->
                          <span class="pl-2">Offer</span>
                          </div>

                          <!-- Offer Card -->
                          <div class="border rounded p-3">
                            <div class="row">
                              <div class="col-sm-8">
                                <div class="d-flex justify-content-between w-100">
                                  <div>
                                     <small class="text-muted">Start date:</small>
                                    <small>21 Nov 2025</small>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <small>$85,000/year</small>
                              </div>
                          </div>
                              <div class="row">
                              <div class="col-sm-8">
                                <div class="d-flex justify-content-between w-100">
                                  <div>
                                     <small class="text-muted">Shift:</small>
                                    <small>Days</small>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <small>$85,000/year</small>
                              </div>
                            </div>
                             <div class="row">
                              <div class="col-sm-8">
                                <div class="d-flex justify-content-between w-100">
                                  <div>
                                     <small class="text-muted">Shift:</small>
                                    <small>Days</small>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <small>$85,000/year</small>
                              </div>
                            </div>
               </div>
            </div>

              <!-- Right Status -->
          <div class="col-md-4">
            <div class="border rounded">
              <div class="status-box p-3">
              <div class="d-flex align-items-center gap-2">
              <h6 class="font-weight-bold">Status</h6>
              <small>3 kev</small>
              </div>
              <span class="badge badge-success px-3 py-2 d-inline-block">
                Offer
              </span>
              </div>
              <div class="p-3">
              <p class="mb-2">
                <small class="text-muted">Date applied</small><br>
                3 Nov 2025
              </p>

             <!--  <p>
                <strong> <small>startnine</small> </strong>
              </p> -->

              </div>
            </div>
          </div>

            <div>
            <h6 class="font-weight-bold mb-3 text-center">Accept Job Offer?</h6>
            <div class="accept-box p-3 rounded text-center">
              <div>
              <small class="mb-2">
               <strong><small>Starting:</small></strong>
                <span class="text-success ml-1">●</span> 21 Nov 2025
              </small>
              </div>
              <div>
              <small class="mb-2">
                <strong>Salary:</strong> $85,000/year casual rate
              </small>
              </div>
              <div>
              <div class="mb-2 d-flex justify-content-center align-items-center">
               <small> <strong>Shifts:</strong> </small>
                <span> <small> Days & Evenings</small></span>
                <!-- <span> <small><a href="#" class="text-primary">Sign-on bonus</a></small></span> -->
              </div>
             
              </div>
            </div>
            </div>
            </div>
            <!-- Footer -->
            <div class="modal-footer border-0">
              <button class="btn btn-success px-5">
                Accept Offer
              </button>
              <button class="btn btn-outline-secondary" data-dismiss="modal">
                Cancel
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>


      <!-- view interview details modal -->

   <div class="modal fade" id="interviewProcessModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content process-modal">

      <!-- Header -->
      <div class="modal-header border-b d-flex flex-column">
        <div class="d-flex justify-content-between w-100">
          <h5 class="mb-1 font-weight-bold">Aged Care Nurse Interview</h5>
           <button class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="d-flex justify-content-between w-100">
        <small class="text-muted ml-auto mr-3">5 Nov 2025</small>
        <small class="text-muted">St. John Hospital; Casual</small>
       </div>
      </div>

      <!-- <hr class="my-0"> -->

      <!-- Body -->
      <div class="modal-body">

        <!-- Status -->
        <div class="alert interview-status mb-4">
          <strong>Interview Scheduled</strong><br>
          Your application is currently being reviewed.
        </div>

        <div class="row">

          <!--INTERVIEW LEFT PROCESS -->
          <div class="col-md-6">
            <h6 class="font-weight-bold mb-2">Interview Details</h6>

            <div class="process">

              <div class="process-item active">
                <span class="dot"></span>
                <div class="content">
                  <small><strong>St. John Hospital</strong></small><br>
                  <small class="text-muted">AddFres</small>
                </div>
              </div>

              <div class="process-item">
                <span class="dot"></span>
                <div class="content">
                <small> <strong>Date:</strong> Monday, 12 November 2025</small> <br>
                 <small> <strong>Time:</strong> </small>
                </div>
              </div>

              <div class="process-item last">
                <span class="dot"></span>
                <small class="content">
                  Sarah Thompson, Nurse Manager
                </small>
              </div>

            </div>
          </div>

          <!--INTERVIEW RIGHT PROCESS CARD -->
          <div class="col-md-6">
            <div class="border rounded px-3 py-2">
              <h6 class="font-weight-bold mb-2">Interview Details</h6>

              <div class="process">

                <div class="process-item orange">
                  <span class="dot"></span>
                  <div class="content">
                    <small><strong>St. John Hospital</strong></small><br>
                    <small class="text-muted">
                      123 Health Road, Sydney, NSW 2000
                    </small>
                  </div>
                </div>

                <div class="process-item active">
                  <span class="dot"></span>
                  <small class="content">
                    <strong>Time:</strong> 10:00 AM
                  </small>
                </div>

                <div class="process-item last">
                  <span class="dot"></span>
                  <div class="content">
                  <small>  <strong>Interviewer:</strong><br>
                    Sarah Thompson, Nurse Manager</small>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-t d-flex justify-content-center">
        <button class="btn btn-danger px-4">
          Withdraw Application
        </button>
        <button class="btn btn-outline-secondary px-4">
          Message Employer
        </button>
      </div>

    </div>
  </div>
</div>


 <!-- ------ -->

 <!-- withdraw  -->

 <div class="modal fade" id="withdrawModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content withdraw-modal">

      <!-- Header -->
      <div class="modal-header border-b d-flex justify-content-between flex-column">
        <div class="d-flex justify-content-between w-100">
          <h5 class="mb-1 font-weight-bold">Withdraw Application</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="d-flex justify-content-between w-100">
        <small class="text-muted ml-auto mr-3">5 Nov 2025</small>
          <small class="text-muted">St. John Hospital · Casual</small>
        </div>
      </div>

      <!-- <hr class="my-0"> -->

      <!-- Body -->
      <div class="modal-body">
        <h6 class="font-weight-bold mb-2">
          Withdraw This Application?
        </h6>

        <small class="text-muted mb-4">
          Are you sure you want to withdraw your application for the
          Registered Nurse position at St. John Hospital? This action
          cannot be undone.
        </small>

        <div class="form-group">
         <small> <label class="font-weight-bold mt-2">
            Reason for Withdraw <span class="text-muted">(Required):</span>
          </label></small>
          <select class="form-control withdraw-text">
            <option selected disabled class="withdraw-text">Select a reason for withdrawing...</option>
            <option class="withdraw-text">Accepted another offer</option>
            <option class="withdraw-text">Position no longer suitable</option>
            <option class="withdraw-text">Change in availability</option>
            <option class="withdraw-text">Other</option>
          </select>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-0 justify-content-start pt-0">
        <button class="btn btn-danger px-4">
          Withdraw Application
        </button>
        <button class="btn btn-outline-secondary px-4" data-dismiss="modal">
          Cancel
        </button>
      </div>

    </div>
  </div>
</div>
 <!-- ----- -->




                    



                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <!-- Right Side Modal -->
                <div class="modal right fade" id="underReviewModal" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          Registered Nurse <br>
                          <small class="text-muted">St. John Hospital</small>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                        <div class="alert alert-warning">
                          <strong>Under Review</strong><br>
                          Your application is currently being reviewed.
                        </div>
                        <!-- <p class="mb-2"><strong>Progress (100%)</strong></p> -->
                        <div class="timeline">
                          <div class="timeline-item">
                            <span><strong>Progress </strong><small> (100%)</small></span><br>
                            <small>Lorem ipsum dolor sit amet</small>
                            <p>5 Nov 2025</p>
                          </div>
                          <div class="timeline-item">
                            <small>A SmuRevined</small><br>
                            <div class="progress-content">
                              <small>test</small>
                              <p>5 Nov 2025</p>
                            </div>
                          </div>
                          <div class="timeline-item">
                            <strong class="pending-offer-head">A Offer</strong><br>
                            <div class="d-flex">
                              <p class="pending-des">Lorem ipsum dolor sit amet,Lorem ipsum dolor
                                sit amet,</p>
                              <p class="text-dark">18 jan 2025</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-dark btn-block w-100">
                          Withdraw Application
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Archived Tab -->
                <div class="tab-pane fade" id="archived">
                             <div class="d-flex w-100 align-items-center mb-3 gap-4">
                    <!-- Left Filters -->
                    <div class="d-flex align-items-center filter-border w-100">
                      <!-- Status Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <span class=""><i class="fas fa-search"></span></i> Status
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Under Review</a>
                          <a class="dropdown-item" href="#">Offer</a>
                          <a class="dropdown-item" href="#">Shortlisted</a>
                          <a class="dropdown-item" href="#">Rejected</a>
                        </div>
                      </div>
                      <!-- Date Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <i class="far fa-calendar-alt mr-1"></i> Last 30 Days
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Last 7 Days</a>
                          <a class="dropdown-item" href="#">Last 30 Days</a>
                          <a class="dropdown-item" href="#">Last 6 Months</a>
                        </div>
                      </div>
                      <!-- Search -->
                      <div class="position-relative mr-2 filter-item">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control search-input" placeholder="Search..." />
                      </div>
                    </div>
                    <!-- Clear Filters -->
                    <div class="filter-border pt-7">
                      <button
                        class="btn btn-light filter-btn d-flex align-items-center w-100 whitespace-nowrap filter-btn gap-4">
                        <span class="d-flex gap-2"> <i class="fas fa-sliders-h mr-1"></i> Clear
                          Filters</span>
                        <span class="text-muted ml-1">4 results</span>
                      </button>
                    </div>
                  </div>
                  <!-- Table -->
                  <div class="application-table table-responsive">
                    <table class="table bg-white">
                      <thead>
                        <tr>
                          <th>Job Title</th>
                          <th>Facility</th>
                          <th>Type</th>
                          <th>Status</th>
                          <th>Date Applied </th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                   <tbody class="table-bordered">
                      @foreach ($archived_list as $list)
                      <tr>
                          <td>
                              <p class="table-nurse-head">{{ $list->job_title }}</p>
                          </td>
                          <td>
                              <p class="table-nurse-head">St. John Hospital</p>
                          </td>
                          <td>
                              <p class="table-nurse-head">St. Archieved Hospital</p>
                          </td>
                          <td>
                            <span
                                class="status-badge {{ $list->status_key }} open-status-modal"
                                data-id="{{ $list->id }}"
                                data-toggle="modal"
                                data-target="#underArchievedModal"
                            >
                                {{ $list->status_label }}
                            </span>
                          </td>
                          <td>
                              <p>{{ \Carbon\Carbon::parse($list->applied_at)->format('j M Y') }}</p>
                          </td>
                          <td>
                              <button class="btn btn-outline-secondary status-badge">
                                  View Details
                              </button>
                          </td>
                      </tr>

                      <!-- Right Side Modal -->
                      <div class="modal right fade" id="underArchievedModal" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">
                                Registered Nurse <br>
                                <small class="text-muted">St. John Hospital</small>
                              </h5>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                              <div class="alert alert-warning">
                                <strong>Under Review</strong><br>
                                Your application is currently being reviewed.
                              </div>
                              <!-- <p class="mb-2"><strong>Progress (100%)</strong></p> -->
                              <div class="timeline">
                                <div class="timeline-item">
                                  <span><strong>Progress </strong><small> (100%)</small></span><br>
                                  <small>Lorem ipsum dolor sit amet</small>
                                  <p>5 Nov 2025</p>
                                </div>
                                <div class="timeline-item">
                                  <small>A SmuRevined</small><br>
                                  <div class="progress-content">
                                    <small>test</small>
                                    <p>5 Nov 2025</p>
                                  </div>
                                </div>
                                <div class="timeline-item">
                                  <strong class="pending-offer-head">A Offer</strong><br>
                                  <div class="d-flex">
                                    <p class="pending-des">Lorem ipsum dolor sit amet,Lorem ipsum dolor
                                      sit amet,</p>
                                    <p class="text-dark">18 jan 2025</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button class="btn btn-dark btn-block w-100">
                                Withdraw Application
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            test
          </div>
        </section>
      </main>
    </div>
  </section>
</main>

 <!-- View Details Modal  -->
 <div class="modal fade" id="offerReviewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content offer-review-modal">

      <!-- Header -->
      <div class="modal-header border-b d-flex justify-content-between flex-column">
        <div class="d-flex justify-content-between w-100">
          <h5 class="mb-1 font-weight-bold">Registered Nurse</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="d-flex justify-content-between w-100">
        <small class="text-muted ml-auto mr-3">5 Nov 2025</small>
        <small class="text-muted">St. John Hospital; Casual</small>
        </div>
      </div>

      <!-- <hr class="my-0"> -->

      <!-- Body -->
      <div class="modal-body">
        <div class="row">

          <!-- LEFT PANEL -->
          <div class="col-md-4 mb-3 mb-md-0">
             <span class="mb-3 px-3 py-2 text-black">
                Offer Review
              </span>
            <div class="border rounded mt-2">
             
              <div class="left-card p-3">

              <h6 class="font-weight-bold mb-2">St. John Hospital</h6>
              <small class="text-muted mb-2">
                123 Health Road, Sydney, NSW 2000
              </small>
              </div>
              <div class="p-3">

            <p>  <small class="mb-1">Casual / Part Time</small></p>
            <p>  <small class="mb-0 text-muted font-weight-bold">
                Full-Time ✓
              </small>
              </p>
              </div>
            </div>
          </div>

          <!-- RIGHT PANEL -->
          <div class="col-md-8">
            <div class="status-alert mb-4">
              <strong>Under Review</strong><br>
              Your application is currently being reviewed.
            </div>

            <h6 class="font-weight-bold mb-3">
              Document Requirements
            </h6>

            <div class="doc-card mb-3">
              <small>
                <strong>Police Check</strong>
              </small>
              <small class="text-muted">
                No documents requested yet
              </small>
            </div>

            <div class="doc-card">
              <small>
                <strong>Vaccination Record</strong>
              </small>
              <small class="text-muted">
                No documents requested yet
              </small>
            </div>
          </div>

        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-t justify-content-center">
        <button class="btn btn-outline-secondary px-4">
          Message Employer
        </button>
        <button class="btn btn-danger px-4">
          Withdraw Application
        </button>
      </div>

    </div>
  </div>
</div>


<!-- ----- -->

@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<script src="{{ url('/public') }}/nurse/assets/js/jquery.ui.datepicker.monthyearpicker.js"></script>
{{-- @include('nurse.front_profile_js'); --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>

<script>
    $(document).on('click', '.open-status-modal', function () {

        var applicationId = $(this).data('id');

        $('#modalTitle').html('');
        $('#modalFacility').html('');
        $('#modalContent').html('');
        $('#modalFooter').html('');
        $('#modalLoader').show();

        $.ajax({
            type: "GET",
            url: "{{ url('/nurse/application-timeline') }}",
            data: { application_id: applicationId },
            cache: false,
            success: function (data) {

                var res = JSON.parse(data);
                console.log(res);

                $('#modalLoader').hide();

                /* -------- Header -------- */
                $('#modalTitle').html(res.job_title);
                $('#modalFacility').html(res.facility);

                /* -------- Timeline -------- */
                var timeline_html = "";

                for (var i = 0; i < res.timeline.length; i++) {

                    timeline_html +=
                        "<div class='timeline-item'>" +
                            "<strong>" + res.timeline[i].title + "</strong>" +
                            "<p>" + res.timeline[i].desc + "</p>" +
                            "<small>" + res.timeline[i].date + "</small>" +
                        "</div>";
                }

                $('#modalContent').html(timeline_html);

                /* -------- Footer -------- */
                var footer_html = "";

                if (res.footer_action == 'withdraw') {
                    footer_html =
                        "<button class='btn btn-dark btn-block'>Withdraw Application</button>";
                }

                if (res.footer_action == 'interview') {
                    footer_html =
                        "<button class='btn btn-outline-primary btn-block'>View Interview Details</button>";
                }

                if (res.footer_action == 'offer') {
                    footer_html =
                        "<button class='btn btn-success btn-block'>Accept Offer</button>";
                }

                $('#modalFooter').html(footer_html);
            }
        });

    });
</script>

@endsection