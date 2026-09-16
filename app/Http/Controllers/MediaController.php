<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\MediaPlatformStat;
use App\Models\User;
use App\Models\Company;
use App\Models\JobOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('media_officer');
    }

    /**
     * لوحة تحكم الميديا
     */
    public function dashboard()
    {
        $user = Auth::user();

        // إحصائيات سريعة
        $reportsCount = Training::where(function($q) {
            $q->whereNotNull('media_press_release')->orWhereNotNull('media_coverage_summary');
        })->count();
        $activeNews = \App\Models\News::active()->count();
        $activeAnnouncements = \App\Models\Announcement::active()->count();
        $totalTrainings = Training::count();
        $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
        $coverageRate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100) : 0;

        // التدريبات القادمة ومتابعة التغطية
        $upcomingTrainings = Training::where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(6)
            ->get();

        // الأخبار والإعلانات الأخيرة
        $recentNews = \App\Models\News::orderBy('published_at', 'desc')->take(4)->get();
        $recentAnnouncements = \App\Models\Announcement::orderBy('created_at', 'desc')->take(4)->get();

        return view('media.dashboard', compact(
            'reportsCount',
            'activeNews',
            'activeAnnouncements',
            'totalTrainings',
            'coveredTrainings',
            'coverageRate',
            'upcomingTrainings',
            'recentNews',
            'recentAnnouncements'
        ));
    }

    /**
     * عرض فهرس التدريبات
     */
    public function trainingsIndex(Request $request)
    {
        $query = Training::with(['company', 'coordinator']);

        // البحث والتصفية
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('media_coverage_status')) {
            $query->where('media_coverage_status', $request->media_coverage_status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $trainings = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('media.trainings.index', compact('trainings'));
    }

    /**
     * عرض تفاصيل تدريب مع التغطية والروابط
     */
    public function trainingShow(Training $training)
    {
        $training->load(['company', 'coordinator']);
        return view('media.trainings.show', compact('training'));
    }

    /**
     * تحديث حالة التغطية الإعلامية للتدريب
     */
    public function updateCoverageStatus(Request $request, Training $training)
    {
        $request->validate([
            'media_coverage_status' => 'required|in:pending,covered,not_required'
        ]);

        $training->update([
            'media_coverage_status' => $request->media_coverage_status
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة التغطية الإعلامية بنجاح');
    }

    /**
     * فهرس ولوحة تقارير التغطية الإعلامية والبيانات الصحفية
     */
    public function reportsIndex(Request $request)
    {
        $query = Training::with(['company', 'coordinator']);

        // البحث بالاسم أو الموقع أو الشركة
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('company', function ($cq) use ($searchTerm) {
                      $cq->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // تصفية حالة التغطية
        if ($request->filled('coverage_status')) {
            $query->where('media_coverage_status', $request->coverage_status);
        }

        // تصفية السنة
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $trainings = $query->orderBy('start_date', 'desc')->paginate(12);

        // إحصائيات تقارير التغطية الإعلامية
        $totalTrainings = Training::count();
        $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
        $pendingCoverage = Training::where('media_coverage_status', 'pending')->orWhereNull('media_coverage_status')->count();
        $notRequiredCoverage = Training::where('media_coverage_status', 'not_required')->count();
        $reportsCount = Training::where(function($q) {
            $q->whereNotNull('media_press_release')->orWhereNotNull('media_coverage_summary');
        })->count();
        $withLinksCount = Training::whereNotNull('media_coverage_links')->where('media_coverage_links', '!=', '')->count();
        $coverageRate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100) : 0;

        $stats = [
            'total' => $totalTrainings,
            'covered' => $coveredTrainings,
            'pending' => $pendingCoverage,
            'not_required' => $notRequiredCoverage,
            'total_reports' => $reportsCount,
            'with_links' => $withLinksCount,
            'rate' => $coverageRate,
        ];

        return view('media.reports.index', compact('trainings', 'stats'));
    }

    /**
     * عرض تقرير تغطية تدريب وبيانه الصحفي
     */
    public function createCoverageReport(Training $training)
    {
        $training->load(['company', 'coordinator']);
        return view('media.reports.coverage', compact('training'));
    }

    /**
     * نموذج تحرير وكتابة التقرير الصحفي والتغطية الإعلامية
     */
    public function editCoverageReport(Training $training)
    {
        $training->load(['company', 'coordinator']);
        return view('media.reports.edit', compact('training'));
    }

    /**
     * حفظ وتحديث التقرير الصحفي والتغطية الإعلامية
     */
    public function updateCoverageReport(Request $request, Training $training)
    {
        $validated = $request->validate([
            'media_coverage_status' => 'required|in:pending,covered,not_required',
            'media_coverage_summary' => 'nullable|string',
            'media_press_release' => 'nullable|string',
            'media_coverage_notes' => 'nullable|string',
            'media_team_members' => 'nullable|string|max:255',
            'media_coverage_links' => 'nullable|string',
            'media_coverage_date' => 'nullable|date',
        ]);

        $training->update($validated);

        return redirect()->route('media.reports.coverage.show', $training)
            ->with('success', 'تم حفظ التقرير الصحفي والتوثيق الإعلامي للبرنامج التدريبي بنجاح.');
    }

    /**
     * عرض تقويم التدريبات
     */
    public function trainingCalendarIndex(Request $request)
    {
        return redirect()->route('media.coverage-calendar');
    }

    private function generateCalendar($month, $year, $trainings)
    {
        $startDate = \Carbon\Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        $calendar = [];
        $currentDay = $startDate->copy();

        $firstDayOfWeek = $currentDay->dayOfWeek;
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar[] = ['day' => null, 'trainings' => []];
        }

        while ($currentDay->month == $month) {
            $dayTrainings = $trainings->filter(function ($training) use ($currentDay) {
                return $currentDay->between(\Carbon\Carbon::parse($training->start_date)->startOfDay(), \Carbon\Carbon::parse($training->end_date)->endOfDay());
            });

            $calendar[] = [
                'day' => $currentDay->copy(),
                'trainings' => $dayTrainings
            ];

            $currentDay->addDay();
        }

        return [
            'days' => $days,
            'weeks' => array_chunk($calendar, 7),
            'month_name' => $this->getArabicMonthName($month)
        ];
    }

    private function getArabicMonthName($month)
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        return $months[$month] ?? 'غير معروف';
    }

    // ==================== 📅 تقويم وجدول التغطيات الإعلامية ====================

    /**
     * جدول وتقويم التغطيات الإعلامية
     */
    public function coverageCalendar(Request $request)
    {
        $trainings = Training::with(['company', 'trainer', 'coordinator'])
            ->orderBy('start_date', 'asc')
            ->get();

        $stats = [
            'total' => $trainings->count(),
            'covered' => $trainings->where('media_coverage_status', 'covered')->count(),
            'pending' => $trainings->filter(function($t) {
                return $t->media_coverage_status === 'pending' || is_null($t->media_coverage_status);
            })->count(),
            'locations' => $trainings->pluck('location')->filter()->unique()->count(),
        ];

        // ألوان الفعاليات وفق هوية المنظومة المعتمدة في تقويم منسق التدريب
        $typeColors = [
            'workshop'   => ['bg' => '#059669', 'border' => '#047857', 'prefix' => 'ورشة: '],
            'course'     => ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => 'دورة: '],
            'internship' => ['bg' => '#1d4ed8', 'border' => '#1e40af', 'prefix' => 'تدريب عملي: '],
            'seminar'    => ['bg' => '#d97706', 'border' => '#b45309', 'prefix' => 'ندوة: '],
        ];

        $calendarTrainings = $trainings->map(function ($t) use ($typeColors) {
            $cfg = $typeColors[$t->type] ?? ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => ''];
            $endDate = $t->end_date ? $t->end_date->copy()->addDay()->format('Y-m-d') : null;
            return [
                'id'              => $t->id,
                'title'           => $cfg['prefix'] . $t->title,
                'start'           => $t->start_date ? $t->start_date->format('Y-m-d') : null,
                'end'             => $endDate,
                'url'             => route('media.reports.coverage.show', $t->id),
                'backgroundColor' => $cfg['bg'],
                'borderColor'     => $cfg['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'type'               => $t->type,
                    'typeArabic'         => $t->type_arabic,
                    'location'           => $t->location ?? 'جامعة طرابلس',
                    'rawTitle'           => $t->title,
                    'instructor'         => $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد'),
                    'seats'              => $t->seats ?? '—',
                    'status'             => $t->status,
                    'duration'           => $t->duration ?? '—',
                    'startDate'          => $t->start_date ? $t->start_date->format('Y-m-d') : '—',
                    'endDate'            => $t->end_date ? $t->end_date->format('Y-m-d') : '—',
                    'coverageStatus'     => $t->media_coverage_status ?? 'pending',
                    'coverageStatusText' => $t->getMediaCoverageStatusText(),
                    'hasPressRelease'    => !empty($t->media_press_release) || !empty($t->media_coverage_summary),
                    'hasLinks'           => !empty($t->media_coverage_links),
                    'reportShowUrl'      => route('media.reports.coverage.show', $t->id),
                    'reportEditUrl'      => route('media.reports.coverage.edit', $t->id),
                    'trainingShowUrl'    => route('media.trainings.show', $t->id),
                ],
                'className'       => 'fc-event-custom fc-event-' . $t->type
            ];
        })->values();

        return view('media.coverage-calendar', compact('trainings', 'stats', 'calendarTrainings'));
    }

    /**
     * تحديث حالة وملاحظات التغطية لبرنامج تدريبي
     */
    public function updateCoverageTask(Request $request, Training $training)
    {
        return $this->updateCoverageStatus($request, $training);
    }

    // ==================== 📊 إدارة إحصائيات المنصة والصفحة الرئيسية ====================

    /**
     * شاشة التحكم بإحصائيات المنصة والواجهة العامة
     */
    public function platformStats()
    {
        $setting = MediaPlatformStat::current();
        $homepageData = MediaPlatformStat::getHomepageStats();

        $realCounts = [
            'graduates' => User::where('role', 'graduate')->count(),
            'companies' => Company::count(),
            'trainings' => Training::count(),
            'opportunities' => JobOpportunity::count(),
        ];

        return view('media.platform-stats', compact('setting', 'homepageData', 'realCounts'));
    }

    /**
     * تحديث إعدادات إحصائيات المنصة
     */
    public function updatePlatformStats(Request $request)
    {
        $request->validate([
            'global_mode' => 'required|in:auto,manual',
            'graduates_mode' => 'required|in:auto,manual',
            'graduates_custom_value' => 'nullable|integer|min:0',
            'graduates_label' => 'nullable|string|max:100',
            'graduates_prefix' => 'nullable|string|max:10',
            'companies_mode' => 'required|in:auto,manual',
            'companies_custom_value' => 'nullable|integer|min:0',
            'companies_label' => 'nullable|string|max:100',
            'companies_prefix' => 'nullable|string|max:10',
            'trainings_mode' => 'required|in:auto,manual',
            'trainings_custom_value' => 'nullable|integer|min:0',
            'trainings_label' => 'nullable|string|max:100',
            'trainings_prefix' => 'nullable|string|max:10',
            'opportunities_mode' => 'required|in:auto,manual',
            'opportunities_custom_value' => 'nullable|integer|min:0',
            'opportunities_label' => 'nullable|string|max:100',
            'opportunities_prefix' => 'nullable|string|max:10',
        ]);

        $setting = MediaPlatformStat::current();
        $setting->update([
            'is_ribbon_visible' => $request->has('is_ribbon_visible'),
            'global_mode' => $request->global_mode,
            'graduates_mode' => $request->graduates_mode,
            'graduates_custom_value' => $request->graduates_custom_value,
            'graduates_visible' => $request->has('graduates_visible'),
            'graduates_label' => $request->graduates_label ?: 'خريج مسجل ومعتمد',
            'graduates_prefix' => $request->graduates_prefix ?? '',
            'companies_mode' => $request->companies_mode,
            'companies_custom_value' => $request->companies_custom_value,
            'companies_visible' => $request->has('companies_visible'),
            'companies_label' => $request->companies_label ?: 'شركة ومؤسسة شريكة',
            'companies_prefix' => $request->companies_prefix ?? '',
            'trainings_mode' => $request->trainings_mode,
            'trainings_custom_value' => $request->trainings_custom_value,
            'trainings_visible' => $request->has('trainings_visible'),
            'trainings_label' => $request->trainings_label ?: 'برنامج تدريبي وتأهيلي',
            'trainings_prefix' => $request->trainings_prefix ?? '',
            'opportunities_mode' => $request->opportunities_mode,
            'opportunities_custom_value' => $request->opportunities_custom_value,
            'opportunities_visible' => $request->has('opportunities_visible'),
            'opportunities_label' => $request->opportunities_label ?: 'فرصة عمل وترشيح',
            'opportunities_prefix' => $request->opportunities_prefix ?? '',
            'updated_by_user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'تم حفظ وتحديث إحصائيات المنصة والصفحة الرئيسية بنجاح، وانعكست فوراً على الواجهة العامة.');
    }
}

