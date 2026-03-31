@extends('nurse.layouts.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ url('/public') }}/nurse/assets/css/jquery.ui.datepicker.monthyearpicker.css">
<link rel='stylesheet'
    href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<style>
 .pagination-wrapper .page-item.active .page-link {
    background-color: #000000ff;
    border-color: #000000ff;
    border-radius: 10px;
  }

  .pagination-wrapper .page-link {
    color: #000000ff;
  }

  .pagination-wrapper .page-item .page-link {
    border-radius: 10px;
  }

  .pagination-wrapper .pagination .active {
    background: #000000ff;
    border-radius: 10px;

  }
  .front-pagination{
    align-items: center;
  }
    /* ================= Job Tab ================= */

    .find_job_div {
        background: #f5f6fa;
    }

  .no-jobs-box {
    padding: 20px;
    margin: 15px 0;
    border: 2px dashed #ccc;
    border-radius: 8px;
    background: #fafafa;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  }
  .no-jobs-box h3 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #444;
    }
/* ================= FILTER BAR ================= */

.search-bar {
    display: flex;
    gap: 14px;
    align-items: center;
    margin-top: 15px;
}

/* Dropdown + input fields */
.search-bar select,
.search-bar input {
    height: 44px;
    min-width: 223px;
    padding: 0 14px;
    font-size: 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    color: #374151;
    outline: none;
    transition: all 0.2s ease;
}

/* Focus effect */
.search-bar select:focus,
.search-bar input:focus {
    border-color: #3b82f6;
    background: #ffffff;
}

/* Sort dropdown slightly smaller */
.sort_by_filter select {
    min-width: 160px;
}

/* ================= BUTTONS ================= */

#add-search-btn {
    background: #000;
    color: #fff;
    border: none;
    height: 42px;
    padding: 0 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}

#add-search-btn:hover {
    background: #1f2937;
}

/* Top Matches Button */
.search-bar button {
    background: #000;
    color: #fff;
    border: none;
    height: 42px;
    padding: 0 18px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
}

/* ================= CONTAINER ALIGNMENT ================= */

.find-jobs-header {
    margin-bottom: 15px;
}

.find-jobs-title {
    font-size: 24px;
    font-weight: 700;
}

/* ================= TABS ================= */

.searchtabs {
    display: flex;
    align-items: center;
    /* remove space-between to avoid pushing right button off */
    gap: 10px;
    overflow: hidden; /* prevent parent from creating its own scrollbar */
}

.centered-filter {
    flex: 1; /* take up remaining space */
    display: flex;
    overflow-x: auto; /* only this section scrolls */
    gap: 10px;
    white-space: nowrap; /* keep tabs in one line */
    scrollbar-width: thin; /* Firefox */
}

.saved-search-tab {
    background: #f1f3f5;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    flex-shrink: 0; /* prevent shrinking */
}

.saved-add-search {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px dashed #7dd3fc;
    border-radius: 20px;
    padding: 6px 14px;
    flex-shrink: 0; /* keep fixed size */
}

/* ================= FILTER SIDEBAR ================= */

.filter-sidebar {
    border: 1px solid #ccc;
    border-radius: 6px;
    background: #fff;
}

.filter-header {
    padding: 12px;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
}

.filter-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.filter-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    cursor: pointer;
}

/* ================= CARD ================= */

.candidate-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.profile-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
}

.name {
    font-size: 18px;
    font-weight: 700;
}

.sub-text {
    font-size: 14px;
    color: #777;
}

/* ================= TAG ================= */

.job-tag {
    background: #f1f3f5;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
}

/* ================= BUTTON ================= */

.btn-custom {
    background: #000;
    color: #fff;
    border-radius: 6px;
    padding: 8px 16px;
}

/* ================= PROGRESS ================= */

.progress-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: conic-gradient(#28a745 0% 95%, #e6e6e6 95% 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: "";
    width: 70px;
    height: 70px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
}

.progress-text {
    position: absolute;
    text-align: center;
}

.progress-text h5 {
    margin: 0;
    font-weight: 700;
    color: #28a745;
}

