<?php

namespace App\Services;

use App\Models\User;
use App\Models\Job;

class CandidateMatchingService
{
    public function findMatchingCandidates(Job $job, $limit = 10)
    {
        return User::where('role', 'graduate')
            ->with(['profile', 'enrollments.training'])
            ->get()
            ->filter(function ($graduate) use ($job) {
                return $this->calculateMatchScore($graduate, $job) >= 60;
            })
            ->sortByDesc(function ($graduate) use ($job) {
                return $this->calculateMatchScore($graduate, $job);
            })
            ->take($limit)
            ->values();
    }

    public function findMatchingJobs(User $graduate, $limit = 10)
    {
        return Job::where('status', 'active')
            ->get()
            ->filter(function ($job) use ($graduate) {
                return $this->calculateMatchScore($graduate, $job) >= 60;
            })
            ->sortByDesc(function ($job) use ($graduate) {
                return $this->calculateMatchScore($graduate, $job);
            })
            ->take($limit)
            ->values();
    }

    private function calculateMatchScore(User $graduate, Job $job)
    {
        $score = 0;
        $profile = $graduate->profile;

        if (!$profile) return 0;

        // مطابقة المهارات (40%)
        $requiredSkills = $job->requirements ?? [];
        $userSkills = $profile->skills ?? [];
        
        if (!empty($requiredSkills)) {
            $skillMatch = count(array_intersect($requiredSkills, $userSkills)) / count($requiredSkills);
            $score += $skillMatch * 40;
        }

        // مطابقة الخبرة (30%)
        $experience = $profile->experience ?? [];
        $experienceScore = min(count($experience) * 10, 30);
        $score += $experienceScore;

        // تقييم التدريب (30%)
        $trainingScore = $graduate->enrollments
            ->where('status', 'completed')
            ->avg('final_score') ?? 0;
        $score += ($trainingScore / 100) * 30;

        return min($score, 100);
    }
}