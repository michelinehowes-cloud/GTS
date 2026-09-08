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
use App\Models\JobFair;
use App\Models\JobFairRegistration;

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
     * عرض بطاقة الخريج الرقمية الموحدة والثابتة
     */
    public function idCard(Request $request)
    {
        /** @var \App\Models\User $graduate */
        $graduate = Auth::user();
        $graduate->load('graduateData');

        // البحث عن المعرض الممرر أو الأخير
        $fairId = $request->query('fair_id');
        $activeFair = null;
        $fairRegistration = null;

        if ($fairId) {
            $activeFair = JobFair::find($fairId);
            if ($activeFair) {
                $fairRegistration = JobFairRegistration::where('job_fair_id', $activeFair->id)
                    ->where('user_id', $graduate->id)
                    ->first();
            }
        }

        if (!$activeFair) {
            // المعرض الأخير المسجل فيه الخريج
            $fairRegistration = JobFairRegistration::where('user_id', $graduate->id)
                ->with('jobFair')
                ->latest()
                ->first();
            if ($fairRegistration) {
                $activeFair = $fairRegistration->jobFair;
            } else {
                $activeFair = JobFair::latest()->first();
            }
        }

        // أحدث دورة تدريبية مقبولة للخريج
        $activeTraining = TrainingApplication::where('user_id', $graduate->id)
            ->where('status', 'approved')
            ->with('training')
            ->latest()
            ->first();

        // هل هناك سياق فعالية أو مشروع نشط؟
        $eventContext = null;
        if ($activeFair && $fairRegistration) {
            $eventContext = [
                'type'     => 'job_fair',
                'title'    => $activeFair->title,
                'subtitle' => 'معرض التوظيف — جامعة طرابلس',
                'logo'     => $activeFair->banner_image ? asset('storage/' . $activeFair->banner_image) : asset('images/job_fair_logo.png'),
                'badge'    => 'تذكرة رقم #' . $fairRegistration->registration_number,
                'status'   => $fairRegistration->attended ? 'تم الحضور' : 'مسجل ومعتمد',
                'date'     => $activeFair->event_date ? $activeFair->event_date->format('d/m/Y') : null,
                'location' => $activeFair->location ?? 'جامعة طرابلس',
            ];
        }

        return view('graduate.id-card', compact('graduate', 'activeFair', 'fairRegistration', 'activeTraining', 'eventContext'));
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
    /**
     * تحديث الملف الشخصي للخريج
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $oldEmail = $user->email;

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
            'university' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:100',
            'faculty' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:100',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'gpa' => 'nullable|numeric|min:0|max:100',
            'languages' => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone',
            'national_id',
            'date_of_birth',
            'gender',
            'address',
            'city',
            'qualification',
            'university',
            'sector',
            'faculty',
            'specialization',
            'graduation_year',
            'gpa',
            'languages',
        ]);

        $user->update($data);

        // تحديث البيانات في جدول graduates_data إذا وجد
        $graduateRecord = \App\Models\GraduateData::where('user_id', $user->id)
            ->orWhere('email', $oldEmail)
            ->orWhere('email', $user->email)
            ->first();

        if ($graduateRecord) {
            $graduateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? null,
                'national_id' => $data['national_id'] ?? null,
                'address' => ($data['city'] ?? '') . ' - ' . ($data['address'] ?? ''),
                'university' => $data['university'] ?? 'جامعة طرابلس',
                'sector' => $data['sector'] ?? null,
                'faculty' => $data['faculty'] ?? null,
                'major' => $data['specialization'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'gpa' => $data['gpa'] ?? null,
                'degree' => $data['qualification'] ?? 'بكالوريوس',
                'qualification' => $data['qualification'] ?? 'بكالوريوس',
                'languages' => $data['languages'] ? array_map('trim', explode(',', $data['languages'])) : null,
            ];

            $graduateRecord->update(array_filter($graduateData, function ($v) {
                return !is_null($v);
            }));
        }

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

    /**
     * رفع السيرة الذاتية
     */
    /**
     * رفع السيرة الذاتية
     */
    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'cv.required' => 'يرجى اختيار ملف السيرة الذاتية',
            'cv.mimes' => 'يجب أن يكون الملف بصيغة PDF',
            'cv.max' => 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت',
        ]);

        $user = Auth::user();

        // حذف السيرة الذاتية القديمة إن وجدت
        if ($user->cv_path && \Storage::exists('public/' . $user->cv_path)) {
            \Storage::delete('public/' . $user->cv_path);
        }

        // رفع السيرة الذاتية الجديدة
        $fileName = 'cv_' . $user->id . '_' . time() . '.pdf';
        $path = $request->file('cv')->storeAs('cvs', $fileName, 'public');

        // تحديث المسار في قاعدة البيانات (جدول users)
        $user->update([
            'cv_path' => $path
        ]);

        // تحديث المسار في جدول graduates_data إذا وجد
        \App\Models\GraduateData::where('email', $user->email)->update([
            'cv_path' => $path
        ]);

        return back()->with('success', 'تم رفع السيرة الذاتية بنجاح');
    }

    /**
     * تحميل السيرة الذاتية
     */
    public function downloadCV()
    {
        $user = Auth::user();

        if (!$user->cv_path || !\Storage::exists('public/' . $user->cv_path)) {
            return back()->with('error', 'السيرة الذاتية غير موجودة');
        }

        return \Storage::download('public/' . $user->cv_path, 'CV_' . $user->name . '.pdf');
    }

    /**
     * عرض السيرة الذاتية
     */
    public function viewCV()
    {
        $user = Auth::user();

        if (!$user->cv_path || !\Storage::exists('public/' . $user->cv_path)) {
            abort(404, 'السيرة الذاتية غير موجودة');
        }

        return response()->file(storage_path('app/public/' . $user->cv_path));
    }
}
