<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobFairVisitorController extends Controller
{
    /**
     * تسجيل زائر جديد في المعرض (متاح للعامة)
     */
    public function store(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'visitor_type' => 'nullable|string|in:student,job_seeker,parent,company_rep,academic,general',
            'education_level' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'visit_purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'يرجى إدخال اسمك الكامل أو الرباعي.',
            'phone.required' => 'يرجى إدخال رقم هاتفك المحمول أو الواتساب.',
        ]);

        $validated['visitor_type'] = $validated['visitor_type'] ?? 'job_seeker';

        // التحقق من عدم التكرار لنفس المعرض ورقم الهاتف
        $existing = JobFairVisitor::where('job_fair_id', $fair->id)
            ->where('phone', $validated['phone'])
            ->first();

        if ($existing) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'already_registered' => true,
                    'message' => 'أنت مسجّل مسبقاً في هذا المعرض! تم فتح تذكرتك الرقمية مباشرة.',
                    'ticket_number' => $existing->ticket_number,
                    'ticket_url' => route('job-fair.visitor.ticket', $existing->ticket_number),
                ]);
            }

            return redirect()->route('job-fair.visitor.ticket', $existing->ticket_number)
                ->with('info', 'أنت مسجل مسبقاً في هذا المعرض، إليك بطاقتك الرقمية الرسمية.');
        }

        // توليد رقم تذكرة فريد
        $year = Carbon::parse($fair->event_date)->year ?: date('Y');
        $random = strtoupper(Str::random(5));
        $ticketNumber = "VIS-{$year}-{$random}";

        while (JobFairVisitor::where('ticket_number', $ticketNumber)->exists()) {
            $ticketNumber = "VIS-{$year}-" . strtoupper(Str::random(5));
        }

        $validated['job_fair_id'] = $fair->id;
        $validated['ticket_number'] = $ticketNumber;
        $validated['qr_code'] = route('job-fair.visitor.ticket', $ticketNumber);
        $validated['attended'] = false;

        $visitor = JobFairVisitor::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيلك بنجاح كزائر رسمي لمعرض التوظيف!',
                'ticket_number' => $visitor->ticket_number,
                'ticket_url' => route('job-fair.visitor.ticket', $visitor->ticket_number),
                'visitor' => [
                    'name' => $visitor->name,
                    'type_label' => $visitor->visitor_type_label,
                    'ticket_number' => $visitor->ticket_number,
                ]
            ]);
        }

        return redirect()->route('job-fair.visitor.ticket', $visitor->ticket_number)
            ->with('success', 'تم تسجيلك بنجاح كزائر لمعرض التوظيف! احتفظ بهذه البطاقة لإبرازها عند الدخول.');
    }

    /**
     * عرض بطاقة/تذكرة الزائر الرقمية
     */
    public function showTicket($ticketNumber)
    {
        $visitor = JobFairVisitor::with('jobFair')->where('ticket_number', $ticketNumber)->firstOrFail();
        $fair = $visitor->jobFair;

        return view('job-fair.visitor-ticket', compact('visitor', 'fair'));
    }

    /**
     * لوحة إدارة الزوار وإحصائياتهم (للإدارة وفريق المعرض)
     */
    public function adminIndex(Request $request, JobFair $fair)
    {
        if (auth()->check()) {
            $u = auth()->user();
            if (!$u->isAdmin() && !$u->hasAnyPermission(['job_fair.visitors', 'job_fair.registrations', 'job_fair.manage', 'job_fair.view']) && !in_array($u->role, ['partnership_officer'])) {
                abort(403, 'غير مصرح لك باستعراض وإدارة زوار المعرض.');
            }
        }

        $query = JobFairVisitor::where('job_fair_id', $fair->id)->latest();

        // بحث بالاسم أو الهاتف أو البريد أو رقم التذكرة أو التخصص
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // فلترة بفئة الزائر
        if ($type = $request->query('type')) {
            $query->where('visitor_type', $type);
        }

        // فلترة بالحضور
        if ($attended = $request->query('attended')) {
            if ($attended === 'yes') {
                $query->where('attended', true);
            } elseif ($attended === 'no') {
                $query->where('attended', false);
            }
        }

        // فلترة بالمدينة
        if ($city = $request->query('city')) {
            $query->where('city', $city);
        }

        $visitors = $query->paginate(20)->withQueryString();

        // إحصائيات عامة وتفصيلية
        $allVisitors = JobFairVisitor::where('job_fair_id', $fair->id)->get();
        $stats = [
            'total' => $allVisitors->count(),
            'attended' => $allVisitors->where('attended', true)->count(),
            'absent' => $allVisitors->where('attended', false)->count(),
            'attendance_rate' => $allVisitors->count() > 0 ? round(($allVisitors->where('attended', true)->count() / $allVisitors->count()) * 100, 1) : 0,
            'students' => $allVisitors->where('visitor_type', 'student')->count(),
            'job_seekers' => $allVisitors->where('visitor_type', 'job_seeker')->count(),
            'company_reps' => $allVisitors->where('visitor_type', 'company_rep')->count(),
            'academics' => $allVisitors->where('visitor_type', 'academic')->count(),
            'parents' => $allVisitors->where('visitor_type', 'parent')->count(),
            'general' => $allVisitors->where('visitor_type', 'general')->count(),
        ];

        // توزيع الفئات للمخططات
        $typeDistribution = [
            'طلاب جامعيين' => $stats['students'],
            'باحثين عن عمل' => $stats['job_seekers'],
            'ممثلي شركات' => $stats['company_reps'],
            'أكاديميين' => $stats['academics'],
            'أولياء أمور' => $stats['parents'],
            'زوار عامين' => $stats['general'],
        ];

        // توزيع المدن
        $cities = $allVisitors->pluck('city')->filter()->unique()->values();

        return view('job-fair.admin.visitors', compact('fair', 'visitors', 'stats', 'typeDistribution', 'cities'));
    }

    /**
     * تسجيل / إلغاء حضور الزائر عند البوابة
     */
    public function toggleCheckIn(Request $request, JobFairVisitor $visitor)
    {
        if (auth()->check()) {
            $u = auth()->user();
            if (!$u->isAdmin() && !$u->hasAnyPermission(['job_fair.visitors', 'job_fair.attendance', 'job_fair.manage']) && !in_array($u->role, ['partnership_officer'])) {
                return response()->json(['error' => 'غير مصرح لك بتسجيل حضور الزوار.'], 403);
            }
        }

        $newAttended = !$visitor->attended;
        $visitor->update([
            'attended' => $newAttended,
            'check_in_at' => $newAttended ? now() : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'attended' => $newAttended,
                'check_in_at' => $visitor->check_in_at ? $visitor->check_in_at->format('Y-m-d H:i') : null,
                'message' => $newAttended ? 'تم تسجيل حضور الزائر بنجاح.' : 'تم إلغاء تسجيل حضور الزائر.'
            ]);
        }

        return back()->with('success', $newAttended ? "تم تسجيل حضور الزائر «{$visitor->name}» بنجاح." : "تم إلغاء حضور الزائر «{$visitor->name}».");
    }

    /**
     * تصدير بيانات الزوار إلى ملف CSV / Excel
     */
    public function export(JobFair $fair)
    {
        if (auth()->check()) {
            $u = auth()->user();
            if (!$u->isAdmin() && !$u->hasAnyPermission(['job_fair.visitors', 'job_fair.registrations', 'job_fair.view']) && !in_array($u->role, ['partnership_officer'])) {
                abort(403, 'غير مصرح لك بتصدير بيانات الزوار.');
            }
        }

        $visitors = JobFairVisitor::where('job_fair_id', $fair->id)->latest()->get();
        $fileName = 'visitors_' . Str::slug($fair->title) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');
            // إضافة BOM للتعامل الصحيح مع اللغة العربية في Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                '#',
                'رقم التذكرة',
                'الاسم الكامل',
                'رقم الهاتف',
                'البريد الإلكتروني',
                'فئة الزائر',
                'المؤهل العلمي',
                'التخصص',
                'جهة العمل / الجامعة',
                'المدينة',
                'هدف الزيارة',
                'الحالة',
                'وقت الحضور',
                'تاريخ التسجيل'
            ]);

            foreach ($visitors as $index => $v) {
                fputcsv($file, [
                    $index + 1,
                    $v->ticket_number,
                    $v->name,
                    $v->phone,
                    $v->email ?: '—',
                    $v->visitor_type_label,
                    $v->education_level_label,
                    $v->specialization ?: '—',
                    $v->organization ?: '—',
                    $v->city ?: '—',
                    $v->visit_purpose_label,
                    $v->attended ? 'حضر' : 'غائب',
                    $v->check_in_at ? $v->check_in_at->format('Y-m-d H:i') : '—',
                    $v->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * حذف سجل زائر
     */
    public function destroy(JobFairVisitor $visitor)
    {
        if (auth()->check()) {
            $u = auth()->user();
            if (!$u->isAdmin() && !$u->hasAnyPermission(['job_fair.visitors', 'job_fair.delete']) && !in_array($u->role, ['partnership_officer'])) {
                abort(403, 'غير مصرح لك بحذف سجلات الزوار.');
            }
        }

        $name = $visitor->name;
        $visitor->delete();

        return back()->with('success', "تم حذف سجل الزائر «{$name}» بنجاح.");
    }
}