.progress-text span {
    font-size: 12px;
    color: #777;
}

/* ================= JOB CARD ================= */

.job-card {
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 24px;
    background: #fff;
}

.job-logo {
    width: 48px;
    height: 48px;
    background: #007bff;
    border-radius: 8px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.job-footer {
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid #eee;
    padding-top: 10px;
}

.apply-btn {
    background: #3C8093;
    color: #fff;
    padding: 8px 18px;
    border-radius: 6px;
    border: none;
}

.details-link {
    color: #0EA5E9;
    text-decoration: none;
    font-size: 14px;
}

/* ================= MATCH CIRCLE ================= */

.match-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#16A34A calc(var(--percent)*1%), #9CA3AF 0);
    display: flex;
    align-items: center;
    justify-content: center;
}

.match-inner {
    width: 65px;
    height: 65px;
    background: #fff;
    border-radius: 50%;
    text-align: center;
}

.match-inner .percent {
    font-size: 18px;
    font-weight: bold;
    color: #16A34A;
}

.match-inner .label {
    font-size: 12px;
}
</style>
@endsection
@section('content')
<main class="main find_job_div">
    <section class="section-box mt-30">
        <div class="container">
            <div class="saved-searches-row" id="search-tabs">
                <div class="searchtabs">
                    <!-- Fixed left tab -->
                    <div class="saved-search-tab">
                        Browse All Nurse
                    </div>
                    @if(count($jobs) <= 0) 
                    <div id="no-job-post-hf" class="saved-search-tab" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="You don’t have active job postings yet.">
                        Post a Job
                    </div>
                     @endif
                    <!-- Scrollable center -->
                    <div class="centered-filter">
                        @forelse ($jobs as $job)
                        <div class="saved-search-tab" value="{{ $job->job_box_id }}">
                            {{ $job->display_name }}
                        </div> 
                        @empty
      
                        @endforelse
                        {{-- @foreach ($jobs as $job)
                        <div class="saved-search-tab" value="{{ $job->job_box_id }}">
                            {{ $job->display_name }}
                        </div>
                        @endforeach --}}
                
                    </div>
            
                    <!-- Fixed right button -->
                    <div class="add-new">
                        <button class="saved-add-search">+ Save New</button>
                    </div>
                </div>
            </div>
            <div>
                <div class="job_tabs">
                    <ul class="tab-nav">
                        <li class="active" data-tab="tab1">Find Nurse</li>
                        <li data-tab="tab2">Manage Saved Searches</li>
                    </ul>
                </div>
                <div id="tab1" class="tab-content-jobs active">
                    <div class="find-jobs-header d-flex justify-content-between align-items-center mb-3">
                        <h2 class="find-jobs-title mb-0 fw-bold">Find Nurse</h2>
                        {{-- <button id="add-search-btn">+ Save Search</button> --}}
                    </div>
                    <div class="search-bar">
                 
                        <div class="form-group top_filter location_filter">
                            <label for="job_start">Role / Speciality</label>
                             <input type="text" placeholder="Search by Nurse type or Speciality">
                        </div>
                        <div class="form-group top_filter location_filter">
                            <label for="job_start">Available to Start</label>
                            <select id="job_start">
                                <option value="any">Any</option>
                                <option value="instant">Instant Connect</option>
                                <option value="last_minute">Last Minute</option>
                                <option value="immediate">Immediate Start</option>
                            </select>
                        </div>
                        <!-- <input type="hidden" id="selectedLocations" name="locations"> -->
                        <div class="form-group top_filter location_filter">
                            <label for="sort">Search Nurse</label>
                             <input type="text" placeholder="Search by Name or Registration Number">
                        </div>
                        <div class="form-group top_filter location_filter">
                            <label for="sort">Sort By</label> 
                            <select>
                                <option value="">Top Matches</option>
                                <option value="">Highest Experience</option>
                                <option value="">Available Soonest</option>
                            </select>
                        </div>
                        <!-- <div class="top_filter sort_by_filter"> -->
                        {{-- <button class="form-group top_filter location_filter" id="add-search-btn">Top Matches</button> --}}
                        <!-- </div> -->
                    </div>
                    <div class="row">
                        <div class="filters col-md-4">
                            <div class="filter-sidebar">
                                <div class="filter-header">Filters</div>
                                <ul class="filter-list">
                                    <li class="filter-item">
                                        <span>Experience Level</span>
                                        <span class="arrow">›</span>
                                    </li>
                                    <li class="filter-item">
                                        <span>Speciality</span>
                                        <span class="arrow">›</span>
                                    </li>
                                    <li class="filter-item">
                                        <span>Certifications</span>
                                        <span class="arrow">›</span>
                                    </li>
                                    <li class="filter-item">
                                        <span>Language spoken</span>
                                        <span class="arrow">›</span>
                                    </li>
                                    <li class="filter-item">
                                        <span>Compliance status</span>
                                        <span class="arrow">›</span>
                                    </li>
                                    <li class="filter-item">
                                        <span>Availability</span>
                                        <span class="arrow">›</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Job Listings -->
                        <div class="job-listings col-md-8">
                            @forelse($nurse_list as $list)                 
                            <div class="candidate-card">
                                <!-- TOP -->
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                            class="profile-img mr-3">
                                        <div>
                                            <div class="name">{{$list->name}} {{$list->lastname}}</div>
                                            <div class="sub-text">
                                                <i class="fa fa-map-marker"></i> Los Angeles, {{ country_name($list->country) }}
                                            </div>
                                            <div class="sub-text mt-1">
                                                <i class="fa fa-briefcase"></i> 15 yrs Exp · ICU · ACLS, BLS
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <i class="fa fa-heart-o" style="font-size:20px;"></i>
                                    </div>
                                </div>
                                <hr>
                                <!-- JOB TAG -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="job-tag">
                                            <i class="fa fa-briefcase"></i> ICU RN · MQ-01425
                                        </span>
                                        <span class="ml-2 btn btn-light btn-sm">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <!-- STATUS -->
                                <div class="row">
                                    <div class="col-md-10 status-list">
                                        <p><i class="fa fa-check text-success"></i> Compliance: Verified</p>
                                        <p><i class="fa fa-check text-success"></i> Vaccinated: Up to Date</p>
                                        <p><i class="fa fa-check text-success"></i> Availability: Within 48h (Lat
                                            Minute)</p>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- PROGRESS -->
                                        <div class="progress-circle">
                                            <div class="progress-text">
                                                <h5>95%</h5>
                                                <span>Match</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- BUTTONS -->
                                <div class="d-flex gap-4 justify-content-end">
                                    <button class="btn btn-custom mr-2">
                                        <i class="fa fa-user"></i> Invite to Apply
                                    </button>
                                    <button class="btn btn-custom">
                                        <i class="fa fa-comments"></i> Invite to Interview
                                    </button>
                                </div>
                            </div>
                          @empty
                            <div id="no-jobs" class="no-jobs-box" >
                                <h3>🚫 No Nurse Found</h3>
                                <p>Sorry, no nurses match your search.</p>
                            </div>
                          @endforelse    
                          <div class="pagination-wrapper front-pagination">
                             {{ $nurse_list->links('pagination::bootstrap-4') }}
                          </div>       
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab2" class="tab-content-jobs">
                tab 2
            </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
    </section>
</main>
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<script src="{{ url('/public') }}/nurse/assets/js/jquery.ui.datepicker.monthyearpicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<script>
    $(document).ready(function() {
            $('.tab-nav li').click(function() {
                // Remove active classes
                $('.tab-nav li').removeClass('active');
                $('.tab-content-jobs').removeClass('active');
                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                $('#' + $(this).data('tab')).addClass('active');
            });
            $('.tab-nav-edit li').click(function() {
                // Remove active classes
                $('.tab-nav-edit li').removeClass('active');
                $('.tab-content-edit').removeClass('active');
                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                $('#' + $(this).data('tab')).addClass('active');
            });
        });
</script>
@endsection