@extends('nurse.layouts.layout')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ url('/public') }}/nurse/assets/css/jquery.ui.datepicker.monthyearpicker.css">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

<style type="text/css">
  .hide_profile_image {
    display: none !important;
  }

  .iti input, .iti input[type=text], .iti input[type=tel] {
    padding-left: 80px !important;
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

  form#register_licenses_form ul.select2-selection__rendered {
    box-shadow: none;
    max-height: inherit;
    border: none;
    position: relative;
  }

  .sublang_main_div select{
    padding: 5px;
    border: 1px solid #dddddd;
    height: 50px;
  }

  .custom-select-wrapper {
  position: relative;
  width: 100%;
}

.custom-select {
  width: 100%;
  padding: 10px;
  appearance: none; /* Remove native arrow */
  -webkit-appearance: none;
  -moz-appearance: none;
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
}

/* Custom arrow */
.custom-select-wrapper::after {
  content: "▼";
  position: absolute;
  top: 76%;
  right: 10px;
  transform: translateY(-50%);
  pointer-events: none;
  color: black;
  height: 36px !important;
  width: 20px;
}

 .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide the default checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* Style for the slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 34px;
}

/* The circle inside the slider */
.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  border-radius: 50%;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
}

/* When the checkbox is checked, move the slider */
input:checked + .slider {
  background-color: black; /* Green */
}

/* When the checkbox is checked, move the circle */
input:checked + .slider:before {
  transform: translateX(26px);
}

.alert-info {
  background-color: #e7f3fe;
  border-left: 6px solid #2196F3;
  padding: 12px;
  margin-top: 10px;
  margin-bottom: 10px;
}

.alert-helper {
  background-color: #f9fbe7;
  border-left: 6px solid #cddc39;
  padding: 12px;
  margin-top: 10px;
  margin-bottom: 10px;
}

#lookupSpinnerText:hover{
  color:white !important;
}  

</style>
@endsection

