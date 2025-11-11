<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;

class GraduateController extends Controller
{
    public function dashboard()
    {
        // إحصائيات الخريج
        $totalTrainings = Training::where('status', 'active')->count();
        $myApplications = TrainingApplication::where('user_id', auth()->id())->count();
        $pendingApplications = TrainingApplication::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();
        $approvedApplications = TrainingApplication::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->count();

        // التدريبات الموصى بها (آخر 3 تدريبات نشطة)
        $recommendedTrainings = Training::where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        // طلباتي الأخيرة
        $myRecentApplications = TrainingApplication::with('training')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('graduate.dashboard', compact(
            'totalTrainings',
            'myApplications',
            'pendingApplications',
            'approvedApplications',
            'recommendedTrainings',
            'myRecentApplications'
        ));
    }
}