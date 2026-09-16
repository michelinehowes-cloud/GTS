<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairEvent;
use App\Models\JobFairEventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobFairEventController extends Controller
{
    public function index(JobFair $fair)
    {
        $events = $fair->events()
            ->withCount('attendees')
            ->orderBy('start_time')
            ->get();

        $stats = [
            'total'       => $events->count(),
            'masterclass' => $events->where('type', 'masterclass')->count(),
            'workshops'   => $events->where('type', 'workshop')->count(),
            'panels'      => $events->where('type', 'panel_discussion')->count(),
            'attendees'   => $events->sum('attendees_count'),
            'capacity'    => $events->sum(fn($e) => $e->capacity ?: 0),
        ];

        return view('job-fair.admin.events', compact('fair', 'events', 'stats'));
    }

    public function store(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|string|in:masterclass,workshop,panel_discussion,keynote',
            'description'     => 'nullable|string',
            'topics'          => 'nullable|string',
            'speaker_name'    => 'nullable|string|max:255',
            'speaker_title'   => 'nullable|string|max:255',
            'speaker_bio'     => 'nullable|string',
            'speaker_image'   => 'nullable|image|max:2048',
            'start_time'      => 'required|date',
            'end_time'        => 'required|date|after:start_time',
            'location'        => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'capacity'        => 'nullable|integer|min:1',
            'status'          => 'nullable|string|in:open,upcoming,completed,ended',
            'is_featured'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('speaker_image')) {
            $validated['speaker_image'] = $request->file('speaker_image')->store('events/speakers', 'public');
        }

        $validated['status'] = $validated['status'] ?? 'open';
        $validated['is_featured'] = $request->has('is_featured');

        $fair->events()->create($validated);

        return back()->with('success', 'تمت إضافة الفعالية العلمية بنجاح.');
    }

    public function update(Request $request, JobFairEvent $event)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|string|in:masterclass,workshop,panel_discussion,keynote',
            'description'     => 'nullable|string',
            'topics'          => 'nullable|string',
            'speaker_name'    => 'nullable|string|max:255',
            'speaker_title'   => 'nullable|string|max:255',
            'speaker_bio'     => 'nullable|string',
            'speaker_image'   => 'nullable|image|max:2048',
            'start_time'      => 'required|date',
            'end_time'        => 'required|date|after:start_time',
            'location'        => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'capacity'        => 'nullable|integer|min:1',
            'status'          => 'required|string|in:open,upcoming,completed,ended',
            'is_featured'     => 'nullable|boolean',
        ]);

        if ($request->hasFile('speaker_image')) {
            if ($event->speaker_image && Storage::disk('public')->exists($event->speaker_image)) {
                Storage::disk('public')->delete($event->speaker_image);
            }
            $validated['speaker_image'] = $request->file('speaker_image')->store('events/speakers', 'public');
        }

        $validated['is_featured'] = $request->has('is_featured');

        $event->update($validated);

        return back()->with('success', 'تم تحديث بيانات الفعالية العلمية بنجاح.');
    }

    public function destroy(JobFairEvent $event)
    {
        if ($event->speaker_image && Storage::disk('public')->exists($event->speaker_image)) {
            Storage::disk('public')->delete($event->speaker_image);
        }

        $event->attendees()->delete();
        $event->delete();

        return back()->with('success', 'تم حذف الفعالية وكافة تسجيلاتها بنجاح.');
    }

    public function attendees(JobFairEvent $event)
    {
        $attendees = $event->attendees()
            ->with(['graduate.graduateData'])
            ->latest()
            ->get();

        return response()->json([
            'event'     => $event,
            'attendees' => $attendees,
            'count'     => $attendees->count(),
        ]);
    }

    public function exportAttendees(JobFairEvent $event): StreamedResponse
    {
        $event->load(['attendees.graduate.graduateData', 'jobFair']);
        $attendees = $event->attendees;

        $fileName = 'attendees_' . $event->id . '_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($attendees, $event) {
            $handle = fopen('php://output', 'w');
            
            // Output UTF-8 BOM for Arabic Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                '#',
                'اسم الفعالية',
                'نوع الفعالية',
                'اسم الخريج / المشارك',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'الكلية',
                'التخصص',
                'سنة التخرج',
                'حالة الحساب',
                'تاريخ ووقت التسجيل',
            ]);

            $index = 1;
            foreach ($attendees as $att) {
                $user = $att->graduate;
                $gData = $user ? $user->graduateData : null;

                fputcsv($handle, [
                    $index++,
                    $event->title,
                    $event->type_label,
                    $user->name ?? 'غير معروف',
                    $user->email ?? '—',
                    $user->phone ?? ($gData->phone ?? '—'),
                    $user->faculty ?? ($gData->faculty ?? '—'),
                    $user->major ?? ($gData->specialization ?? '—'),
                    $user->graduation_year ?? ($gData->graduation_year ?? '—'),
                    $user && $user->is_active ? 'نشط' : 'مجمد',
                    $att->created_at ? $att->created_at->format('Y-m-d H:i') : '—',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
