<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairWishlist;
use App\Models\JobFairEvent;
use App\Models\JobFairEventAttendee;
use App\Models\JobFairRegistration;
use Illuminate\Http\Request;

class GraduateJobFairController extends Controller
{
    /**
     * عرض صفحة الفعاليات والمعارض للخريج
     */
    public function index(Request $request)
    {
        $graduate = auth()->user();

        // 1. جلب المعارض المنشورة أو الجارية
        $fairs = JobFair::whereIn('status', ['published', 'ongoing'])
            ->with(['companies.company', 'events', 'sponsors'])
            ->withCount(['companies', 'events', 'projects'])
            ->orderBy('event_date', 'asc')
            ->get();

        if ($fairs->isEmpty()) {
            $fairs = JobFair::with(['companies.company', 'events', 'sponsors'])
                ->withCount(['companies', 'events', 'projects'])
                ->latest()
                ->take(3)
                ->get();
        }

        // 2. تسجيلات الخريج في المعارض
        $myRegistrations = JobFairRegistration::where('user_id', $graduate->id)
            ->with('jobFair')
            ->get()
            ->keyBy('job_fair_id');

        // 3. معرفات الفعاليات المسجل بها الخريج
        $registeredEventIds = JobFairEventAttendee::where('graduate_id', $graduate->id)
            ->pluck('job_fair_event_id')
            ->toArray();

        // 4. الفعاليات المصاحبة المتاحة
        $fairIds = $fairs->pluck('id')->toArray();
        $events = JobFairEvent::whereIn('job_fair_id', $fairIds)
            ->with('jobFair')
            ->withCount('attendees')
            ->orderBy('start_time', 'asc')
            ->get();

        // 5. الفعاليات التي سجل فيها الخريج
        $myEvents = JobFairEvent::whereIn('id', $registeredEventIds)
            ->with('jobFair')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('graduate.job-fairs.index', compact(
            'fairs',
            'myRegistrations',
            'registeredEventIds',
            'events',
            'myEvents'
        ));
    }

    /**
     * View the interactive digital catalog for a specific job fair
     */
    public function catalog(JobFair $fair)
    {
        $graduate = auth()->user();
        
        // Ensure user is registered for this fair
        $isRegistered = $fair->registrations()->where('user_id', $graduate->id)->exists();
        if (!$isRegistered) {
            return redirect()->route('job-fair.public')->with('error', 'يجب التسجيل في المعرض أولاً.');
        }

        $companies = $fair->companies()->with('company')->get();
        $events = $fair->events()->orderBy('start_time')->get();

        // User's wishlisted company IDs
        $wishlistedIds = JobFairWishlist::where('graduate_id', $graduate->id)
            ->pluck('job_fair_company_id')->toArray();

        // User's registered event IDs
        $registeredEventIds = JobFairEventAttendee::where('graduate_id', $graduate->id)
            ->pluck('job_fair_event_id')->toArray();

        // AI Matchmaking logic (basic version)
        // Compare graduate's specialization with company's participating_sectors or available_positions
        $specialization = $graduate->graduateData?->specialization ?? $graduate->specialization ?? $graduate->major ?? '';
        $recommendedCompanies = collect();

        if ($specialization) {
            foreach ($companies as $c) {
                $sectors = strtolower($c->participating_sectors ?? '');
                $positions = strtolower($c->available_positions ?? '');
                $specMatch = strtolower($specialization);

                if (str_contains($sectors, $specMatch) || str_contains($positions, $specMatch)) {
                    $recommendedCompanies->push($c);
                }
            }
        }

        return view('graduate.job-fairs.catalog', compact(
            'fair', 'companies', 'events', 'wishlistedIds', 'registeredEventIds', 'recommendedCompanies'
        ));
    }

    /**
     * Toggle a company in the graduate's wishlist
     */
    public function toggleWishlist(JobFairCompany $company)
    {
        $graduate = auth()->user();

        $wishlist = JobFairWishlist::where('graduate_id', $graduate->id)
            ->where('job_fair_company_id', $company->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed', 'message' => 'تم الإزالة من المفضلة']);
        } else {
            JobFairWishlist::create([
                'graduate_id' => $graduate->id,
                'job_fair_company_id' => $company->id
            ]);
            return response()->json(['status' => 'added', 'message' => 'تمت الإضافة إلى المفضلة']);
        }
    }