@section('content')
<main class="main">
  <section class="section-box mt-0">
    <div class="">
      <div class="row m-0 profile-wrapper">
        <div class="col-lg-3 col-md-4 col-sm-12 p-0 left_menu">

        @include('nurse.sidebar_profile')
        </div>
        <div class="col-lg-9 col-md-8 col-sm-12 col-12 right_content">
          <div class="content-single content_profile">
            @if(!email_verified())
            <div class="container-fluid">
              <div class="alert alert-warning mt-2" role="alert">
                <span class="d-flex align-items-center justify-content-center "><img src="{{ asset('nurse/assets/imgs/info.png') }}" width="25px;" alt="info" class="mx-2"> Thank you for signing up with us. To get full access, please verify your email first. If you didn't receive the email, <a href="javascript:void(0);" class="link-opacity-100 mx-1" style="color: black;text-decoration-line: underline;
                  text-decoration-style: straight;" onclick="return resendEmailLink()"><b> click here to resend it.</b></a></span>
              </div>
            </div>
            @endif
            @if(!account_verified())
            <div class="container-fluid">
              <div class="alert alert-warning mt-2" role="alert">
                <span class="d-flex align-items-center justify-content-center "><img src="{{ asset('nurse/assets/imgs/info.png') }}" width="25px;" alt="info" class="mx-2">Thank you for verifying your email!<br>Please complete your profile, and once approved, you will be able to apply for jobs and make your profile visible.
                </span>
              </div>
            </div>
            @endif
            @if(!completeProfile())
            <div class="container-fluid">
              <div class="alert alert-warning mt-2" role="alert">
                <span class="d-flex align-items-center justify-content-center "><img src="{{ asset('nurse/assets/imgs/info.png') }}" width="25px;" alt="info" class="mx-2">Thank you for completing your profile.<br>We are currently reviewing your details and will get in touch with you shortly.
                </span>
              </div>
            </div>
            @endif
            @if(!approvedProfile())
            <div class="container-fluid">
              <div class="alert alert-warning mt-2" role="alert">
                <span class="d-flex align-items-center justify-content-center "><img src="{{ asset('nurse/assets/imgs/info.png') }}" width="25px;" alt="info" class="mx-2">Congratulations! Your profile has been successfully approved.<br>You can now apply for jobs, connect with employers, and receive interview requests.
                </span>
              </div>
            </div>
            @endif
            {{-- @if(!email_verified())
            <div class="alert alert-success mt-2" role="alert">
              <span class="d-flex align-items-center justify-content-center ">Please verify your email first to access your account </span>
            </div>
            @endif --}}

            <div class="tab-content">
                <?php $user_id=''; $i = 0;?>

                <div class="tab-pane fade" id="tab-my-profile-setting" style="display: block;opacity:1;">

                    
                    <div class="card shadow-sm border-0 p-4 mt-30">
                      <h3 class="mt-0 color-brand-1 mb-2">Registrations and Licences</h3>

                        {{-- Registration Countries Block --}}
                    <form class="mt-0" id="EditProfile" onsubmit="return editedCountryReg()" method="POST">
                       @csrf
                        <div class="col-12 mt-1">
                                <h5 class="mb-3">Registration & Qualification</h5>
                                @php
                                    $user = Auth::guard('nurse_middle')->user();

                                    $registrationCountries = (array) json_decode($user->registration_countries ?? '[]');
                                    $qualificationCountries = (array) json_decode(
                                        $user->qualification_countries ?? '[]'
                                    );
                                 
                                    $countries = country_name_from_db();
                                @endphp                    
                                <div class="form-group mb-4 drp--clr">
                                    <label class="font-sm mb-2">
                                        Countries of Registration<span class="text-danger">*</span>  (You must have at least one registration country.)
                                    </label>

                                    <input type="hidden"
                                        name="country_r"
                                        class="country_r"
                                        value='{{ json_encode($registrationCountries ?? []) }}'>

                                  <ul id="register_record" style="display:none;">
                                      @foreach($countries as $r_record)
                                          <li data-value="{{ $r_record->iso2 }}">
                                              {{ $r_record->name }}
                                          </li>
                                      @endforeach
                                  </ul>
                                  <select
                                      class="js-example-basic-multiple addAll_removeAll_btn"
                                      data-list-id="register_record"
                                      name="register_record[]"
                                      id="registerCountries"
                                      multiple>
                                  </select>
                                    <span id="reqempsdate" class="reqError text-danger valley"></span>
                                    <small class="text-muted">
                                        Select the countries where you are registered to practice.
                                    </small> 
                                </div>          
                                <div class="form-group drp--clr">
                                    <label class="font-sm mb-2">
                                        Countries of Qualification <span class="text-danger">*</span>
                                    </label>
                                    <input type="hidden"
                                          name="qualification_countries"
                                          id="qualificationCountriesInput"
                                          value="{{ json_encode($qualificationCountries) }}">

                                    <ul id="qualification-country-list" style="display:none;">
                                        @foreach ($countries as $country)
                                            <li
                                                data-value="{{ $country->iso2 }}"
                                                class="{{ in_array($country->iso2, $qualificationCountries) ? 'selected' : '' }}"
                                            >
                                                {{ $country->name }}
                                            </li>
                                        @endforeach
                                    </ul>

                                    <select
                                        class="js-example-basic-multiple addAll_removeAll_btn"
                                        data-list-id="qualification-country-list"
                                        id="qualificationCountries"
                                        multiple
                                    ></select>

                                    <small class="text-muted">
                                        We’ve prefilled this based on your registration countries.
                                        Update if your qualification was in a different country.
                                    </small>                           
                                </div>
                        </div>
                      <div id="registrationCardsContainer"></div>

                                      @php
                          $user = Auth::guard('nurse_middle')->user();
                           $registration_country = DB::table('registration_profiles_countries')->where('user_Id',$user->id)->where('country_code',$user->active_country)->first();
                           $profile_register_country = DB::table('registration_profiles_countries')->where('user_Id',$user->id)->get();
                        @endphp

                     @if($profile_register_country->isNotEmpty() && !empty($user->active_country) && !empty($registration_country))
                     <div class="mb-4 card registration-card registration-card-{{$registration_country->country_code }}" data-existing="1">
                          <h5 class="d-flex justify-content-between align-items-center">
                              <span>
                                  Registration & Licences — {{ country_name($registration_country->country_code) }}
                              </span>

                              <span class="badge badge-status badge-{{ $registration_country->status }}">
                                  {{ ucwords(str_replace('_',' ',$registration_country->status)) }}
                              </span>
                          </h5>
                          <p>Switch country to manage another registration.Move the tab just below My profile and above Setting. </p>
                          @if (in_array($registration_country->status, [1, 2, null], true))
                            <div class="form-group">
                                <label>Status</label>
                                <div class="d-flex gap-3">
                                    <label class="me-3">
                                        <input type="radio"
                                              name="registration[{{ $registration_country->id }}][status]"
                                              value="2"
                                              {{ $registration_country->status == 2 ? 'checked' : '' }}
                                              class="status-radio"
                                              data-code="{{ $registration_country->country_code }}"
                                              style="width:16px;height:16px;margin-right:6px">
                                        Draft
                                    </label>

                                    <label>
                                        <input type="radio"
                                              name="registration[{{ $registration_country->id }}][status]"
                                              value="3"
                                              {{ $registration_country->status == 3 ? 'checked' : '' }}
                                              class="status-radio"
                                              data-code="{{ $registration_country->country_code }}"
                                              style="width:16px;height:16px;margin-right:6px">
                                        Submitted (for Review)
                                    </label>
                                </div>
                            </div>
                          @else
                            <input type="hidden" name="registration[{{ $registration_country->id }}][status]" value="{{ $registration_country->status }}">
                          @endif
                          {{-- Mobile no --}}
                          <div class="form-group">
                              <label>Mobile Number</label>

                              <div class="iti iti--allow-dropdown iti--separate-dial-code w-100">
                                  <div class="iti__flag-container">
                                      <div class="iti__selected-flag" title="{{ strtoupper($registration_country->mobile_country_iso) }}">
                                          <div class="iti__flag iti__{{ strtolower($registration_country->mobile_country_iso) }}"></div>
                                          <div class="iti__selected-dial-code">
                                              +{{ $registration_country->mobile_country_code }}
                                          </div>
                                      </div>
                                  </div>

                                  <input type="text"
                                        class="form-control"  name="registration[{{ $registration_country->id }}][mobile_number]" 
                                        value="{{ $registration_country->mobile_number }}"
                                      >
                              </div>
                          </div>
                          {{-- Jurisdiction --}}
                          <div class="form-group">
                              <label>Jurisdiction / Registration Authority</label>
                              <input type="text"
                                    name="registration[{{ $registration_country->id }}][jurisdiction]"
                                    value="{{ $registration_country->registration_authority_name }}"
                                    class="form-control">
                          </div>

                          {{-- License Number --}}
                          <div class="form-group">
                              <label>License / Registration Number</label>
                              <input type="text"
                                    name="registration[{{ $registration_country->id }}][registration_number]"
                                    value="{{ $registration_country->registration_number }}"
                                    class="form-control">
                          </div>

                          {{-- Expiry Date --}}
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date"
                              name="registration[{{ $registration_country->id }}][expiry_date]"
                              value="{{ \Carbon\Carbon::parse($registration_country->expiry_date)->format('Y-m-d') }}"
                              {{-- min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" --}}
                              class="form-control">
                        </div>


            
                        <div class="form-group">
                            <label>Upload Evidence</label>

               
                            <input type="hidden"
                                  name="registration[{{ $registration_country->id }}][upload_evidence]"
                                  class="registration_evidence_input-{{ $registration_country->id }}"
                                  value='{{ $registration_country->upload_evidence }}'>

                            <input type="file"
                                  class="form-control"
                                  multiple
                                  onchange="uploadRegistrationEvidence({{ $registration_country->id }})">

                            <span class="text-danger error-upload-{{ $registration_country->id }}"></span>

                          <div class="mt-2 registration-evidence-preview-{{ $registration_country->id }}">
                              @if(!empty($registration_country->upload_evidence))
                                  @foreach(json_decode($registration_country->upload_evidence, true) as $file)
                                      <div class="trans_img">
                                        <div>
                                          <i class="fa fa-file"></i> 
                                          <a href="{{ url('/public/uploads/registration/' . $file) }}" target="_blank">
                                              {{ preg_replace('/^\d+_\d+_/', '', $file) }}
                                          </a>
                                          <span class="close_btn"
                                                onclick="removeRegistrationEvidence('{{ $file }}', {{ $registration_country->id }})">
                                              <i class="fa fa-close"></i>
                                          </span>
                                        </div>
                                      </div>
                                  @endforeach
                              @endif
                          </div>

                        </div>

                      </div>
                     @endif
                    <button class="btn btn-apply-big font-md font-bold" @if(!email_verified()) disabled @endif type="submit" id="submitfrm">Save Changes</button>
                  </form>


                      <form id="register_licenses_form" method="POST" onsubmit="return update_register_licenses()">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::guard('nurse_middle')->user()->id }}">
                        <div class="form-group level-drp">
                          <label class="form-label" for="input-1">What is your current AHPRA registration status?</label>
                          <select id="registration-status" name="ahpra_registration_status" class="form-control">
                            <option value="">-- Select Registration Status --</option>
                            <option value="EN" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "EN") selected @endif>Enrolled Nurse (EN)</option>
                            <option value="EN-ME" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "EN-ME") selected @endif>Enrolled Nurse – Medication Endorsed (EN-ME)</option>
                            <option value="ENN" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "ENN") selected @endif>Enrolled Nurse (with Notation)</option>
                            <option value="RN" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "RN") selected @endif>Registered Nurse (RN)</option>
                            <option value="RM" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "RM") selected @endif>Registered Midwife (RM)</option>
                            <option value="RN_RM" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "RN_RM") selected @endif>Registered Nurse and Midwife (RN/RM)</option>
                            <option value="NP" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "NP") selected @endif>Nurse Practitioner (NP) (as endorsed under RN)</option>
                            <option value="Graduate_RN" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Graduate_RN") selected @endif>Graduate Nurse – Transitional Authorisation</option>
                            <option value="Graduate_RM" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Graduate_RM") selected @endif>Graduate Midwife – Transitional Authorisation</option>
                            <option value="Student_Nurse" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Student_Nurse") selected @endif>Student Nurse – AHPRA-registered (NMBA-approved course)</option>
                            <option value="Student_Midwife" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Student_Midwife") selected @endif>Student Midwife – AHPRA-registered (NMBA-approved course)</option>
                            <option value="Overseas" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Overseas") selected @endif>Overseas-Qualified Nurses and Midwives not currently registered with AHPRA</option>
                            <option value="Not_Registered" @if(!empty($licenses_data) && $licenses_data->ahpra_registration_status == "Not_Registered") selected @endif>Not currently registered with AHPRA</option>
                          </select>
                          <span id="register_status" class="reqError text-danger valley"></span>
                        </div>
                        <!-- Conditional AHPRA Input Group -->
                        
                        <div id="ahpra-details-group" style="display: none;">
                          <div class="form-group level-drp" id="ahpra-number">
                            <!-- AHPRA Number -->
                            <label for="ahpra-number"><strong>Please Enter your AHPRA Registration Number:</strong></label>
                            <input class="form-control ahpra_number" type="text" name="ahpra_number"
                                  placeholder="e.g. NMW0001234567" value="@if(!empty($licenses_data)){{ $licenses_data->aphra_registration_no }}@endif"/>
                            <small style="color: gray;">Format: NMW followed by 10 digits (e.g., NMW0001234567)</small>
                            <div class="group_one_aphrano">
                              <span id="group_one_aphrano" class="reqError text-danger valley"></span>
                            </div>
                            
                          </div>  
                          <!-- Consent Checkbox -->
                          <div class="declaration_box">
                           
                              <input type="checkbox" name="ahpra_consent" class="declare_information" id="ahpra-consent" @if(!empty($licenses_data) && $licenses_data->aphra_verifying_checkbox == "1") checked @endif/>
                              
                            <label for="declare_information">I consent to Mediqa verifying my AHPRA registration via the public AHPRA register.</label>
                            
                            
                          </div>
                          <span id="aphra_checkbox" class="reqError text-danger valley"></span>
                          <div class="add_new_certification_div mb-3 mt-3">
                            
                            <a style="cursor: pointer;" class="lookup-ahpra-btn">
                              <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="lookupSpinner"></span>
                              <span id="lookupSpinnerText">Lookup AHPRA Registration</span>
                            </a>
                            
                          </div>
                          

                          
                          <span id="reqaphra_reg" class="reqError text-danger valley"></span>

                          <div style="margin-top: 15px;" class="mb-5 manual_entry_div">
                            <p style="margin-bottom: 10px;color: black;">
                              <strong>Can’t find your AHPRA registration? Please enter your details manually below. </strong><br>
                              
                            </p>
                            <!-- <button type="button" id="manualEntryBtn" class="btn btn-outline-dark">
                              Enter AHPRA Details Manually
                            </button> -->
                          </div>
                          <div class="ahpra-lookup">
                            <input type="hidden" name="api_division" class="api_division" value="@if(!empty($licenses_data)){{ $licenses_data->register_division }}@endif">
                            <input type="hidden" name="api_endorsements" class="api_endorsements" value="@if(!empty($licenses_data)){{ $licenses_data->register_endorsements }}@endif">
                            <input type="hidden" name="api_reg_type" class="api_reg_type" value="@if(!empty($licenses_data)){{ $licenses_data->register_reg_type }}@endif">
                            <input type="hidden" name="api_reg_status" class="api_reg_status" value="@if(!empty($licenses_data)){{ $licenses_data->register_reg_status }}@endif">
                            <input type="hidden" name="api_notations" class="api_notations" value="@if(!empty($licenses_data)){{ $licenses_data->register_notations }}@endif">
                            <input type="hidden" name="api_conditions" class="api_conditions" value="@if(!empty($licenses_data)){{ $licenses_data->register_conditions }}@endif">
                            <input type="hidden" name="api_expiry" class="api_expiry" value="@if(!empty($licenses_data)){{ $licenses_data->register_expiry }}@endif">
                            <input type="hidden" name="api_principal_practice" class="api_principal_practice" value="@if(!empty($licenses_data)){{ $licenses_data->register_principal_place }}@endif">
                            <input type="hidden" name="api_other_practices" class="api_other_practices" value="@if(!empty($licenses_data)){{ $licenses_data->register_other_place }}@endif">
                            
                            <div id="ahpra-lookup-result" style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;display: none;">
                              <h6>AHPRA Registration Details</h6>
                              <p id="successful_ahpra" style="display: none;">Your AHPRA registration is verified successfully, please review the retrieved data below.</p>
                              {{-- <div><strong>Division:</strong> <span id="division">@if(!empty($licenses_data)){{ $licenses_data->register_division }}@endif</span></div>
                              <div><strong>Endorsements:</strong> <span id="endorsements">@if(!empty($licenses_data)){{ $licenses_data->register_endorsements }}@endif</span></div>
                              <div><strong>Registration Type:</strong> <span id="reg_type">@if(!empty($licenses_data)){{ $licenses_data->register_reg_type }}@endif</span></div>
                              <div><strong>Registration Status:</strong> <span id="reg_status">@if(!empty($licenses_data)){{ $licenses_data->register_reg_status }}@endif</span></div>
                              <div><strong>Notations:</strong> <span id="notations">@if(!empty($licenses_data)){{ $licenses_data->register_notations }}@endif</span></div>
                              <div><strong>Conditions:</strong> <span id="conditions">@if(!empty($licenses_data)){{ $licenses_data->register_conditions }}@endif</span></div>
                              <div><strong>Expiry:</strong> <span id="expiry"></span>@if(!empty($licenses_data)){{ $licenses_data->register_expiry }}@endif</div>
                              <div><strong>Principal Place of Practice:</strong> <span id="principal_practice">@if(!empty($licenses_data)){{ $licenses_data->register_principal_place }}@endif</span></div>
                              <div><strong>Other Places of Practice:</strong> <span id="other_practices">@if(!empty($licenses_data)){{ $licenses_data->register_other_place }}@endif</span></div> --}}

                              <!-- Confirmation of Source -->

                              <table class="table table-bordered table-striped mt-3">
                                  <tbody>
                                    <tr>
                                      <th><strong>Division</strong></th>
                                      <td id="division">@if(!empty($licenses_data)){{ $licenses_data->register_division }}@endif</td>
                                    </tr>
                                    <tr>
                                      <th><strong>Endorsements</strong></th>
                                      <td id="endorsements">@if(!empty($licenses_data)){{ $licenses_data->register_endorsements }}@endif</td>
                                    </tr>
                                    <tr>
                                      <th><st