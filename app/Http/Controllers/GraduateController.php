<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\Nomination;
use App\Models\JobOpportunity;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    /**
     * عرض الملف الشخصي للخريج
     */
    public function profile()
    {
        $user = Auth::user();
        return view('graduate.profile', compact('user'));
    }

    /**
     * تحديث الملف الشخصي للخريج
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'qualification' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'university' => 'nullable|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:4',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'phone',
            'national_id',
            'date_of_birth',
            'gender',
            'address',
            'city',
            'qualification',
            'specialization',
            'graduation_year',
            'university',
            'gpa',
        ]));

        return redirect()->route('graduate.profile')
            ->with('success', 'تم تحديث البيانات الشخصية بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
}