    /**
     * Register or unregister from an event
     */
    public function toggleEventRegistration(JobFairEvent $event)
    {
        $graduate = auth()->user();

        $registration = JobFairEventAttendee::where('graduate_id', $graduate->id)
            ->where('job_fair_event_id', $event->id)
            ->first();

        if ($registration) {
            $registration->delete();
            return back()->with('success', 'تم إلغاء التسجيل في الفعالية بنجاح.');
        } else {
            // Check capacity
            $currentAttendees = JobFairEventAttendee::where('job_fair_event_id', $event->id)->count();
            if ($event->capacity && $currentAttendees >= $event->capacity) {
                return back()->with('error', 'عذراً، الفعالية مكتملة العدد.');
            }

            JobFairEventAttendee::create([
                'job_fair_event_id' => $event->id,
                'graduate_id' => $graduate->id,
                'status' => 'registered'
            ]);

            try {
                app(\App\Services\NotificationService::class)->notifyJobFairEventRegistration($graduate, $event);
            } catch (\Throwable $e) {
                \Log::warning('Failed sending workshop registration notification: ' . $e->getMessage());
            }

            return back()->with('success', 'تم التسجيل في الفعالية بنجاح وإرسال تفاصيل الموعد والتوقيت إلى بريدك الإلكتروني.');
        }
    }

    /**
     * Scan Company QR Code
     */
    public function scanCompanyQr(JobFair $fair, \App\Models\Company $company)
    {
        $graduate = auth()->user();
        
        // Ensure user is registered for this fair
        $isRegistered = $fair->registrations()->where('user_id', $graduate->id)->exists();
        if (!$isRegistered) {
            return redirect()->route('job-fair.public')->with('error', 'يجب التسجيل في المعرض أولاً لتتمكن من تسليم سيرتك الذاتية.');
        }

        // Ensure company is in this fair
        $isCompanyInFair = $fair->companies()->where('company_id', $company->id)->exists();
        if (!$isCompanyInFair) {
            return redirect()->route('graduate.dashboard')->with('error', 'هذه الشركة غير مشاركة في المعرض الحالي.');
        }

        // job_fair_visits.company_id references users(id)
        $companyUserId = $company->user_id;
        if (!$companyUserId && $company->email) {
            $companyUserId = \App\Models\User::where('email', $company->email)->value('id');
        }
        if (!$companyUserId || !\App\Models\User::where('id', $companyUserId)->exists()) {
            return redirect()->route('graduate.dashboard')->with('error', 'تعذر تسجيل زيارة الجناح لعدم اكتمال ربط حساب الشركة بالمنظومة.');
        }

        // Check if visit already exists
        $visit = \App\Models\JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $companyUserId)
            ->where('graduate_id', $graduate->id)
            ->first();

        $already_visited = false;

        if ($visit) {
            $already_visited = true;
        } else {
            \App\Models\JobFairVisit::create([
                'job_fair_id' => $fair->id,
                'company_id'  => $companyUserId,
                'graduate_id' => $graduate->id,
            ]);
        }

        return view('graduate.job-fairs.scan-success', compact('fair', 'company', 'already_visited'));
    }

    /**
     * إلغاء تسجيل الخريج في المعرض ذاتياً
     */
    public function cancelRegistration(JobFair $fair)
    {
        $graduate = auth()->user();

        $registration = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', $graduate->id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'أنت غير مسجل في هذا المعرض.');
        }

        if ($registration->attended) {
            return back()->with('error', 'لا يمكن إلغاء التسجيل نظراً لتسجيل حضورك الفعلي في المعرض مسبقاً.');
        }

        // إلغاء ورش العمل والفعاليات المصاحبة لهذا المعرض
        $eventIds = $fair->events()->pluck('id');
        if ($eventIds->isNotEmpty()) {
            JobFairEventAttendee::whereIn('job_fair_event_id', $eventIds)
                ->where('graduate_id', $graduate->id)
                ->delete();
        }

        \App\Models\AuditLog::logAction(
            'job_fair_registration_cancelled_by_graduate',
            "قام الخريج ({$graduate->name}) بإلغاء تسجيله ذاتياً في المعرض [{$fair->title}]",
            'JobFair',
            $graduate->id,
            $fair->id,
            ['registration_id' => $registration->id]
        );

        $registration->delete();

        return back()->with('success', 'تم إلغاء تسجيلك في المعرض والفعاليات المرتبطة به بنجاح.');
    }
}

