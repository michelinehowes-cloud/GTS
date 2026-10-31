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
        if (!auth()->user()->canManageJobFairEvents()) {
            abort(403, 'غير مصرح لك بإضافة الفعاليات وورش العمل.');
        }

        $validated = $request->validate($this->validationRules());

        $speakerPayload = $this->processSpeakersPayload($request, null);
        $data = array_merge($validated, $speakerPayload);
        unset($data['speaker_images']);

        $data['status'] = $data['status'] ?? 'open';
        $data['is_featured'] = $request->has('is_featured');

        $fair->events()->create($data);

        return back()->with('success', 'تمت إضافة الفعالية العلمية بنجاح.');
    }

    public function update(Request $request, JobFairEvent $event)
    {
        if (!auth()->user()->canManageJobFairEvents()) {
            abort(403, 'غير مصرح لك بتعديل الفعاليات وورش العمل.');
        }

        $validated = $request->validate($this->validationRules());

        $speakerPayload = $this->processSpeakersPayload($request, $event);
        $data = array_merge($validated, $speakerPayload);
        unset($data['speaker_images']);

        $data['is_featured'] = $request->has('is_featured');

        $event->update($data);

        return back()->with('success', 'تم تحديث بيانات الفعالية العلمية بنجاح.');
    }

    public function destroy(JobFairEvent $event)
    {
        if (!auth()->user()->canManageJobFairEvents()) {
            abort(403, 'غير مصرح لك بحذف الفعاليات وورش العمل.');
        }

        foreach ($this->collectOwnedSpeakerImages($event) as $imgPath) {
            if ($imgPath && !str_starts_with($imgPath, 'http') && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }
        }

        $event->attendees()->delete();
        $event->delete();

        return back()->with('success', 'تم حذف الفعالية وكافة تسجيلاتها بنجاح.');
    }

    protected function validationRules(): array
    {
        return [
            'title'                     => 'required|string|max:255',
            'type'                      => 'required|string|in:masterclass,workshop,panel_discussion,keynote',
            'description'               => 'nullable|string',
            'topics'                    => 'nullable|string',
            'speaker_name'              => 'nullable|string|max:255',
            'speaker_title'             => 'nullable|string|max:255',
            'speaker_bio'               => 'nullable|string',
            'speaker_image'             => 'nullable|image|max:4096',
            'speakers'                  => 'nullable|array|max:10',
            'speakers.*.name'           => 'nullable|string|max:255',
            'speakers.*.title'          => 'nullable|string|max:255',
            'speakers.*.role'           => 'nullable|string|max:100',
            'speakers.*.bio'            => 'nullable|string|max:2000',
            'speakers.*.existing_image' => 'nullable|string|max:500',
            'speakers.*.remove_image'   => 'nullable|boolean',
            'speakers.*.image'          => 'nullable|image|max:4096',
            'speaker_images'            => 'nullable|array|max:10',
            'speaker_images.*'          => 'nullable|image|max:4096',
            'start_time'                => 'required|date',
            'end_time'                  => 'required|date|after:start_time',
            'location'                  => 'nullable|string|max:255',
            'target_audience'           => 'nullable|string|max:255',
            'capacity'                  => 'nullable|integer|min:1',
            'status'                    => 'nullable|string|in:open,upcoming,completed,ended',
            'is_featured'               => 'nullable|boolean',
        ];
    }

    /**
     * استخراج الصور المملوكة فعلياً لهذه الفعالية لمنع حقن مسارات خارجية وتنظيف الصور المستبدلة
     */
    protected function collectOwnedSpeakerImages(?JobFairEvent $event): array
    {
        if (!$event) {
            return [];
        }

        $paths = [];
        if (!empty($event->speaker_image)) {
            $paths[] = trim((string) $event->speaker_image);
        }

        $raw = $event->speakers;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (is_array($raw)) {
            foreach ($raw as $entry) {
                if (is_array($entry) && !empty($entry['image'])) {
                    $paths[] = trim((string) $entry['image']);
                }
            }
        }

        return array_values(array_unique(array_filter($paths)));
    }

    /**
     * معالجة قائمة المتحدثين والمحاورين ومزامنة المتحدث الأول مع الحقول القديمة للتوافق الكامل
     */
    protected function processSpeakersPayload(Request $request, ?JobFairEvent $existingEvent = null): array
    {
        $ownedImages = $this->collectOwnedSpeakerImages($existingEvent);
        $defaultRole = JobFairEvent::defaultSpeakerRoleForType($request->input('type'));
        $speakers = [];

        if ($request->has('speakers') && is_array($request->input('speakers'))) {
            foreach ($request->input('speakers') as $idx => $row) {
                if (!is_array($row)) {
                    continue;
                }

                $name        = trim((string) ($row['name'] ?? ''));
                $title       = trim((string) ($row['title'] ?? ''));
                $role        = trim((string) ($row['role'] ?? ''));
                $bio         = trim((string) ($row['bio'] ?? ''));
                $removeImage = !empty($row['remove_image']);
                $existingImg = trim((string) ($row['existing_image'] ?? ''));

                if ($existingImg !== '' && !in_array($existingImg, $ownedImages, true)) {
                    $existingImg = '';
                }

                $uploadedFile = $request->file("speakers.$idx.image") ?? $request->file("speaker_images.$idx");
                if ($uploadedFile) {
                    $imagePath = $uploadedFile->store('events/speakers', 'public');
                } elseif ($removeImage) {
                    $imagePath = null;
                } else {
                    $imagePath = $existingImg !== '' ? $existingImg : null;
                }

                if ($name === '' && $title === '' && $bio === '' && !$imagePath) {
                    continue;
                }

                $speakers[] = [
                    'name'  => $name,
                    'title' => $title,
                    'role'  => $role !== '' ? $role : $defaultRole,
                    'bio'   => $bio,
                    'image' => $imagePath,
                ];
            }
        } else {
            // التوافق مع الطلبات التقليدية التي ترسل speaker_name / speaker_image فقط
            $legacyName  = trim((string) ($request->input('speaker_name') ?? ''));
            $legacyTitle = trim((string) ($request->input('speaker_title') ?? ''));
            $legacyBio   = trim((string) ($request->input('speaker_bio') ?? ''));
            $legacyImage = $existingEvent?->speaker_image;

            if ($request->hasFile('speaker_image')) {
                $legacyImage = $request->file('speaker_image')->store('events/speakers', 'public');
            }

            if ($legacyName !== '' || $legacyTitle !== '' || $legacyBio !== '' || $legacyImage) {
                $speakers[] = [
                    'name'  => $legacyName,
                    'title' => $legacyTitle,
                    'role'  => $defaultRole,
                    'bio'   => $legacyBio,
                    'image' => $legacyImage ?: null,
                ];
            }
        }

        // حذف الصور القديمة التي لم تعد مستخدمة من التخزين
        $keptImages = array_values(array_filter(array_map(fn($s) => $s['image'] ?? null, $speakers)));
        foreach ($ownedImages as $oldPath) {
            if (!in_array($oldPath, $keptImages, true) && !str_starts_with($oldPath, 'http')) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $primary = $speakers[0] ?? null;

        return [
            'speakers'      => $speakers,
            'speaker_name'  => ($primary && $primary['name'] !== '') ? $primary['name'] : null,
            'speaker_title' => ($primary && $primary['title'] !== '') ? $primary['title'] : null,
            'speaker_bio'   => ($primary && $primary['bio'] !== '') ? $primary['bio'] : null,
            'speaker_image' => $primary['image'] ?? null,
        ];
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
