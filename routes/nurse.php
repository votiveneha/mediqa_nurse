<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\nurse\HomeController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|ererer
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// ===========
// User Route
// ===========
Route::get('/clear-route-cache', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear'); // optional, also clears application cache
    Artisan::call('view:clear');
    return "Route cache cleared!";
});
Route::post('/fetch-provinces', 'App\Http\Controllers\HomeController@fetchProvinces')->name('fetch-provinces');
Route::get('/', 'App\Http\Controllers\nurse\HomeController@index_main')->name('home_main');
Route::get('/term-and-condition', 'App\Http\Controllers\nurse\HomeController@term_and_condition')->name('term-and-condition');
Route::get('/contact', 'App\Http\Controllers\nurse\HomeController@contact')->name('contact');
Route::get('/about', 'App\Http\Controllers\nurse\HomeController@about')->name('about');
Route::get('/privacy', 'App\Http\Controllers\nurse\HomeController@privacy')->name('privacy');
Route::post('/save-contact', 'App\Http\Controllers\HomeController@saveContact')->name('save-contact');
Route::post('/getNurseTypeJobs', 'App\Http\Controllers\HomeController@getNurseTypeJobs')->name('getNurseTypeJobs');
Route::get('/nurseCareHome', 'App\Http\Controllers\HomeController@nurseCareHome')->name('nurseCareHome');
Route::post('/getSubSpecialties', 'App\Http\Controllers\HomeController@getSubSpecialties')->name('getSubSpecialties');
Route::post('/getNursepractitionorSpecialities', 'App\Http\Controllers\HomeController@getNurseSpecialties')->name('getNursepractitionorSpecialities');
Route::post('/getsurgicalSpeciality', 'App\Http\Controllers\HomeController@getsurgicalSpeciality')->name('getsurgicalSpeciality');
Route::post('/getsurgicalSubSpeciality', 'App\Http\Controllers\HomeController@getsurgicalSubSpeciality')->name('getsurgicalSubSpeciality');

Route::prefix('healthcare-facilities')->name('medical-facilities.')->namespace('App\Http\Controllers\medical_facilities')->group(function () {
  Route::get('/', 'HomeController@index_main')->name('medical_facilities_home_main');
  Route::get('/medical-facilities-registraion', 'HomeController@registraion')->name('medical-facilities-registraion');
  Route::post('/healthcareRegistration', 'HomeController@healthcareRegistration')->name('healthcareRegistration');
  Route::get('/login', 'HomeController@login')->name('login');
  Route::post('/userloginAction', 'HomeController@userloginAction')->name('userloginAction');
  Route::middleware('nurse_middle')->group(function () {});
});

Route::prefix('agencies')->name('agencies.')->namespace('App\Http\Controllers\agencies')->group(function () {
  Route::get('/', 'HomeController@index_main')->name('agencies_home_main');
  Route::get('/agencies-registraion', 'HomeController@registraion')->name('agencies-registraion');
  Route::get('/login', 'HomeController@login')->name('login');

  Route::middleware('nurse_middle')->group(function () {});
});

Route::prefix('individuals')->name('individuals.')->namespace('App\Http\Controllers\individuals')->group(function () {
  // Route::get('/', 'HomeController@index_main')->name('agencies_home_main');
  Route::get('/individuals_registraion', 'HomeController@registraion')->name('individuals_registraion');
  Route::get('/login', 'HomeController@login')->name('login');

  //Route::middleware('nurse_middle')->group(function () {});
});
Route::prefix('cpd_providers')->name('cpd_providers.')->namespace('App\Http\Controllers\cpd_providers')->group(function () {
  // Route::get('/', 'HomeController@index_main')->name('agencies_home_main');
  Route::get('/cpd_providers-registraion', 'HomeController@registraion')->name('cpd_providers-registraion');
  Route::get('/login', 'HomeController@login')->name('login');

  //Route::middleware('nurse_middle')->group(function () {});
});

