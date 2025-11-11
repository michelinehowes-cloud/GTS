<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class PlacementCoordinatorController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'active_jobs' => Job::where('status', 'active')->count(),
            'total_applications' => Application::count(),
            'pending_review' => Application::where('status', 'received')->count(),
            'interview_invited' => Application::where('status', 'interview_invited')->count(),
        ];

        $recentApplications = Application::with(['user', 'job.company'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('placement.dashboard', compact('stats', 'recentApplications'));
    }

    public function opportunities()
    {
        $jobs = Job::with('company')->where('status', 'active')->get();
        return view('placement.opportunities', compact('jobs'));
    }
}