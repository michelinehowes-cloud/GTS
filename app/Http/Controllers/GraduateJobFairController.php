<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairWishlist;
use App\Models\JobFairEvent;
use App\Models\JobFairEventAttendee;
use Illuminate\Http\Request;

class GraduateJobFairController extends Controller
{
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
        $specialization = $graduate->graduateProfile->university_specialization ?? '';
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
            return back()->with('success', 'تم التسجيل في الفعالية بنجاح.');
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

        // Check if visit already exists
        $visit = \App\Models\JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $company->id)
            ->where('graduate_id', $graduate->id)
            ->first();

        $already_visited = false;

        if ($visit) {
            $already_visited = true;
        } else {
            \App\Models\JobFairVisit::create([
                'job_fair_id' => $fair->id,
                'company_id'  => $company->id,
                'graduate_id' => $graduate->id,
            ]);
        }

        return view('graduate.job-fairs.scan-success', compact('fair', 'company', 'already_visited'));
    }
}
