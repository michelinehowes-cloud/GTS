<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\Nomination;
use App\Models\JobOpportunity;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class GraduateController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        // إحصائيات الخريج
        $totalTrainings = Training::where('status', 'active')->count();
        $myApplications = TrainingApplication::where('user_id', $user->id)->count();
        $pendingApplications = TrainingApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $approvedApplications = TrainingApplication::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // بيانات الرسم البياني لحالة التقديمات
        $applicationStats = TrainingApplication::where('user_id', $user->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // التدريبات للتقويم
        $calendarTrainings = Training::where('status', 'active')
            ->get(['id', 'title', 'start_date', 'end_date', 'type'])
            ->map(function ($training) {
                return [
                    'title' => $training->title,
                    'start' => $training->start_date->format('Y-m-d'),
                    'end' => $training->end_date ? $training->end_date->format('Y-m-d') : null,
                    'url' => route('graduate.trainings.show', $training->id),
                    'className' => 'fc-event-' . $training->type
                ];
            });

        // الترشيحات (مطابقة عبر البريد الإلكتروني)
        $nominations = Nomination::whereHas('graduate', function ($q) use ($user) {
            $q->where('email', $user->email);
        })
            ->with(['jobOpportunity.company', 'nominator'])
            ->latest()
            ->get();

        // الإشعارات - استخدام الموديل المخصص
        $notifications = Notification::forUser($user->id)
            ->unread()
            ->latest()
            ->limit(5)
            ->get();

        // التقدم في التدريبات الحالية
        $activeTrainings = TrainingApplication::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereHas('training', function ($q) {
                $q->where('status', 'active');
            })
            ->with('training')
            ->get()
            ->map(function ($app) {
                $training = $app->training;
                $start = $training->start_date;
                $end = $training->end_date;
                $now = now();
                $progress = 0;

                if ($start && $end) {
                    if ($now < $start) {
                        $progress = 0;
                    } elseif ($now > $end) {
                        $progress = 100;
                    } else {
                        $totalDuration = $start->diffInDays($end);
                        $daysPassed = $start->diffInDays($now);
                        $progress = $totalDuration > 0 ? ($daysPassed / $totalDuration) * 100 : 0;
                    }
                }
                $app->progress = round($progress);
                return $app;
            });

        // التدريبات الموصى بها (آخر 3 تدريبات نشطة)
        $recommendedTrainings = Training::where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        // طلباتي الأخيرة
        $myRecentApplications = TrainingApplication::with('training')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('graduate.dashboard', compact(
            'totalTrainings',
            'myApplications',
            'pendingApplications',
            'approvedApplications',
            'recommendedTrainings',
            'myRecentApplications',
            'applicationStats',
            'calendarTrainings',
            'nominations',
            'notifications',
            'activeTrainings'
        ));
    }
}