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
            'internship' => ['bg' => '#1d4ed8', 'border' => '#1e40af', 'prefix' => 'تدريب عملي: '],
            'seminar'    => ['bg' => '#d97706', 'border' => '#b45309', 'prefix' => 'ندوة: '],
        ];

        // تسجيلات الخريج في البرامج التدريبية لتحديد تدريباته الخاصة
        $myApplicationsMap = TrainingApplication::where('user_id', $user->id)
            ->get(['training_id', 'status'])
            ->keyBy('training_id');

        $trainingsForCalendar = Training::where('status', 'active')
            ->with(['trainer'])
            ->orderBy('start_date', 'asc')
            ->get();

        // تصفية التكرارات المتطابقة الناتجة عن البيانات الاختبارية المتكررة، مع إعطاء الأولوية القصوى للتدريبات التي سجل بها الخريج
        $uniqueTrainings = collect();
        $seenKeys = [];

        foreach ($trainingsForCalendar as $t) {
            $isMy = isset($myApplicationsMap[$t->id]);
            $key = trim($t->title) . '|' . ($t->start_date ? $t->start_date->format('Y-m-d') : '') . '|' . ($t->end_date ? $t->end_date->format('Y-m-d') : '');

            if ($isMy) {
                $uniqueTrainings->put($t->id, $t);
                $seenKeys[$key] = true;
            } elseif (!isset($seenKeys[$key])) {
                $uniqueTrainings->put($t->id, $t);
                $seenKeys[$key] = true;
            }
        }

        $calendarTrainings = collect();

        foreach ($uniqueTrainings as $t) {
            if (!$t->start_date) continue;

            $cfg = $typeColors[$t->type] ?? ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => ''];
            $isMy = isset($myApplicationsMap[$t->id]);
            $appStatus = $isMy ? $myApplicationsMap[$t->id]->status : null;

            $startDate = \Carbon\Carbon::parse($t->start_date);
            $endDate = $t->end_date ? \Carbon\Carbon::parse($t->end_date) : null;
            $durationDays = $endDate ? $startDate->diffInDays($endDate) : 0;

            $allowedDays = $t->training_days_of_week;
            $hasSpecificDays = is_array($allowedDays) && count($allowedDays) > 0 && count($allowedDays) < 7;

            $extendedProps = [
                'type'          => $t->type,
                'location'      => $t->location ?? 'جامعة طرابلس',
                'rawTitle'      => $t->title,
                'instructor'    => $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد'),
                'seats'         => $t->seats ?? '—',
                'status'        => $t->status,
                'duration'      => $t->duration ?? '—',
                'startDate'     => $startDate->format('Y-m-d'),
                'endDate'       => $endDate ? $endDate->format('Y-m-d') : '—',
                'timing'        => $t->timing_text ?: 'غير محدد',
                'compactTiming' => $t->compact_timing_text ?: ($t->timing_text ?: ''),
                'startTime'     => $t->formatted_start_time,
                'endTime'       => $t->formatted_end_time,
                'showUrl'       => route('graduate.trainings.show', $t->id),
                'isMyTraining'  => $isMy,
                'myAppStatus'   => $appStatus,
            ];

            $myBadge = $isMy ? '⭐ ' : '';
            $bgColor = $isMy ? '#0d3882' : $cfg['bg'];
            $borderColor = $isMy ? '#f59e0b' : $cfg['border'];
            $myClass = $isMy ? ' fc-event-my-training' : '';

            // الحالة الأولى: الفعاليات القصيرة (يوم إلى يومين)
            if ($durationDays <= 2 || !$endDate || $startDate->isSameDay($endDate)) {
                $calendarTrainings->push([
                    'id'              => 'tr_' . $t->id,
                    'title'           => $myBadge . $cfg['prefix'] . $t->title,
                    'start'           => $startDate->format('Y-m-d'),
                    'end'             => ($endDate && !$startDate->isSameDay($endDate)) ? $endDate->copy()->addDay()->format('Y-m-d') : null,
                    'allDay'          => true,
                    'url'             => route('graduate.trainings.show', $t->id),
                    'backgroundColor' => $bgColor,
                    'borderColor'     => $borderColor,
                    'textColor'       => '#ffffff',
                    'extendedProps'   => array_merge($extendedProps, ['eventRole' => 'event']),
                    'className'       => 'fc-event-custom fc-event-' . $t->type . $myClass,
                ]);
            } else {
                // الحالة الثانية: البرامج التدريبية الممتدة (أكثر من يومين)
                // لمنع تغطية أسابيع كاملة بشرائط صلبة متراكمة تسبب فوضى، نحدد حدث انطلاق البرنامج وحدث ختامه
                // 1. حدث انطلاق / بدء التدريب
                $calendarTrainings->push([
                    'id'              => 'tr_' . $t->id . '_start',
                    'title'           => $myBadge . ($isMy ? 'بدء: ' : '🚀 انطلاق: ') . $t->title,
                    'start'           => $startDate->format('Y-m-d'),
                    'end'             => null,
                    'allDay'          => true,
                    'url'             => route('graduate.trainings.show', $t->id),
                    'backgroundColor' => $bgColor,
                    'borderColor'     => $borderColor,
                    'textColor'       => '#ffffff',
                    'extendedProps'   => array_merge($extendedProps, ['eventRole' => 'start']),
                    'className'       => 'fc-event-custom fc-event-start fc-event-' . $t->type . $myClass,
                ]);

                // 2. حدث ختام / نهاية التدريب
                if ($endDate) {
                    $calendarTrainings->push([
                        'id'              => 'tr_' . $t->id . '_end',
                        'title'           => $myBadge . ($isMy ? 'ختام: ' : '🏁 ختام: ') . $t->title,
                        'start'           => $endDate->format('Y-m-d'),
                        'end'             => null,
                        'allDay'          => true,
                        'url'             => route('graduate.trainings.show', $t->id),
                        'backgroundColor' => $isMy ? '#1e3a8a' : '#475569',
                        'borderColor'     => $isMy ? '#f59e0b' : '#334155',
                        'textColor'       => '#ffffff',
                        'extendedProps'   => array_merge($extendedProps, ['eventRole' => 'end']),
                        'className'       => 'fc-event-custom fc-event-end fc-event-' . $t->type . $myClass,
                    ]);
                }

                // 3. إذا كان الخريج مسجلاً بالتدريب وله أيام تدريب محددة أسبوعياً، نُدرج جلساته المباشرة
                if ($isMy && $hasSpecificDays) {
                    $allowedDays = array_map('intval', $allowedDays);
                    $current = $startDate->copy();
                    while ($current->lte($endDate)) {
                        if (in_array($current->dayOfWeek, $allowedDays, true)) {
                            $calendarTrainings->push([
                                'id'              => 'tr_' . $t->id . '_' . $current->format('Ymd'),
                                'title'           => '⭐ جلسة: ' . $t->title,
                                'start'           => $current->format('Y-m-d'),
                                'end'             => null,
                                'allDay'          => true,
                                'url'             => route('graduate.trainings.show', $t->id),
                                'backgroundColor' => '#0d3882',
                                'borderColor'     => '#f59e0b',
                                'textColor'       => '#ffffff',
                                'extendedProps'   => array_merge($extendedProps, ['eventRole' => 'session']),
                                'className'       => 'fc-event-custom fc-event-session fc-event-my-training',
                            ]);
                        }
                        $current->addDay();
                    }
                }
            }
        }

        $calendarLocations = $uniqueTrainings->pluck('location')->filter()->unique()->values();
        $myCalendarCount = $calendarTrainings->where('extendedProps.isMyTraining', true)->count();


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
            'calendarLocations',
            'myCalendarCount',
            'nominations',
            'notifications',
            'activeTrainings'
        ));
    }

    /**
     * عرض بطاقة الخريج الرقمية الموحدة والثابتة
     * تصدر لكل خريج يسجل في المنصة وتحمل شعار مكتب تدريب الخريجين فقط
     */
    public function idCard(Request $request)
    {
        /** @var \App\Models\User $graduate */
        $graduate = Auth::user();
        $graduate->load('graduateData');

        // جميع الفعاليات والمعارض النشطة الجارية أو القادمة التي سجل بها هذا الخريج تحديداً
        $myRegistrations = JobFairRegistration::where('user_id', $graduate->id)
            ->with('jobFair')
            ->get()
            ->filter(function ($reg) {
                return $reg->jobFair !== null && !$reg->jobFair->is_ended;
            });

        // أحدث دورة تدريبية مقبولة للخريج
        $activeTraining = TrainingApplication::where('user_id', $graduate->id)
            ->where('status', 'approved')
            ->with('training')
            ->latest()
            ->first();

        return view('graduate.id-card', compact(
            'graduate',
            'myRegistrations',
            'activeTraining'
        ));
    }

    /**
     * عرض الملف الشخصي للخريج
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load([
            'graduateData',
            'trainingApplications.training',
            'certificates.training'
        ]);

        $myTrainings = $user->trainingApplications()->with('training')->latest()->take(5)->get();
        $myCertificates = $user->certificates()->with('training')->latest()->take(5)->get();

        return view('graduate.profile', compact('user', 'myTrainings', 'myCertificates'));
    }

    /**
     * تحديث الملف الشخصي للخريج
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'national_id' => 'nullable|string|max:50',
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
            'employment_status' => 'nullable|string|in:seeking_opportunities,employed,unemployed,further_study,continuing_education',
            'languages' => 'nullable',
            'skills' => 'nullable',
            'experiences' => 'nullable|string',
            'education' => 'nullable|string',
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
            'experiences',
            'education',
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
                $cleaned = str_replace('،', ',', $langInput);
                $data['languages'] = array_values(array_filter(array_map('trim', explode(',', $cleaned))));
            } elseif (is_array($langInput)) {
                $data['languages'] = array_values(array_filter($langInput));
            } else {
                $data['languages'] = [];
            }
        }

        // معالجة المهارات كمصفوفة لتوافق casts في User و GraduateData
        if ($request->has('skills')) {
            $skillsInput = $request->input('skills');
            if (is_string($skillsInput)) {
                $cleaned = str_replace('،', ',', $skillsInput);
                $data['skills'] = array_values(array_filter(array_map('trim', explode(',', $cleaned))));
            } elseif (is_array($skillsInput)) {
                $data['skills'] = array_values(array_filter($skillsInput));
            } else {
                $data['skills'] = [];
            }
        }

        $user->update($data);

        // إعداد الحقول الإضافية لمزامنة جدول graduates_data
        $syncPayload = [];
        if ($request->filled('employment_status')) {
            $syncPayload['employment_status'] = $request->input('employment_status');
        }
        if (array_key_exists('education', $data)) {
            $syncPayload['certifications'] = $data['education'];
        }
        if (array_key_exists('experiences', $data)) {
            $syncPayload['work_experience'] = $data['experiences'];
        }

        // مزامنة البيانات وتحديثها فورياً في جدول graduates_data
        try {
            \App\Models\GraduateData::syncFromUser($user, $syncPayload);
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
