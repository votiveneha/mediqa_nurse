<?php

namespace App\Services\User;

class NurseJobMatchService
{

    public function calculateMatch($nurseTypes, $nurseSpecialties, $experience_data, $nurseVaccines, $eligibility, $policeCheck, $workingChildren, $ndisCheck, $preferences, $job)
    {
        $score = 0;

        $score += $this->typeScore($nurseTypes, $job);
        $score += $this->specialtyScore($nurseSpecialties, $job);
        $score += $this->experienceScore($experience_data, $job);
        $score += $this->vaccinationScore($nurseVaccines, $job);
        $score += $this->checksClearanceScore(
            $eligibility,
            $policeCheck,
            $workingChildren,
            $ndisCheck,
            $job
        );
        $score += $this->workPreferenceScore($preferences, $job);

        return round($score, 2);
    }

    
    private function workPreferenceScore($preferences, $job)
    {
        $score = 0;

        $score += $this->sectorScore($preferences, $job);
        $score += $this->shiftScore($preferences, $job);
        $score += $this->employmentScore($preferences, $job);
        $score += $this->benefitsScore($preferences, $job);
        $score += $this->locationSalaryScore($preferences, $job);

        return ($score / 25) * 30; // ✅ scaled to 30%
    }
    
    private function sectorScore($preferences, $job)
    {
        $weight = 5;

        if (empty($preferences->sector_preferences) || empty($job->sector)) {
            return 0;
        }

        $userSector = strtolower($preferences->sector_preferences); // public/private
        $jobSector = $job->sector; // 1,2,3

        // Example mapping (adjust if needed)
        $map = [
            'public' => [1],
            'private' => [2],
            'both' => [1, 2, 3]
        ];

        if (isset($map[$userSector]) && in_array($jobSector, $map[$userSector])) {
            return $weight;
        }

        return 0;
    }

    function flattenArray($array)
    {
        $result = [];

        array_walk_recursive($array, function ($item) use (&$result) {
            $result[] = $item;
        });

        return $result;
    }
    private function shiftScore($preferences, $job)
    {
        $weight = 5;

        // Handle user data
        $user = $preferences->work_shift_preferences;
        if (is_string($user)) {
            $user = json_decode($user, true);
        }

        // Handle job data
        $jobShift = $job->shift_type;
        if (is_string($jobShift)) {
            $jobShift = json_decode($jobShift, true);
        }

        if (empty($user) || empty($jobShift)) {
            return 0;
        }

        $userFlat = $this->flattenArray($user);

        return count(array_intersect($userFlat, $jobShift)) > 0 ? $weight : 0;
    }
    private function employmentScore($preferences, $job)
    {
        $weight = 5;

        // Handle user data
        $user = $preferences->emptype_preferences;
        if (is_string($user)) {
            $user = json_decode($user, true);
        }

        // Handle job data
        $jobType = $job->emplyeement_type;
        if (is_string($jobType)) {
            $jobType = json_decode($jobType, true);
        }

        if (empty($user) || empty($jobType)) {
            return 0;
        }

        $userFlat = $this->flattenArray($user);

        return count(array_intersect($userFlat, $jobType)) > 0 ? $weight : 0;
    }
    private function benefitsScore($preferences, $job)
    {
        $weight = 5;

        $user = json_decode($preferences->benefits_preferences, true);
        $jobBenefits = json_decode($job->benefits, true);

        if (!$user || !$jobBenefits) {
            return 0;
        }

        $userFlat = $this->flattenArray($user);
        $jobFlat = $this->flattenArray($jobBenefits);

        $matched = array_intersect($userFlat, $jobFlat);

        if (count($jobFlat) == 0) {
            return $weight;
        }

        return (count($matched) / count($jobFlat)) * $weight;
    }
    private function locationSalaryScore($preferences, $job)
    {
        $weight = 5;
        $score = 0;

        // LOCATION
        if (!empty($preferences->location_status) && !empty($job->location)) {
            if ($preferences->location_status == $job->location) {
                $score += 2.5;
            }
        }

        // SALARY
        if (!empty($preferences->salary_expectations) && !empty($job->salary)) {
            if ($job->salary >= $preferences->salary_expectations) {
                $score += 2.5;
            }
        }

        return $score;
    }
    private function checksClearanceScore($eligibility, $policeCheck, $workingChildren, $ndisCheck, $job)
    {
        $weight = 5;

        if (empty($job->checks_clearance_req)) {
            return $weight;
        }

        $requirements = $job->checks_clearance_req;

        if (is_string($requirements)) {
            $requirements = json_decode($requirements, true);
        }

        if (!is_array($requirements) || count($requirements) == 0) {
            return $weight;
        }

        $requiredChecks = 0;
        $matchedChecks = 0;

        $eligibilityGroup = ['citizen', 'pr', 'visa', 'sponsorship', 'bridging', 'supervised'];

        if (count(array_intersect($requirements, $eligibilityGroup)) > 0) {

            $requiredChecks++;

            if ($eligibility) {
                $matchedChecks++;
            }
        }

        if (in_array('ndis_screen_check', $requirements)) {

            $requiredChecks++;

            if ($ndisCheck) {
                $matchedChecks++;
            }
        }

        if (in_array('working_children', $requirements)) {

            $requiredChecks++;

            if ($workingChildren) {
                $matchedChecks++;
            }
        }
        if (in_array('police_clearance', $requirements)) {

            $requiredChecks++;

            if ($policeCheck) {
                $matchedChecks++;
            }
        }

        if ($requiredChecks == 0) {
            return $weight;
        }

        return ($matchedChecks / $requiredChecks) * $weight;
    }