Route::prefix('nurse')->name('nurse.')->namespace('App\Http\Controllers\nurse')->group(function () {
  Route::get('/getNurseDatas', 'HomeController@getNurseType')->name('getNurseType');
  Route::get('getSpecialityDatas1', 'HomeController@getSpecialityDatas')->name('getSpecialityDatas1');
  Route::get('/forgot-password', 'HomeController@forgotPassword')->name('forgot-password');
  Route::post('/forgot-password', 'HomeController@SendResetPasswordLink')->name('send-reset-password-link');
  Route::post('/addnewsletter', 'HomeController@addnewsletters')->name('addnewsletter');
  Route::post('/contact_us_data', 'HomeController@contact_us_data')->name('contact_us_data');

  Route::get('/reset-password/{token}/{lp}', 'HomeController@ResetPassword')->name('reset-password');
  Route::post('/reset-password', 'HomeController@UpdatePassword')->name('update-password');

  Route::post('/userloginAction', 'HomeController@userloginAction')->name('userloginAction');
  Route::post('/mail-exist', 'HomeController@mail_exist')->name('mail-exist');

  Route::get('/fetch-subspecialty', 'HomeController@fetchSubspecialty')->name('fetch-subspecialty');
  Route::get('/logout', 'HomeController@logout')->name('logout');
  Route::get('/login', 'HomeController@login')->name('login');

  Route::get('/', 'HomeController@index')->name('home');

  Route::get('/nurse-register', 'HomeController@nurse_register')->name('nurse-register');
  Route::post('/fetch-ahpra-details', 'AhpraLookupsController@getAhpraDetails')->name('getAhpraDetails');
  Route::get('/email-verification-pending', 'HomeController@emailVerificationPending')->name('email-verification-pending');
  Route::get('/resent-verification', 'HomeController@resentVerification')->name('resent-verification-link');
  Route::get('/email-verification/{token}', 'HomeController@email_verification')->name('email-verification');
  Route::post('/do-nurse-register', 'HomeController@do_nurse_register')->name('do-nurse-register');
  Route::get('/profile-under-reviewed', 'HomeController@profileUnderReviewed')->name('profile-under-reviewed');
  Route::middleware('nurse_middle')->group(function () {
    Route::get('/my-profile', 'HomeController@manage_profile')->name('my-profile');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    Route::post('/changepassword', 'HomeController@changepassword')->name('changepassword');
    Route::post('/updateProfile', 'HomeController@updateProfile')->name('updateProfile');
    Route::post('/user-upload-image', 'HomeController@upload_profile_image')->name('user-upload-image');
    Route::post('/change_password', 'HomeController@change_password')->name('change_password');
    Route::post('/update-profession', 'HomeController@update_profession')->name('update-profession');
    Route::post('/update-profession-user-ahpra_numberI', 'HomeController@update_profession_ahpra_numberI')->name('update-profession-user-ahpra_numberI');
    Route::post('/user/active-country', [HomeController::class, 'updateActiveCountry'])->name('active-country');
    Route::post('/upload-registration-evidence', 'HomeController@uploadRegistrationEvidence')->name('nurse.uploadRegistrationEvidenceI');
    Route::post('/remove-registration-evidence', 'HomeController@removeRegistrationEvidence')->name('nurse.removeRegistrationEvidence');
    Route::post('/remove-registration-country', 'HomeController@remove_registration_country')->name('nurse.remove-registration-country');
    Route::post('/remove-qualification-country', 'HomeController@remove_qualification_country')->name('nurse.remove-qualification-country');
    Route::post('/nurse/save-registration-country', function (Request $request) {
      auth('nurse_middle')->user()->update([
        'active_country' => $request->country_id,
        'country' => $request->country_id,
        'country_code' => $request->country_code,
      ]);

      return response()->json(['success' => true]);
    })->name('saveRegistrationCountry');
    
    Route::post('/update-profession-user-emergency', 'HomeController@update_emergency')->name('update-profession-user-emergency');
    Route::post('/update-profession-profile-setting', 'HomeController@update_profession_profile_setting')->name('update-profession-profile-setting');
    Route::post('/updateProfession', 'HomeController@updateProfession')->name('updateProfession');
    Route::post('/updateEducation', 'HomeController@updateEducation')->name('updateEducation');
    Route::post('/updateExperience', 'HomeController@updateExperience')->name('updateExperience');
    Route::post('/uploadExpImgs', 'HomeController@uploadExpImgs')->name('uploadExpImgs');
    Route::get('/getEmployeePositions', 'HomeController@getEmployeePositions')->name('getEmployeePositions');
    Route::post('/uploadAnotherImgs_cert', 'HomeController@uploadAnotherImgs_cert')->name('uploadAnotherImgs_cert');
    Route::get('/getWorkplaceData', 'HomeController@getWorkplaceData')->name('getWorkplaceData');
    Route::get('/getSubWorkplaceData', 'HomeController@getSubWorkplaceData')->name('getSubWorkplaceData');
    Route::post('/updateTraining', 'HomeController@updateTraining')->name('updateTraining');
    Route::post('/updateInterview', 'HomeController@updateInterview')->name('updateInterview');
    Route::post('/updatePreferences', 'HomeController@updatePreferences')->name('updatePreferences');
    Route::post('/updateWorkPreference', 'HomeController@updateWorkPreference')->name('updateWorkPreference');
    
    Route::post('/updateAdditionalInfo', 'HomeController@updateAdditionalInfo')->name('updateAdditionalInfo');
    Route::post('/updateReference', 'HomeController@updateReference')->name('updateReference');
    
    Route::post('/deleteReferee', 'HomeController@deleteReferee')->name('deleteReferee');
    Route::post('/deleteCertification', 'HomeController@deleteCertification')->name('deleteCertification');
    Route::post('/deleteOtherTraining', 'HomeController@deleteOtherTraining')->name('deleteOtherTraining');
    Route::post('/deleteOtherEducation', 'HomeController@deleteOtherEducation')->name('deleteOtherEducation');
    Route::post('/deleteWorkExperience', 'HomeController@deleteWorkExperience')->name('deleteWorkExperience');
    Route::post('/deleteImg', 'HomeController@deleteImg')->name('deleteImg');
    Route::post('/deleteImg1', 'HomeController@deleteImg1')->name('deleteImg1');
    Route::post('/deleteImgCert', 'HomeController@deleteImgCert')->name('deleteImgCert');
    Route::post('/deleteanoImgcert', 'HomeController@deleteanoImgcert')->name('deleteanoImgcert');
    Route::post('/uploadImgs', 'HomeController@uploadImgs')->name('uploadImgs');
    Route::post('/uploadImgs1', 'HomeController@uploadImgs1')->name('uploadImgs1');
    Route::post('/uploadmantraImgs1', 'HomeController@uploadmantraImgs1')->name('uploadmantraImgs1');
    Route::post('/uploadAnotherImgs', 'HomeController@uploadAnotherImgs')->name('uploadAnotherImgs');
    Route::post('/deleteTraining', 'HomeController@deleteTraining')->name('deleteTraining');
    Route::post('/deleteAnoImg1', 'HomeController@deleteAnoImg1')->name('deleteAnoImg1');
    Route::post('/deleteImg12', 'HomeController@deleteImg12')->name('deleteImg12');
    Route::post('/deleteotherImg', 'HomeController@deleteotherImg')->name('deleteotherImg');
    Route::post('/deletecertification_img', 'HomeController@deletecertification_img')->name('deletecertification_img');
    Route::post('/getSkillsData', 'HomeController@getSkillsData')->name('getSkillsData');
	  Route::post('/deleteevidence', 'HomeController@deleteEvidence')->name('deleteEvidence');
	
	/**************[Profile Vaccination]**************/
	Route::post('/vaccinationForm', 'HomeController@vaccinationForm')->name('vaccinationForm');
    Route::any('/profileVaccination', 'HomeController@profileVaccination')->name('profileVaccination');
    Route::post('/removeVaccine', 'HomeController@removeVaccine')->name('removeVaccine');
    Route::post('/getContent', 'HomeController@getContent')->name('getContent');
    Route::any('/getVaccinationData', 'HomeController@getVaccinationData')->name('getVaccinationData');
    Route::any('/removeEvidanceFile','HomeController@removeEvidanceFile')->name('removeEvidanceFile');
    Route::any('/removeEvidance','HomeController@removeEvidance')->name('removeEvidance');

    /**************[Registeration & Licences]**************/
  Route::get('/registration_licences', 'LicencesContoller@registration_licences')->name('registration_licences');
  Route::post('/ahepra_lookup', 'LicencesContoller@ahepra_lookup')->name('ahepra_lookup');
  Route::post('/update_registration_licenses', 'LicencesContoller@update_registration_licenses')->name('update_registration_licenses');
  Route::post('/uploadLicensesEvidenceImgs', 'LicencesContoller@uploadLicensesEvidenceImgs')->name('uploadLicensesEvidenceImgs');
  Route::post('/deleteLicensesEvidenceImg', 'LicencesContoller@deleteLicensesEvidenceImg')->name('deleteLicensesEvidenceImg');
	Route::get('/ahpra-lookup/{number}', 'AhpraLookupsController@lookup1')->name('lookup1');
  
	/**************[Work Clearance]**************/
  Route::any('/workClearances','ProfessionalController@workClearances')->name('workClearances');
  Route::post('/update-profession-user-eligibility', 'ProfessionalController@update_eligibility_to_work')->name('update-profession-user-eligibility');
  Route::post('/update-ndis', 'ProfessionalController@updateNdis')->name('update-ndis');
  Route::post('/update-profession-user-children', 'ProfessionalController@update_children_to_work')->name('update-profession-user-children');
  Route::post('/removeWwcc', 'ProfessionalController@removeWwcc')->name('removeWwcc');
  Route::post('/update-profession-user-police-check', 'ProfessionalController@update_police_check_to_work')->name('update-profession-user-police-check');
  Route::post('/updateSpecializedClearance', 'ProfessionalController@updateSpecializedClearance')->name('updateSpecializedClearance');
  Route::post('/removeSpecialized', 'ProfessionalController@removeSpecialized')->name('removeSpecialized');

  Route::any('/removeEligibilityFile','ProfessionalController@removeEligibilityFile')->name('removeEligibilityFile');
  Route::any('/removendisFile','ProfessionalController@removendisFile')->name('removendisFile');
  Route::any('/removewwccFile','ProfessionalController@removewwccFile')->name('removewwccFile');
  Route::any('/removePolicyFile','ProfessionalController@removePolicyFile')->name('removePolicyFile');
  Route::any('/removeSpecializedFile','ProfessionalController@removeSpecializedFile')->name('removeSpecializedFile');

  /**************[Setting & Availability]**************/
  Route::get('/setting_availablity', 'HomeController@setting_availablity')->name('setting_availablity');
  Route::post('/update-profession-profile-setting', 'HomeController@update_profession_profile_setting')->name('update-profession-profile-setting');
  
  /**************[Mandatory Training]**************/
  Route::get('/mandatory_training', 'MandatortrainingController@mandatory_training')->name('mandatory_training');
  Route::post('/uploadTrainingEvidenceImgs', 'MandatortrainingController@uploadTrainingEvidenceImgs')->name('uploadTrainingEvidenceImgs');
  Route::get('/getMandatoryCourses', 'MandatortrainingController@getMandatoryCourses')->name('getMandatoryCourses');
  Route::get('/getMandatoryCoursesName', 'MandatortrainingController@getMandatoryCoursesName')->name('getMandatoryCoursesName');
  Route::post('/deleteTrainingEvidenceImg', 'MandatortrainingController@deleteTrainingEvidenceImg')->name('deleteTrainingEvidenceImg');
  Route::post('/updateMandatoryTraining', 'MandatortrainingController@updateMandatoryTraining')->name('updateMandatoryTraining');
  
  /**************[Professional Membership]**************/
  Route::any('/professionalMembership','ProfessionalController@professionalMembership')->name('professionalMembership');
  Route::any('/getCountryOrgnizations','ProfessionalController@getCountryOrgnizations')->name('getCountryOrgnizations');
  Route::any('/getCountrySubOrgnizations','ProfessionalController@getCountrySubOrgnizations')->name('getCountrySubOrgnizations');
  Route::any('/getMembershipData','ProfessionalController@getMembershipData')->name('getMembershipData');
  Route::any('/getSubMembershipData','ProfessionalController@getSubMembershipData')->name('getSubMembershipData');
  Route::any('/getawardsRecognitions','ProfessionalController@getawardsRecognitions')->name('getawardsRecognitions');
  Route::post('/updateProfessionalMembership', 'ProfessionalController@updateProfessionalMembership')->name('updateProfessionalMembership');
  Route::post('/uploadMembershipImgs', 'ProfessionalController@uploadMembershipImgs')->name('uploadMembershipImgs');
  Route::post('/uploadAwardImgs', 'ProfessionalController@uploadAwardImgs')->name('uploadAwardImgs');
  Route::post('/deleteEvidenceImg', 'ProfessionalController@deleteEvidenceImg')->name('deleteEvidenceImg');
  Route::post('/deleteAwardEvidenceImg', 'ProfessionalController@deleteAwardEvidenceImg')->name('deleteAwardEvidenceImg');
  
  /**************[Language Skills]**************/
  Route::get('/language_skills', 'LanguageSkillsContoller@index')->name('language_skills');
  Route::get('/getLanguagesData', 'LanguageSkillsContoller@getLanguagesData')->name('getLanguagesData');
  Route::get('/getSubLanguagesData', 'LanguageSkillsContoller@getSubLanguagesData')->name('getSubLanguagesData');
  Route::get('/getTestLanguagesData', 'LanguageSkillsContoller@getTestLanguagesData')->name('getTestLanguagesData');
  Route::post('/updateLanguageSkills', 'LanguageSkillsContoller@updateLanguageSkills')->name('updateLanguageSkills');
  Route::post('/uploadlangEvidenceImgs', 'LanguageSkillsContoller@uploadlangEvidenceImgs')->name('uploadlangEvidenceImgs');
  Route::post('/deletelangEvidenceImg', 'LanguageSkillsContoller@deletelangEvidenceImg')->name('deletelangEvidenceImg');   
  
  /**************[Work Preferences & Flexibility]**************/
  Route::get('/match_percentage', 'MatchController@match_percentage')->name('match_percentage');
  Route::get('/sector_preferences', 'WorkPreferencesController@index')->name('sector_preferences');
  Route::post('/updateSectorPreferences', 'WorkPreferencesController@updateSectorPreferences')->name('updateSectorPreferences');
  Route::get('/work_environment_preferences', 'WorkPreferencesController@work_environment_preferences')->name('work_environment_preferences');
  Route::post('/updateWorkPreferences', 'WorkPreferencesController@updateWorkPreferences')->name('updateWorkPreferences');
  Route::get('/employeement_type_preferences', 'WorkPreferencesController@employeement_type_preferences')->name('employeement_type_preferences');
  Route::get('/getEmpData', 'WorkPreferencesController@getEmpData')->name('getEmpData');
  Route::get('/getEmpDataExp', 'HomeController@getEmpData')->name('getEmpDataExp');
  Route::post('/updateEmpTypePreferences', 'WorkPreferencesController@updateEmpTypePreferences')->name('updateEmpTypePreferences');
  Route::get('/WorkShiftPreferences', 'WorkPreferencesController@WorkShiftPreferences')->name('WorkShiftPreferences');
  Route::post('/updateShiftPreferences', 'WorkPreferencesController@updateShiftPreferences')->name('updateShiftPreferences');
  Route::get('/getSubWorkData', 'WorkPreferencesController@getSubWorkData')->name('getSubWorkData');
  Route::get('/position_preferences', 'WorkPreferencesController@position_preferences')->name('position_preferences');
  Route::post('/updatePositionPreferences', 'WorkPreferencesController@updatePositionPreferences')->name('updatePositionPreferences');
  Route::get('/benefitsPreferences', 'WorkPreferencesController@benefitsPreferences')->name('benefitsPreferences');
  Route::post('/updateBenefitsPreferences', 'WorkPreferencesController@updateBenefitsPreferences')->name('updateBenefitsPreferences');
  Route::get('/locationPreferences', 'WorkPreferencesController@locationPreferences')->name('locationPreferences');
  Route::post('/updateLocationPreferences', 'WorkPreferencesController@updateLocationPreferences')->name('updateLocationPreferences');
  Route::get('/salaryExpectations', 'WorkPreferencesController@salaryExpectations')->name('salaryExpectations');
  Route::post('/updatesalaryExpectations', 'WorkPreferencesController@updatesalaryExpectations')->name('updatesalaryExpectations');
  
  /**************[Interview Preferences]**************/
  Route::any('/interview','ProfessionalController@interview')->name('interview');
  
  /**************[Find Jobs]**************/
  Route::get('/find_jobs', 'JobsController@index')->name('find_jobs');
  Route::post('/getWorkFlexiblityData', 'JobsController@getWorkFlexiblityData')->name('getWorkFlexiblityData');  
  Route::post('/getWorkEnvironmentData', 'JobsController@getWorkEnvironmentData')->name('getWorkEnvironmentData');  
  Route::post('/getNurseData', 'JobsController@getNurseData')->name('getNurseData'); 
  Route::post('/getSpecialityData', 'JobsController@getSpecialityData')->name('getSpecialityData'); 
  Route::post('/getFilterData', 'JobsController@getFilterData')->name('getFilterData');  
  Route::post('/getExperienceData', 'JobsController@getExperienceData')->name('getExperienceData');  
  Route::post('/getFilterNurseData', 'JobsController@getFilterNurseData')->name('getFilterNurseData');
  Route::post('/getFilterSpecialityData', 'JobsController@getFilterSpecialityData')->name('getFilterSpecialityData');  
  Route::post('/updateSectorData', 'JobsController@updateSectorData')->name('updateSectorData');
  Route::post('/getJobsSorting', 'JobsController@getJobsSorting')->name('getJobsSorting');
  Route::post('/applyJobs', 'JobsController@applyJobs')->name('applyJobs');
  Route::post('/addSavedSearches', 'JobsController@addSavedSearches')->name('addSavedSearches');
  Route::post('/deleteSearchJobsData', 'JobsController@deleteSearchJobsData')->name('deleteSearchJobsData');
  Route::post('/duplicateSearch', 'JobsController@duplicateSearch')->name('duplicateSearch');
  Route::get('/getEditSearchData', 'JobsController@getEditSearchData')->name('getEditSearchData');
  Route::post('deleteMultipleSearches', 'JobsController@deleteMultipleSearches')->name('deleteMultipleSearches');
  Route::get('/getEmpDataSearch', 'JobsController@getEmpDataSearch')->name('getEmpDataSearch');
  Route::get('/getSavedSearch', 'JobsController@getSavedSearch')->name('getSavedSearch');
  Route::post('remove-filter/{id}', 'JobsController@removeFilter');
  Route::post('updateAlert/{id}', 'JobsController@updateAlert');
  Route::post('run-saved-search/{id}', 'JobsController@run')->name('run-saved-search');
  Route::post('get_tags', 'JobsController@get_tags')->name('get_tags');
  Route::post('get_filters_data', 'JobsController@get_filters_data')->name('get_filters_data');
  Route::get('getEmptypeData', 'JobsController@getEmptypeData')->name('getEmptypeData');
  Route::get('matchedJobs', 'MatchController@matchedJobs')->name('matchedJobs');
  Route::get('getSpecialityDatas', 'WorkPreferencesController@getSpecialityDatas')->name('getSpecialityDatas');
  
  });
});