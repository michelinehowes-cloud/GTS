<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingMedia;
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
        $coveredTrainings = Training::where('media_coverage_status', 'covered')->count();

        // التدريبات القادمة
        $upcomingTrainings = Training::where('start_date', '>', now())
            ->orderBy('start_date')
            ->get();

        // الأخبار والإعلانات الأخيرة
        $recentNews = \App\Models\News::orderBy('published_at', 'desc')->take(3)->get();
        $recentAnnouncements = \App\Models\Announcement::orderBy('created_at', 'desc')->take(3)->get();

        // الوسائط الأخيرة
        $recentMedia = TrainingMedia::with(['training', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('media.dashboard', compact(
            'totalMedia',
            'activeNews',
            'activeAnnouncements',
            'coveredTrainings',
            'upcomingTrainings',
            'recentNews',
            'recentAnnouncements',
            'recentMedia'
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
}
