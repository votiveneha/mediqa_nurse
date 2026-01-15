<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateProfile extends FormRequest
{
    public function rules()
    {
        return [
            'fullname' => 'required', 'min:3',
            'lastname' => 'required',            
            'post_code' => 'required',
            'date_of_birth' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:today', // not in the future 
                'before_or_equal:' . now()->subYears(10)->toDateString(), // at least 10 years old 
            ],
            // 'registration_countries' => ['required', function ($attr, $value, $fail) {
            //     $data = json_decode($value, true);
            //     if (!is_array($data) || count($data) < 1) {
            //         $fail('Please select at least one registration country.');
            //     }
            // }],

            // Conditional: required only if status == 3 
            'registration.*.mobile_number' => ['required_if:registration.*.status,3'],
            'registration.*.jurisdiction' => ['required_if:registration.*.status,3'],
            'registration.*.registration_number' => ['required_if:registration.*.status,3'],
            'registration.*.expiry_date' => ['after:today'],
            'registration.*.upload_evidence' => ['required_if:registration.*.status,3', 'min:1'],
            'gender' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => 'The First name field is required.',
            'lastname.required' => 'The Last name field is required.',
            'post_code.required' => 'The Post_code field is required.',
            'date_of_birth.required' => 'The Date of Birth field is required.',
            'date_of_birth.before_or_equal' => 'Date of Birth cannot be in the future and must be at least 10 years old.',
            'country.required' => 'Please Select Country.',
            'state.required' => 'Please Select State.',
            'city.required' => 'The City field is required.',
            // 'registration_countries.required' => 'Please select at least one registration country.',
            'registration.*.mobile_number.required_if' => 'Mobile number is required when submit for review.',
            'registration.*.jurisdiction.required_if' => 'Jurisdiction is required when submit for review.',
            'registration.*.registration_number.required_if' => 'Registration number is required when submit for review.',
            'registration.*.expiry_date.after' => 'Expiry date must be a future date.',
            'registration.*.upload_evidence.required_if' => 'Evidence upload is required when submit for review.',
            'gender.required' => 'The Gender is required',
           
            // 'email.required' => 'The email field is required.',
        ];
    }
}