    private function vaccinationScore($nurseVaccines, $job)
    {
        $weight = 5;

        // If job has no vaccination requirement → full score
        if (empty($job->vaccination_req) || $job->vaccination_req == "[]" || $job->vaccination_req == null) {
            return $weight;
        }

        $jobVaccines = $job->vaccination_req;

        // Convert JSON to array
        if (is_string($jobVaccines)) {
            $jobVaccines = json_decode($jobVaccines, true);
        }

        if (!is_array($jobVaccines) || count($jobVaccines) == 0) {
            return $weight; // treat as no requirement
        }

        $jobVaccines = array_map('intval', $jobVaccines);

        $matched = array_intersect($nurseVaccines, $jobVaccines);

        $score = (count($matched) / count($jobVaccines)) * $weight;

        return $score;
    }

    private function typeScore($nurseTypes, $job)
    {
        $weight = 15;
        if (empty($nurseTypes)) {
            return 0;
        }
        $jobTypes = $job->nurse_type_id;
        if (is_string($jobTypes)) {
            $jobTypes = json_decode($jobTypes, true);
        }
        if (!is_array($jobTypes)) {
            return 0;
        }
        $jobTypes = array_map('intval', $jobTypes);
        $matched = array_intersect($nurseTypes, $jobTypes);
        return count($matched) > 0 ? $weight : 0;
    }

    private function specialtyScore($nurseSpecialties, $job)
    {
        $weight = 15;
        if (empty($nurseSpecialties)) {
            return 0;
        }
        $jobTypes = $job->typeofspeciality;
        if (is_string($jobTypes)) {
            $jobTypes = json_decode($jobTypes, true);
        }
        if (!is_array($jobTypes)) {
            return 0;
        }
        $jobTypes = array_map('intval', $jobTypes);
        $matched = array_intersect($nurseSpecialties, $jobTypes);
        return count($matched) > 0 ? $weight : 0;
    }

    private function experienceScore($nurseData, $job)
    {
        $weight = 15;
        $jobSpecialties = $job->typeofspeciality;

        if (is_string($jobSpecialties)) {
            $jobSpecialties = json_decode($jobSpecialties, true);
        }

        if (!is_array($jobSpecialties)) {
            return 0;
        }

        // find nurse records with matching specialty
        $matched = $nurseData->whereIn('specialties', $jobSpecialties);

        if ($matched->isEmpty()) {
            return 0; // no specialty match
        }

        // take highest experience among matched specialties
        $nurseExperience = $matched->max('assistent_level');

        if ($job->experience_level == 0) {
            return $weight;
        }

        $ratio = min($nurseExperience / $job->experience_level, 1);
        return $ratio * $weight;
    }
    // private function specialtyScore($nurseSpecialties, $job)
    // {
    //     $weight = 15;

    //     // $nurseSpecialties = $nurseData->pluck('specialties')->toArray();

    //     $jobSpecialties = $job->specialties;

    //     if (is_string($jobSpecialties)) {
    //         $jobSpecialties = json_decode($jobSpecialties, true);
    //     }

    //     $matched = array_intersect($nurseSpecialties, $jobSpecialties);

    //     if (count($jobSpecialties) == 0) {
    //         return 0;
    //     }

    //     return (count($matched) / count($jobSpecialties)) * $weight;
    // }
}
