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
                                    <button class="btn btn-outline-danger status-badge">
                                        Withdraw
                                    </button>
                                    @break

                                @case(4) {{-- interview_scheduled --}}
                                    <button class="btn btn-outline-primary status-badge">
                                        View Interview Details
                                    </button>
                                    @break

                                @case(6) {{-- conditional_offer --}}
                                    <button class="btn btn-outline-info status-badge">
                                        View Offer
                                    </button>
                                    @break

                                @case(7) {{-- offer --}}
                                    <button class="btn btn-outline-success status-badge">
                                        Accept Offer
                                    </button>
                                    @break

                                @default
                                    <span class="text-muted">—</span>
                            @endswitch
                        </td>
                        </tr>
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
                              <button class="btn btn-outline-secondary status-badge" >
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
        </section>
      </main>
    </div>
  </section>
</main>
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