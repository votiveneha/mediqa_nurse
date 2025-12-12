<style>
 
  .support-button {
    background-color: #000000;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin-left: 10px;
  }
  
  
  .support-button:hover {
    background-color: #000000;
    color:white;
    transform: translateY(-1px);
  }
  
  @media only screen and (min-width:1050px) and (max-width:1350px)  {
   
   .support-button {
   background-color: #000000;
   color: white;
   border: none;
   padding: 10px 8px;
   border-radius: 20px;
   font-size: 13px !important;
   font-weight: 500;
   cursor: pointer;
   transition: background-color 0.3s ease, transform 0.2s ease;
   margin-left: 10px;
}

.logout-line .font-md {
   font-size: 13px !important;
   line-height: 24px !important;
}

 }
</style>
<div class="sidebar_profile">
    <div class="box-company-profile mb-20">
      <div class="image-compay-rel">
        <img alt="{{  Auth::guard('nurse_middle')->user()->lastname }}" src="{{ asset( Auth::guard('nurse_middle')->user()->profile_img)}}">
      </div>
      <div class="row mt-10">
        <div class="text-center">
          <h5 class="f-18">{{ Auth::guard('nurse_middle')->user()->preferred }}</h5>
          @if(Auth::guard('nurse_middle')->user()->state)
          <span class="card-location font-regular">{{ state_name(Auth::guard('nurse_middle')->user()->state) }} , {{ country_name(Auth::guard('nurse_middle')->user()->country) }}</span>
          @endif
          <p class="mt-0 font-md color-text-paragraph-2 mb-15">{{ specialty_name_by_id(1) }}, 2 years</p>
        </div>
      </div>
    </div>

    <div class="profile-chklst">
      <span>My Career</span>
      
    </div>

    
    
    <div class="box-nav-tabs nav-tavs-profile mb-5 p-0 profile-icns">
      <ul class="nav" role="tablist">
        
        <li><a class="{{ request()->is('nurse/match_percentage') ?'active':'' }} btn btn-border recruitment-icon mb-20 profile_tabs" href="{{ route('nurse.match_percentage') }}"><i class="fi fi-rr-stethoscope"></i> Overall Match</a></li>
        <li><a class="{{ request()->is('nurse/work_environment_preferences') ?'active':'' }} btn btn-border recruitment-icon mb-20 profile_tabs" href="#"><i class="fi fi-rr-handshake"></i> Applications</a></li>
        <li><a class="{{ request()->is('nurse/employeement_type_preferences') ?'active':'' }} btn btn-border recruitment-icon mb-20 profile_tabs" href="#"><i class="fi fi-rr-briefcase"></i> Interviews</a></li>
        <li><a class="{{ request()->is('nurse/WorkShiftPreferences') ?'active':'' }} btn btn-border recruitment-icon mb-20 profile_tabs" href="#"><i class="fi fi-rr-calendar-clock"></i> My Jobs</a></li>
        <li><a class="{{ request()->is('nurse/matchedJobs') ?'active':'' }} btn btn-border recruitment-icon mb-20 profile_tabs" href="{{ route('nurse.matchedJobs') }}"><i class="fi fi-rr-calendar-clock"></i> Matched Jobs</a></li>
        
        
        <div class="mt-0 mb-20 logout-line">
          <a class="link-red font-md" href="{{ route('nurse.logout') }}"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out</a>
          <a class="support-button font-md" href="{{ route('contact') }}">Need support?</a>
        </div>
      </ul>
    </div>
  </div>