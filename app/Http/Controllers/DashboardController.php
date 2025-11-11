<?php
// ملف: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Training;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // استخدام نظام بسيط للصلاحيات
        if ($this->isAdmin($user)) {
            return $this->adminDashboard($user);
        } 
        
        // إذا كان خريج أو مستخدم عادي
        return $this->graduateDashboard($user);
    }

    private function isAdmin($user)
    {
        // طريقة 1: إذا كان لديك حقل user_type في قاعدة البيانات
        // return $user->user_type === 'admin';
        
        // طريقة 2: التحقق من الإيميل (للتطوير)
        return in_array($user->email, ['admin@admin.com', 'coordinator@admin.com']);
        
        // طريقة 3: إذا كان User ID = 1 (للتطوير)
        // return $user->id === 1;
    }

    private function adminDashboard($user)
    {
        $stats = [
            'total_users' => User::count(),
            'total_trainings' => Training::count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
        ];

        $latestTrainings = Training::latest()->take(5)->get();
        $recentApplications = Application::with(['user', 'training'])->latest()->take(5)->get();

        // استخدام المسار الصحيح - admin.dashboard
        return view('admin.dashboard', compact('stats', 'latestTrainings', 'recentApplications'));
    }

    private function graduateDashboard($user)
    {
        $stats = [
            'total_trainings' => Training::where('status', 'active')->count(),
            'accepted_applications' => $user->applications()->where('status', 'accepted')->count(),
            'pending_applications' => $user->applications()->where('status', 'pending')->count(),
        ];

        $myApplications = $user->applications()->with('training')->latest()->take(5)->get();

        // إذا كان لديك ملف للخريجين، غيّر المسار هنا
        // إذا لم يكن موجوداً، استخدم admin.dashboard مؤقتاً
        return view('admin.dashboard', compact('stats', 'myApplications'));
    }
}