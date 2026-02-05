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
      <img alt="{{  Auth::guard('healthcare_facilities')->user()->lastname }}" src="{{ asset( Auth::guard('healthcare_facilities')->user()->profile_img)}}">
    </div>
    <div class="row mt-10">
      <div class="text-center">
        <h5 class="f-18">{{ Auth::guard('healthcare_facilities')->user()->preferred }}</h5>
        
      </div>
    </div>
  </div>

  <div class="profile-chklst">
    <span>Job Posting</span>
   
  </div>

  

  <div class="box-nav-tabs nav-tavs-profile mb-5 p-0 profile-icns">
    <ul class="nav" role="tablist">
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-user"></i> Basics</a></li>
      
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-marker"></i> Location</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-briefcase"></i> Contract & Pay</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-calendar-clock"></i> Shifts & Scheduling</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-document-signed"></i> Requirements</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-gift"></i> Benefits</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-eye"></i> Visibility & Apply Settings</a></li>
      <li><a class="btn btn-border aboutus-icon mb-20 profile_tabs" href="#" aria-controls="tab-my-profile" aria-selected="true"><i class="fi fi-rr-file-edit"></i> Job Description</a></li>
      <div class="mt-0 mb-20 logout-line">
        <a class="link-red font-md" href="{{ route('nurse.logout') }}"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out</a>
        
      </div>
    </ul>
  </div>
</div>