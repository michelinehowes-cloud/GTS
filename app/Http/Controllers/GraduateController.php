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

        // التدريبات للتقويم المعتمد
        $typeColors = [
            'workshop'   => ['bg' => '#059669', 'border' => '#047857', 'prefix' => 'ورشة: '],
            'course'     => ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => 'دورة: '],
            'internship' => ['bg' => '#1d4ed8', 'border' => '#1e40af', 'prefix' => 'تدريب: '],
            'seminar'    => ['bg' => '#d97706', 'border' => '#b45309', 'prefix' => 'ندوة: '],
        ];

        $calendarTrainings = Training::where('status', 'active')
            ->get(['id', 'title', 'start_date', 'end_date', 'type', 'location'])
            ->map(function ($training) use ($typeColors) {
                $cfg = $typeColors[$training->type] ?? ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => ''];
                $endDate = $training->end_date ? $training->end_date->copy()->addDay()->format('Y-m-d') : null;
                return [
                    'id' => $training->id,
                    'title' => $cfg['prefix'] . $training->title,
                    'start' => $training->start_date ? $training->start_date->format('Y-m-d') : null,
                    'end' => $endDate,
                    'url' => route('graduate.trainings.show', $training->id),
                    'backgroundColor' => $cfg['bg'],
                    'borderColor' => $cfg['border'],
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => $training->type,
                        'location' => $training->location ?? 'جامعة طرابلس',
                        'rawTitle' => $training->title,
                    ],
                    'className' => 'fc-event-custom fc-event-' . $training->type
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

        // جميع الفعاليات والمعارض النشطة الجارية أو القادمة (غير المنتهية) التي سجل بها هذا الخريج تحديداً
        $myRegistrations = JobFairRegistration::where('user_id', $graduate->id)
            ->with('jobFair')
            ->get()
            ->filter(function ($reg) {
                return $reg->jobFair !== null && !$reg->jobFair->is_ended;
            });

        $selectedMode = $request->query('mode'); // 'general' or null
        $fairId = $request->query('fair_id');

        $activeFair = null;
        $fairRegistration = null;

        // 1. إذا اختار الخريج صراحة عرض "الهوية العامة" فقط
        if ($selectedMode === 'general') {
            $activeFair = null;
            $fairRegistration = null;
        } elseif ($fairId) {
            // 2. إذا حدد الخريج فعالية معينة عبر الرابط أو شريط التبديل وكانت غير منتهية
            $fairRegistration = $myRegistrations->firstWhere('job_fair_id', $fairId);
            if ($fairRegistration && !$fairRegistration->jobFair->is_ended) {
                $activeFair = $fairRegistration->jobFair;
            }
        } elseif ($myRegistrations->isNotEmpty()) {
            // 3. التحديد التلقائي الأذكى: أقرب فعالية نشطة قادمة
            $upcomingReg = $myRegistrations->sortBy(function ($reg) {
                return $reg->jobFair->event_date;
            })->first();

            if ($upcomingReg) {
                $fairRegistration = $upcomingReg;
                $activeFair = $upcomingReg->jobFair;
            }
        }

        // أحدث دورة تدريبية مقبولة للخريج
        $activeTraining = TrainingApplication::where('user_id', $graduate->id)
            ->where('status', 'approved')
            ->with('training')
            ->latest()
            ->first();

        // إعداد سياق الفعالية فقط في حال كان الخريج مسجلاً بالفعل في فعالية نشطة
        $eventContext = null;
        if ($activeFair && $fairRegistration) {
            $isWhiteLogo = !empty($activeFair->fair_logo_white_path) || ($activeFair->id === 1 && file_exists(public_path('images/job_fair_logo_white.png')));
            $eventContext = [
                'type'          => 'job_fair',
                'fair_id'       => $activeFair->id,
                'title'         => $activeFair->title,
                'subtitle'      => 'معرض التوظيف — جامعة طرابلس',
                'logo'          => $activeFair->white_logo_url ?: $activeFair->logo_url,
                'is_white_logo' => $isWhiteLogo,
                'badge'         => 'تذكرة رقم #' . $fairRegistration->registration_number,
                'status'        => $fairRegistration->attended ? 'تم الحضور' : 'مسجل ومعتمد',
                'date'          => $activeFair->event_date ? $activeFair->event_date->format('d/m/Y') : null,
                'location'      => $activeFair->location ?? 'جامعة طرابلس',
                'qr_data'       => $fairRegistration->qr_code ?: route('graduate.profile.public', $graduate->id),
            ];
        }

        return view('graduate.id-card', compact(
            'graduate',
            'myRegistrations',
            'activeFair',
            'fairRegistration',
            'activeTraining',
            'eventContext'
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
            'degree' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'university_id' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:100',
            'sector_id' => 'nullable|string|max:100',
            'faculty' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:100',
            'specialization_id' => 'nullable|string|max:100',
            'major' => 'nullable|string|max:100',
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
        ]);

        // توحيد الحقول الأكاديمية ومعالجة المرادفات
        $data['university'] = $data['university'] ?? $request->input('university_id') ?? $user->university ?? 'جامعة طرابلس';
        $data['sector'] = $data['sector'] ?? $request->input('sector_id') ?? $user->sector;
        $data['faculty'] = $data['faculty'] ?? $request->input('faculty_id') ?? $user->faculty;
        
        $specValue = $data['specialization'] ?? $request->input('specialization_id') ?? $request->input('major') ?? $user->specialization ?? $user->major;
        $data['specialization'] = $specValue;
        $data['major'] = $specValue;

        $qualValue = $data['qualification'] ?? $request->input('degree') ?? $user->qualification ?? $user->degree ?? 'بكالوريوس';
        $data['qualification'] = $qualValue;
        $data['degree'] = $qualValue;

        // معالجة اللغات المتقنة كمصفوفة لتوافق casts في User و GraduateData
        if ($request->has('languages')) {
            $langInput = $request->input('languages');
            if (is_string($langInput)) {
                $data['languages'] = array_values(array_filter(array_map('trim', explode(',', $langInput))));
            } else {
                $data['languages'] = $langInput;
            }
        }

        $user->update($data);

        // مزامنة البيانات وتحديثها فورياً في جدول graduates_data
        try {
            \App\Models\GraduateData::syncFromUser($user);
        } catch (\Exception $e) {
            \Log::warning('Failed GraduateData sync in updateProfile: ' . $e->getMessage());
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
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
        ]);

        $user = Auth::user();
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
