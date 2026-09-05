<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingMedia;
use App\Models\MediaCamera;
use App\Models\LiveBroadcastSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $totalMedia = TrainingMedia::count();
        $activeNews = \App\Models\News::active()->count();
        $activeAnnouncements = \App\Models\Announcement::active()->count();
        $totalTrainings = Training::count();
        $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();
        $coverageRate = $totalTrainings > 0 ? round(($coveredTrainings / $totalTrainings) * 100) : 0;

        // إعدادات البث والكاميرات
        $broadcastSetting = LiveBroadcastSetting::current();
        $camerasCount = MediaCamera::count();
        $liveCamerasCount = MediaCamera::where('is_live', true)->count();
        $activeCamera = $broadcastSetting->activeCamera ?? MediaCamera::first();

        // التدريبات القادمة ومتابعة التغطية
        $upcomingTrainings = Training::where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(6)
            ->get();

        // الأخبار والإعلانات الأخيرة
        $recentNews = \App\Models\News::orderBy('published_at', 'desc')->take(4)->get();
        $recentAnnouncements = \App\Models\Announcement::orderBy('created_at', 'desc')->take(4)->get();

        // الكاميرات المتوفرة
        $cameras = MediaCamera::orderBy('display_order')->take(4)->get();

        return view('media.dashboard', compact(
            'totalMedia',
            'activeNews',
            'activeAnnouncements',
            'totalTrainings',
            'coveredTrainings',
            'coverageRate',
            'broadcastSetting',
            'camerasCount',
            'liveCamerasCount',
            'activeCamera',
            'upcomingTrainings',
            'recentNews',
            'recentAnnouncements',
            'cameras'
        ));
    }

    /**
     * عرض فهرس التدريبات
     */
    public function trainingsIndex(Request $request)
    {
        $query = Training::with(['company', 'coordinator', 'media']);

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
     * عرض تفاصيل تدريب مع وسائطه
     */
    public function trainingShow(Training $training)
    {
        $training->load(['company', 'coordinator', 'media.uploader']);
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
     * معرض الوسائط
     */
    public function mediaGallery(Request $request)
    {
        $query = TrainingMedia::with(['training', 'uploader']);

        // التصفية
        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('is_welcome_page_media')) {
            $query->where('is_welcome_page_media', $request->boolean('is_welcome_page_media'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(20);
        $trainings = Training::orderBy('title')->get();

        return view('media.gallery', compact('media', 'trainings'));
    }

    /**
     * نموذج رفع الوسائط
     */
    public function uploadForm()
    {
        $trainings = Training::orderBy('title')->get();
        return view('media.upload', compact('trainings'));
    }

    /**
     * رفع وسائط جديدة
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:51200', // 50MB max
            'training_id' => 'nullable|exists:trainings,id',
            'is_welcome_page_media' => 'boolean',
            'caption' => 'nullable|string|max:255'
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileType = $this->getFileType($file);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('media', $fileName, 'public');

                $media = TrainingMedia::create([
                    'training_id' => $request->training_id,
                    'file_path' => $path,
                    'file_type' => $fileType,
                    'caption' => $request->caption,
                    'uploaded_by' => Auth::id(),
                    'is_welcome_page_media' => $request->boolean('is_welcome_page_media'),
                    'display_order' => $request->is_welcome_page_media ? TrainingMedia::where('is_welcome_page_media', true)->max('display_order') + 1 : null,
                    'is_active' => true
                ]);

                $uploadedFiles[] = $media;
            }
        }

        $message = count($uploadedFiles) > 1 ? 'تم رفع ' . count($uploadedFiles) . ' ملف بنجاح' : 'تم رفع الملف بنجاح';

        return redirect()->back()->with('success', $message);
    }

    /**
     * عرض بيانات وسيط واحد (JSON)
     */
    public function show(TrainingMedia $media)
    {
        return response()->json($media);
    }

    /**
     * تحديث وصف الوسيط
     */
    public function update(Request $request, TrainingMedia $media)
    {
        $request->validate([
            'caption' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:1'
        ]);

        $media->update($request->only(['caption', 'is_active', 'display_order']));

        return redirect()->back()->with('success', 'تم تحديث الوسيط بنجاح');
    }

    /**
     * حذف وسيط
     */
    public function destroy(TrainingMedia $media)
    {
        // حذف الملف من التخزين
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return redirect()->back()->with('success', 'تم حذف الوسيط بنجاح');
    }

    /**
     * تحديد نوع الملف
     */
    private function getFileType($file)
    {
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image/')) {
            return 'image';
        } elseif (str_contains($mime, 'video/')) {
            return 'video';
        }

        return 'file';
    }

    /**
     * إنشاء تقرير تغطية
     */
    public function createCoverageReport(Training $training)
    {
        $training->load(['media', 'company']);

        // جمع الوسائط النشطة
        $media = $training->media()->active()->get();

        return view('media.reports.coverage', compact('training', 'media'));
    }

    /**
     * عرض تقويم التدريبات
     */
    public function trainingCalendarIndex(Request $request)
    {
        try {
            $month = $request->input('month', \Carbon\Carbon::now()->month);
            $year = $request->input('year', \Carbon\Carbon::now()->year);

            $month = max(1, min(12, $month));
            $year = max(2020, min(2030, $year));

            $startDate = \Carbon\Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $trainings = Training::where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
                ->get();

            $calendar = $this->generateCalendar($month, $year, $trainings);

            // استخدام نفس العرض الخاص بالتقييم والمتابعة لأنه عام
            return view('evaluation-followup.training-calendar.index', compact('calendar', 'trainings', 'month', 'year', 'startDate'));

        } catch (\Exception $e) {
            return redirect()->route('media.dashboard')
                ->with('error', 'حدث خطأ في تحميل التقويم: ' . $e->getMessage());
        }
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

    // ==================== 🎥 غرفة تحكم البث المباشر والكاميرات الذكية ====================

    /**
     * شاشة غرفة تحكم البث المباشر (Live Control Room)
     */
    public function liveStudio()
    {
        $setting = LiveBroadcastSetting::current();
        $cameras = MediaCamera::orderBy('display_order')->get();
        $activeCamera = $setting->activeCamera ?? $cameras->first();
        $upcomingTrainings = Training::where('start_date', '>=', now()->toDateString())->orderBy('start_date')->take(5)->get();

        return view('media.live-studio', compact('setting', 'cameras', 'activeCamera', 'upcomingTrainings'));
    }

    /**
     * إضافة كاميرا جديدة أو رابط بث خارجي
     */
    public function storeCamera(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location_tag' => 'nullable|string|max:255',
            'stream_type' => 'required|in:youtube_live,hls_m3u8,rtsp_ip,iframe_embed,zoom_meet,external_url',
            'stream_url' => 'required|string',
            'description' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
        ]);

        $order = (MediaCamera::max('display_order') ?? 0) + 1;

        $camera = MediaCamera::create([
            'title' => $request->title,
            'location_tag' => $request->location_tag,
            'stream_type' => $request->stream_type,
            'stream_url' => $request->stream_url,
            'is_live' => true,
            'is_primary' => $request->boolean('is_primary'),
            'display_order' => $order,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        if ($camera->is_primary || MediaCamera::count() === 1) {
            MediaCamera::where('id', '!=', $camera->id)->update(['is_primary' => false]);
            $camera->update(['is_primary' => true]);
            LiveBroadcastSetting::current()->update(['active_camera_id' => $camera->id]);
        }

        return redirect()->back()->with('success', 'تمت إضافة الكاميرا / رابط البث بنجاح.');
    }

    /**
     * تحويل الكاميرا النشطة للبث على الهواء (On Air Switch)
     */
    public function setCameraOnAir(MediaCamera $camera)
    {
        MediaCamera::where('id', '!=', $camera->id)->update(['is_primary' => false]);
        $camera->update(['is_primary' => true, 'is_live' => true]);

        $setting = LiveBroadcastSetting::current();
        $setting->update([
            'active_camera_id' => $camera->id,
            'is_live_now' => true
        ]);

        return redirect()->back()->with('success', "تم تحويل البث المباشر على الهواء بنجاح إلى: {$camera->title} 🔴");
    }

    /**
     * تبديل حالة تشغيل الكاميرا
     */
    public function toggleCameraLive(MediaCamera $camera)
    {
        $camera->update(['is_live' => !$camera->is_live]);
        $status = $camera->is_live ? 'مفعلة' : 'متوقفة';
        return redirect()->back()->with('success', "تم تغيير حالة الكاميرا إلى: {$status}");
    }

    /**
     * حذف كاميرا
     */
    public function deleteCamera(MediaCamera $camera)
    {
        $setting = LiveBroadcastSetting::current();
        if ($setting->active_camera_id === $camera->id) {
            $other = MediaCamera::where('id', '!=', $camera->id)->first();
            $setting->update(['active_camera_id' => $other ? $other->id : null]);
        }

        $camera->delete();
        return redirect()->back()->with('success', 'تم حذف الكاميرا بنجاح.');
    }

    /**
     * تبديل حالة البث المباشر العام (Go Live / Stop Live)
     */
    public function toggleBroadcast(Request $request)
    {
        $setting = LiveBroadcastSetting::current();
        $setting->update([
            'is_live_now' => !$setting->is_live_now
        ]);

        $msg = $setting->is_live_now ? 'تم إطلاق البث المباشر للجمهور بنجاح (ON AIR 🔴)' : 'تم إيقاف البث المباشر عن الجمهور (OFF AIR ⚪)';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * تحديث بيانات وعنوان البث المباشر
     */
    public function updateBroadcast(Request $request)
    {
        $request->validate([
            'broadcast_title' => 'required|string|max:255',
            'broadcast_description' => 'nullable|string',
            'viewers_count' => 'nullable|integer|min:0',
        ]);

        $setting = LiveBroadcastSetting::current();
        $setting->update([
            'broadcast_title' => $request->broadcast_title,
            'broadcast_description' => $request->broadcast_description,
            'viewers_count' => $request->viewers_count ?? $setting->viewers_count,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات البث المباشر بنجاح.');
    }

    // ==================== 📅 تقويم وجدول التغطيات الإعلامية ====================

    /**
     * جدول وتقويم التغطيات الإعلامية
     */
    public function coverageCalendar(Request $request)
    {
        $trainings = Training::with(['company', 'trainer'])
            ->orderBy('start_date', 'asc')
            ->get();

        $stats = [
            'total' => $trainings->count(),
            'covered' => $trainings->where('media_coverage_status', 'covered')->count(),
            'pending' => $trainings->where('media_coverage_status', 'pending')->count(),
            'not_required' => $trainings->where('media_coverage_status', 'not_required')->count(),
        ];

        return view('media.coverage-calendar', compact('trainings', 'stats'));
    }

    /**
     * تحديث حالة وملاحظات التغطية لبرنامج تدريبي
     */
    public function updateCoverageTask(Request $request, Training $training)
    {
        $request->validate([
            'media_coverage_status' => 'required|in:pending,covered,not_required',
        ]);

        $training->update([
            'media_coverage_status' => $request->media_coverage_status,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة التغطية الإعلامية بنجاح.');
    }
}

